<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'strategy',
        'total_debt_amount',
        'monthly_budget',
        'extra_payment',
        'start_date',
        'projected_end_date',
        'actual_end_date',
        'status',
        'total_interest_saved',
        'months_saved',
        'strategy_config',
        'progress_data',
        'is_active'
    ];

    protected $casts = [
        'total_debt_amount' => 'decimal:2',
        'monthly_budget' => 'decimal:2',
        'extra_payment' => 'decimal:2',
        'total_interest_saved' => 'decimal:2',
        'start_date' => 'date',
        'projected_end_date' => 'date',
        'actual_end_date' => 'date',
        'strategy_config' => 'array',
        'progress_data' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com dívidas
     */
    public function debts(): BelongsToMany
    {
        return $this->belongsToMany(Debt::class, 'debt_payment_plan')
            ->withPivot([
                'initial_balance',
                'priority_order',
                'allocated_extra_payment',
                'added_date',
                'projected_payoff_date',
                'actual_payoff_date',
                'status'
            ])
            ->withTimestamps()
            ->orderBy('debt_payment_plan.priority_order');
    }

    /**
     * Relacionamento com cronogramas de pagamento
     */
    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    /**
     * Scope para planos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope para planos completos
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Calcula o progresso do plano (percentual)
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_debt_amount <= 0) {
            return 100;
        }

        $totalPaid = $this->debts->sum(function ($debt) {
            return $debt->original_amount - $debt->current_balance;
        });

        return round(($totalPaid / $this->total_debt_amount) * 100, 2);
    }

    /**
     * Calcula o valor total restante de todas as dívidas
     */
    public function getTotalRemainingAttribute(): float
    {
        return $this->debts->where('pivot.status', 'active')->sum('current_balance');
    }

    /**
     * Calcula quantos meses restam no plano
     */
    public function getMonthsRemainingAttribute(): int
    {
        if (!$this->projected_end_date) {
            return 0;
        }

        $startDate = $this->start_date > now() ? $this->start_date : now();
        return max(0, $startDate->diffInMonths($this->projected_end_date));
    }

    /**
     * Verifica se o plano está no prazo
     */
    public function getIsOnTrackAttribute(): bool
    {
        if (!$this->projected_end_date) {
            return true;
        }

        $expectedProgress = $this->calculateExpectedProgress();
        $actualProgress = $this->progress_percentage;

        return $actualProgress >= ($expectedProgress - 5); // Margem de 5%
    }

    /**
     * Calcula o progresso esperado até agora
     */
    public function calculateExpectedProgress(): float
    {
        if (!$this->projected_end_date || !$this->start_date) {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->projected_end_date);
        if ($totalDays <= 0) {
            return 100;
        }

        $daysPassed = $this->start_date->diffInDays(now());
        $progress = ($daysPassed / $totalDays) * 100;

        return min(100, max(0, $progress));
    }

    /**
     * Obtém a próxima dívida a ser priorizada
     */
    public function getNextPriorityDebt(): ?Debt
    {
        return $this->debts()
            ->where('debt_payment_plan.status', 'active')
            ->where('current_balance', '>', 0)
            ->orderBy('debt_payment_plan.priority_order')
            ->first();
    }

    /**
     * Calcula economia total de juros
     */
    public function calculateInterestSavings(): array
    {
        $withoutPlan = 0;
        $withPlan = 0;

        foreach ($this->debts as $debt) {
            // Cálculo sem plano (apenas pagamento mínimo)
            $monthsWithoutPlan = $debt->calculateMonthsToPayoff();
            $totalPaymentWithoutPlan = $monthsWithoutPlan * $debt->minimum_payment;
            $withoutPlan += $totalPaymentWithoutPlan;

            // Cálculo com plano será feito pelo service
        }

        return [
            'without_plan' => $withoutPlan,
            'with_plan' => $withPlan,
            'savings' => $withoutPlan - $withPlan,
            'months_saved' => 0 // Calculado pelo service
        ];
    }
}
