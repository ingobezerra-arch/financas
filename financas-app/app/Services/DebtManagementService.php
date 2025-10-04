<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\PaymentPlan;
use App\Models\PaymentSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DebtManagementService
{
    /**
     * Cria um plano de pagamento usando a estratégia especificada
     */
    public function createPaymentPlan(
        User $user,
        array $debtIds,
        string $strategy,
        float $monthlyBudget,
        float $extraPayment = 0,
        array $customPriorities = []
    ): PaymentPlan {
        return DB::transaction(function () use ($user, $debtIds, $strategy, $monthlyBudget, $extraPayment, $customPriorities) {
            // Buscar dívidas
            $debts = $user->debts()->whereIn('id', $debtIds)->active()->get();
            
            if ($debts->isEmpty()) {
                throw new \Exception('Nenhuma dívida ativa encontrada para criar o plano.');
            }

            // Calcular total das dívidas
            $totalDebtAmount = $debts->sum('current_balance');
            
            // Ordenar dívidas conforme estratégia
            $orderedDebts = $this->orderDebtsByStrategy($debts, $strategy, $customPriorities);
            
            // Criar o plano de pagamento
            $paymentPlan = $user->paymentPlans()->create([
                'name' => $this->generatePlanName($strategy),
                'description' => $this->generatePlanDescription($strategy, $debts->count(), $totalDebtAmount),
                'strategy' => $strategy,
                'total_debt_amount' => $totalDebtAmount,
                'monthly_budget' => $monthlyBudget,
                'extra_payment' => $extraPayment,
                'start_date' => now(),
                'status' => 'active',
                'strategy_config' => $this->getStrategyConfig($strategy, $customPriorities)
            ]);

            // Anexar dívidas ao plano
            $this->attachDebtsToPaymentPlan($paymentPlan, $orderedDebts);
            
            // Gerar cronograma de pagamentos
            $this->generatePaymentSchedule($paymentPlan);
            
            return $paymentPlan->fresh(['debts', 'paymentSchedules']);
        });
    }

    /**
     * Ordena dívidas conforme a estratégia escolhida
     */
    protected function orderDebtsByStrategy(Collection $debts, string $strategy, array $customPriorities = []): Collection
    {
        return match($strategy) {
            'snowball' => $this->orderBySnowball($debts),
            'avalanche' => $this->orderByAvalanche($debts),
            'custom' => $this->orderByCustom($debts, $customPriorities),
            default => $debts
        };
    }

    /**
     * Método Bola de Neve: menor saldo primeiro
     */
    protected function orderBySnowball(Collection $debts): Collection
    {
        return $debts->sortBy('current_balance')->values();
    }

    /**
     * Método Avalanche: maior taxa de juros primeiro
     */
    protected function orderByAvalanche(Collection $debts): Collection
    {
        return $debts->sortByDesc('interest_rate')->values();
    }

    /**
     * Método Personalizado: ordem definida pelo usuário
     */
    protected function orderByCustom(Collection $debts, array $customPriorities): Collection
    {
        if (empty($customPriorities)) {
            return $debts;
        }

        return $debts->sortBy(function ($debt) use ($customPriorities) {
            return array_search($debt->id, $customPriorities) !== false 
                ? array_search($debt->id, $customPriorities) 
                : 999;
        })->values();
    }

    /**
     * Anexa dívidas ao plano de pagamento
     */
    protected function attachDebtsToPaymentPlan(PaymentPlan $paymentPlan, Collection $orderedDebts): void
    {
        foreach ($orderedDebts as $index => $debt) {
            $paymentPlan->debts()->attach($debt->id, [
                'initial_balance' => $debt->current_balance,
                'priority_order' => $index + 1,
                'allocated_extra_payment' => 0,
                'added_date' => now(),
                'status' => 'active'
            ]);
        }
    }

    /**
     * Gera cronograma completo de pagamentos
     */
    public function generatePaymentSchedule(PaymentPlan $paymentPlan): void
    {
        // Limpar cronograma existente
        $paymentPlan->paymentSchedules()->delete();

        $debts = $paymentPlan->debts()->orderBy('debt_payment_plan.priority_order')->get();
        $monthlyBudget = $paymentPlan->monthly_budget;
        $extraPayment = $paymentPlan->extra_payment;
        
        $schedules = [];
        $currentMonth = 1;
        $currentDate = $paymentPlan->start_date->copy();
        
        // Calcular pagamentos mínimos totais
        $totalMinimumPayments = $debts->sum('minimum_payment');
        
        if ($monthlyBudget < $totalMinimumPayments) {
            throw new \Exception('Orçamento mensal insuficiente para cobrir pagamentos mínimos.');
        }
        
        $availableExtraAmount = $monthlyBudget - $totalMinimumPayments + $extraPayment;
        
        // Simular pagamentos mês a mês
        $debtBalances = $debts->pluck('current_balance', 'id')->toArray();
        $isCompleted = false;
        
        while (!$isCompleted && $currentMonth <= 360) { // Limite de 30 anos
            $monthSchedules = [];
            $remainingExtraAmount = $availableExtraAmount;
            $paidOffThisMonth = [];
            
            // Primeiro, aplicar pagamentos mínimos
            foreach ($debts as $debt) {
                if ($debtBalances[$debt->id] <= 0) {
                    continue;
                }
                
                $payment = $this->calculatePayment($debt, $debtBalances[$debt->id], $debt->minimum_payment);
                
                $monthSchedules[] = [
                    'payment_plan_id' => $paymentPlan->id,
                    'debt_id' => $debt->id,
                    'month' => $currentMonth,
                    'due_date' => $currentDate->copy(),
                    'payment_amount' => $payment['payment_amount'],
                    'minimum_payment' => $debt->minimum_payment,
                    'extra_payment' => 0,
                    'interest_amount' => $payment['interest_amount'],
                    'principal_amount' => $payment['principal_amount'],
                    'remaining_balance' => $payment['remaining_balance'],
                    'priority_order' => $debt->pivot->priority_order,
                    'is_final_payment' => $payment['remaining_balance'] <= 0.01
                ];
                
                $debtBalances[$debt->id] = $payment['remaining_balance'];
                
                if ($debtBalances[$debt->id] <= 0.01) {
                    $paidOffThisMonth[] = $debt->id;
                }
            }
            
            // Depois, aplicar valores extras conforme estratégia
            foreach ($debts as $debt) {
                if ($debtBalances[$debt->id] <= 0 || $remainingExtraAmount <= 0) {
                    continue;
                }
                
                // Encontrar o cronograma deste mês para esta dívida
                $scheduleIndex = array_search($debt->id, array_column($monthSchedules, 'debt_id'));
                if ($scheduleIndex === false) continue;
                
                // Aplicar extra payment na dívida de maior prioridade
                if ($debt->pivot->priority_order == 1 || $paymentPlan->strategy === 'custom') {
                    $maxExtraForThisDebt = min($remainingExtraAmount, $debtBalances[$debt->id]);
                    
                    if ($maxExtraForThisDebt > 0) {
                        $extraPayment = $this->calculatePayment($debt, $debtBalances[$debt->id], $maxExtraForThisDebt);
                        
                        $monthSchedules[$scheduleIndex]['extra_payment'] = $maxExtraForThisDebt;
                        $monthSchedules[$scheduleIndex]['payment_amount'] += $maxExtraForThisDebt;
                        $monthSchedules[$scheduleIndex]['principal_amount'] += $extraPayment['principal_amount'];
                        $monthSchedules[$scheduleIndex]['remaining_balance'] = $extraPayment['remaining_balance'];
                        $monthSchedules[$scheduleIndex]['is_final_payment'] = $extraPayment['remaining_balance'] <= 0.01;
                        
                        $debtBalances[$debt->id] = $extraPayment['remaining_balance'];
                        $remainingExtraAmount -= $maxExtraForThisDebt;
                        
                        if ($debtBalances[$debt->id] <= 0.01) {
                            $paidOffThisMonth[] = $debt->id;
                        }
                    }
                }
            }
            
            // Adicionar cronogramas do mês
            $schedules = array_merge($schedules, $monthSchedules);
            
            // Verificar se todas as dívidas foram pagas
            $isCompleted = collect($debtBalances)->every(fn($balance) => $balance <= 0.01);
            
            // Próximo mês
            $currentMonth++;
            $currentDate->addMonth();
        }
        
        // Inserir cronogramas no banco
        PaymentSchedule::insert($schedules);
        
        // Atualizar data prevista de fim
        $lastSchedule = collect($schedules)->last();
        if ($lastSchedule) {
            $paymentPlan->update([
                'projected_end_date' => Carbon::parse($lastSchedule['due_date'])
            ]);
        }
    }

    /**
     * Calcula pagamento para uma dívida
     */
    protected function calculatePayment(Debt $debt, float $currentBalance, float $paymentAmount): array
    {
        $monthlyInterestRate = $debt->interest_rate / 100;
        $interestAmount = $currentBalance * $monthlyInterestRate;
        $principalAmount = max(0, $paymentAmount - $interestAmount);
        $remainingBalance = max(0, $currentBalance - $principalAmount);
        
        return [
            'payment_amount' => $paymentAmount,
            'interest_amount' => round($interestAmount, 2),
            'principal_amount' => round($principalAmount, 2),
            'remaining_balance' => round($remainingBalance, 2)
        ];
    }

    /**
     * Compara estratégias de pagamento
     */
    public function compareStrategies(Collection $debts, float $monthlyBudget, float $extraPayment = 0): array
    {
        $strategies = ['snowball', 'avalanche'];
        $results = [];
        
        foreach ($strategies as $strategy) {
            $orderedDebts = $this->orderDebtsByStrategy($debts, $strategy);
            $simulation = $this->simulatePaymentStrategy($orderedDebts, $monthlyBudget, $extraPayment);
            
            $results[$strategy] = [
                'total_interest' => $simulation['total_interest'],
                'total_payments' => $simulation['total_payments'],
                'months_to_payoff' => $simulation['months_to_payoff'],
                'debt_order' => $orderedDebts->pluck('name', 'id')->toArray()
            ];
        }
        
        return $results;
    }

    /**
     * Simula estratégia de pagamento
     */
    public function simulatePaymentStrategy(Collection $debts, float $monthlyBudget, float $extraPayment = 0): array
    {
        $debtBalances = $debts->pluck('current_balance', 'id')->toArray();
        $totalMinimumPayments = $debts->sum('minimum_payment');
        
        if ($monthlyBudget < $totalMinimumPayments) {
            throw new \Exception('Orçamento insuficiente para pagamentos mínimos.');
        }
        
        $availableExtraAmount = $monthlyBudget - $totalMinimumPayments + $extraPayment;
        $totalInterest = 0;
        $totalPayments = 0;
        $months = 0;
        
        while (collect($debtBalances)->some(fn($balance) => $balance > 0.01) && $months < 360) {
            $months++;
            
            // Aplicar pagamentos mínimos
            foreach ($debts as $debt) {
                if ($debtBalances[$debt->id] <= 0) continue;
                
                $payment = $this->calculatePayment($debt, $debtBalances[$debt->id], $debt->minimum_payment);
                $debtBalances[$debt->id] = $payment['remaining_balance'];
                $totalInterest += $payment['interest_amount'];
                $totalPayments += $payment['payment_amount'];
            }
            
            // Aplicar valor extra na primeira dívida não paga
            $remainingExtra = $availableExtraAmount;
            foreach ($debts as $debt) {
                if ($debtBalances[$debt->id] <= 0 || $remainingExtra <= 0) continue;
                
                $extraAmount = min($remainingExtra, $debtBalances[$debt->id]);
                $payment = $this->calculatePayment($debt, $debtBalances[$debt->id], $extraAmount);
                
                $debtBalances[$debt->id] = $payment['remaining_balance'];
                $totalInterest += $payment['interest_amount'];
                $totalPayments += $extraAmount;
                $remainingExtra -= $extraAmount;
                
                break; // Foca apenas na primeira dívida
            }
        }
        
        return [
            'total_interest' => round($totalInterest, 2),
            'total_payments' => round($totalPayments, 2),
            'months_to_payoff' => $months
        ];
    }

    /**
     * Atualiza progresso do plano
     */
    public function updatePlanProgress(PaymentPlan $paymentPlan): void
    {
        $debts = $paymentPlan->debts;
        $totalOriginal = $debts->sum('pivot.initial_balance');
        $totalCurrent = $debts->sum('current_balance');
        $totalPaid = $totalOriginal - $totalCurrent;
        
        $progressData = [
            'total_original' => $totalOriginal,
            'total_current' => $totalCurrent,
            'total_paid' => $totalPaid,
            'progress_percentage' => $totalOriginal > 0 ? ($totalPaid / $totalOriginal) * 100 : 0,
            'last_updated' => now()
        ];
        
        $paymentPlan->update(['progress_data' => $progressData]);
        
        // Verificar se o plano foi concluído
        if ($totalCurrent <= 0.01) {
            $paymentPlan->update([
                'status' => 'completed',
                'actual_end_date' => now()
            ]);
        }
    }

    /**
     * Gera nome do plano baseado na estratégia
     */
    protected function generatePlanName(string $strategy): string
    {
        return match($strategy) {
            'snowball' => 'Plano Bola de Neve - ' . now()->format('M/Y'),
            'avalanche' => 'Plano Avalanche - ' . now()->format('M/Y'),
            'custom' => 'Plano Personalizado - ' . now()->format('M/Y'),
            default => 'Plano de Pagamento - ' . now()->format('M/Y')
        };
    }

    /**
     * Gera descrição do plano
     */
    protected function generatePlanDescription(string $strategy, int $debtCount, float $totalAmount): string
    {
        $strategyName = match($strategy) {
            'snowball' => 'Bola de Neve (menor saldo primeiro)',
            'avalanche' => 'Avalanche (maior juros primeiro)',
            'custom' => 'Personalizada',
            default => 'Padrão'
        };
        
        return "Plano de quitação usando estratégia {$strategyName} para {$debtCount} dívida(s) totalizando R$ " . number_format($totalAmount, 2, ',', '.');
    }

    /**
     * Obtém configuração da estratégia
     */
    protected function getStrategyConfig(string $strategy, array $customPriorities = []): array
    {
        return [
            'strategy' => $strategy,
            'custom_priorities' => $customPriorities,
            'created_at' => now()
        ];
    }

    /**
     * Registra pagamento realizado
     */
    public function recordPayment(PaymentSchedule $schedule, float $amount, Carbon $date = null, string $notes = null): void
    {
        DB::transaction(function () use ($schedule, $amount, $date, $notes) {
            // Marcar cronograma como pago
            $schedule->markAsPaid($amount, $date, $notes);
            
            // Atualizar saldo da dívida
            $debt = $schedule->debt;
            $payment = $this->calculatePayment($debt, $debt->current_balance, $amount);
            
            $debt->update([
                'current_balance' => $payment['remaining_balance']
            ]);
            
            // Se dívida foi quitada
            if ($payment['remaining_balance'] <= 0.01) {
                $debt->update(['status' => 'paid']);
                
                // Atualizar pivot table
                $schedule->paymentPlan->debts()->updateExistingPivot($debt->id, [
                    'status' => 'paid',
                    'actual_payoff_date' => $date ?? now()
                ]);
            }
            
            // Atualizar progresso do plano
            $this->updatePlanProgress($schedule->paymentPlan);
        });
    }
}