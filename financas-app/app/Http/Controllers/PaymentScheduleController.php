<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use App\Models\PaymentPlan;
use App\Services\DebtManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PaymentScheduleController extends Controller
{
    protected DebtManagementService $debtService;

    public function __construct(DebtManagementService $debtService)
    {
        $this->middleware('auth');
        $this->debtService = $debtService;
    }

    /**
     * Exibe cronograma de um plano de pagamento
     */
    public function index(PaymentPlan $paymentPlan, Request $request): View
    {
        $this->authorize('view', $paymentPlan);
        
        $query = $paymentPlan->paymentSchedules()->with('debt');
        
        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('debt_id')) {
            $query->where('debt_id', $request->debt_id);
        }
        
        if ($request->filled('month')) {
            $query->forMonth($request->month);
        }
        
        $schedules = $query->orderBy('due_date')->paginate(20);
        
        // Resumo do cronograma
        $summary = [
            'total_payments' => $paymentPlan->paymentSchedules()->count(),
            'pending_payments' => $paymentPlan->paymentSchedules()->pending()->count(),
            'paid_payments' => $paymentPlan->paymentSchedules()->where('status', 'paid')->count(),
            'overdue_payments' => $paymentPlan->paymentSchedules()->overdue()->count(),
            'total_amount' => $paymentPlan->paymentSchedules()->sum('payment_amount'),
            'paid_amount' => $paymentPlan->paymentSchedules()->where('status', 'paid')->sum('payment_amount'),
            'next_payment_date' => $paymentPlan->paymentSchedules()->pending()->orderBy('due_date')->first()?->due_date
        ];
        
        return view('payment-schedules.index', compact('paymentPlan', 'schedules', 'summary'));
    }

    /**
     * Registra pagamento de um cronograma
     */
    public function recordPayment(Request $request, PaymentSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $schedule->paymentPlan);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->debtService->recordPayment(
                $schedule,
                $validated['amount'],
                Carbon::parse($validated['payment_date']),
                $validated['notes'] ?? null
            );
            
            return back()->with('success', 'Pagamento registrado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao registrar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Marca pagamento como pulado
     */
    public function skipPayment(Request $request, PaymentSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $schedule->paymentPlan);
        
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);
        
        $schedule->update([
            'status' => 'skipped',
            'notes' => $validated['reason'] ?? 'Pagamento pulado pelo usuário'
        ]);
        
        return back()->with('info', 'Pagamento marcado como pulado.');
    }

    /**
     * Reagenda um pagamento
     */
    public function reschedule(Request $request, PaymentSchedule $schedule): RedirectResponse
    {
        $this->authorize('update', $schedule->paymentPlan);
        
        $validated = $request->validate([
            'new_due_date' => 'required|date|after:today',
            'reason' => 'nullable|string|max:500'
        ]);
        
        $schedule->update([
            'due_date' => $validated['new_due_date'],
            'notes' => $validated['reason'] ?? 'Reagendado pelo usuário'
        ]);
        
        return back()->with('success', 'Pagamento reagendado com sucesso!');
    }

    /**
     * Exibe próximos pagamentos do usuário
     */
    public function upcoming(Request $request): View
    {
        $days = $request->get('days', 30);
        
        $upcomingPayments = PaymentSchedule::whereHas('paymentPlan', function($query) {
                $query->where('user_id', auth()->id())->where('status', 'active');
            })
            ->with(['debt', 'paymentPlan'])
            ->upcoming($days)
            ->orderBy('due_date')
            ->get()
            ->groupBy(function($payment) {
                return $payment->due_date->format('Y-m-d');
            });
            
        // Próximos 7 dias
        $weekPayments = PaymentSchedule::whereHas('paymentPlan', function($query) {
                $query->where('user_id', auth()->id())->where('status', 'active');
            })
            ->with(['debt', 'paymentPlan'])
            ->upcoming(7)
            ->orderBy('due_date')
            ->get();
            
        $totalUpcoming = $weekPayments->sum('payment_amount');
        
        return view('payment-schedules.upcoming', compact('upcomingPayments', 'weekPayments', 'totalUpcoming', 'days'));
    }

    /**
     * Exibe pagamentos em atraso
     */
    public function overdue(): View
    {
        $overduePayments = PaymentSchedule::whereHas('paymentPlan', function($query) {
                $query->where('user_id', auth()->id())->where('status', 'active');
            })
            ->with(['debt', 'paymentPlan'])
            ->overdue()
            ->orderBy('due_date')
            ->get();
            
        $totalOverdue = $overduePayments->sum('payment_amount');
        $averageDelay = $overduePayments->avg(function($payment) {
            return $payment->due_date->diffInDays(now());
        });
        
        return view('payment-schedules.overdue', compact('overduePayments', 'totalOverdue', 'averageDelay'));
    }

    /**
     * Marca múltiplos pagamentos como pagos
     */
    public function bulkMarkAsPaid(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:payment_schedules,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $schedules = PaymentSchedule::whereIn('id', $validated['schedule_ids'])
            ->whereHas('paymentPlan', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();
            
        $successCount = 0;
        $errors = [];
        
        foreach ($schedules as $schedule) {
            try {
                $this->debtService->recordPayment(
                    $schedule,
                    $schedule->payment_amount,
                    Carbon::parse($validated['payment_date']),
                    $validated['notes'] ?? null
                );
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Erro no pagamento {$schedule->id}: " . $e->getMessage();
            }
        }
        
        if ($successCount > 0) {
            $message = "{$successCount} pagamento(s) registrado(s) com sucesso!";
            if (!empty($errors)) {
                $message .= ' Alguns erros ocorreram: ' . implode(', ', $errors);
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', 'Nenhum pagamento foi registrado. Erros: ' . implode(', ', $errors));
        }
    }

    /**
     * Gera relatório de pagamentos
     */
    public function report(Request $request, PaymentPlan $paymentPlan): View
    {
        $this->authorize('view', $paymentPlan);
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        $payments = $paymentPlan->paymentSchedules()
            ->with('debt')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->orderBy('due_date')
            ->get();
            
        $stats = [
            'total_scheduled' => $payments->sum('payment_amount'),
            'total_paid' => $payments->where('status', 'paid')->sum('paid_amount'),
            'total_pending' => $payments->where('status', 'pending')->sum('payment_amount'),
            'total_overdue' => $payments->where('status', 'overdue')->sum('payment_amount'),
            'payment_rate' => $payments->count() > 0 ? ($payments->where('status', 'paid')->count() / $payments->count()) * 100 : 0
        ];
        
        return view('payment-schedules.report', compact('paymentPlan', 'payments', 'stats', 'startDate', 'endDate'));
    }
}
