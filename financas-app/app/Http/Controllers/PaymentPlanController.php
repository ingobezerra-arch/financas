<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use App\Models\Debt;
use App\Services\DebtManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PaymentPlanController extends Controller
{
    protected DebtManagementService $debtService;

    public function __construct(DebtManagementService $debtService)
    {
        $this->middleware('auth');
        $this->debtService = $debtService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = auth()->user()->paymentPlans();
        
        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('strategy')) {
            $query->where('strategy', $request->strategy);
        }
        
        $paymentPlans = $query->with(['debts'])->latest()->paginate(10);
        
        // Estatísticas
        $stats = [
            'total_plans' => auth()->user()->paymentPlans()->count(),
            'active_plans' => auth()->user()->paymentPlans()->active()->count(),
            'completed_plans' => auth()->user()->paymentPlans()->completed()->count(),
            'total_debt_in_plans' => auth()->user()->paymentPlans()->active()->sum('total_debt_amount'),
            'monthly_budget_allocated' => auth()->user()->paymentPlans()->active()->sum('monthly_budget')
        ];
        
        return view('payment-plans.index', compact('paymentPlans', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $debts = auth()->user()->debts()->active()->get();
        
        if ($debts->isEmpty()) {
            return redirect()->route('debts.index')
                ->with('warning', 'Você precisa ter dívidas ativas para criar um plano de pagamento.');
        }
        
        // Comparar estratégias
        $comparison = null;
        if ($debts->count() > 1) {
            try {
                $comparison = $this->debtService->compareStrategies($debts, 1000); // Valor default para comparar
            } catch (\Exception $e) {
                // Ignore se não conseguir comparar
            }
        }
        
        return view('payment-plans.create', compact('debts', 'comparison'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'debt_ids' => 'required|array|min:1',
            'debt_ids.*' => 'exists:debts,id',
            'strategy' => 'required|in:snowball,avalanche,custom',
            'monthly_budget' => 'required|numeric|min:1',
            'extra_payment' => 'nullable|numeric|min:0',
            'custom_priorities' => 'nullable|array',
            'custom_priorities.*' => 'exists:debts,id'
        ]);
        
        try {
            $paymentPlan = $this->debtService->createPaymentPlan(
                auth()->user(),
                $validated['debt_ids'],
                $validated['strategy'],
                $validated['monthly_budget'],
                $validated['extra_payment'] ?? 0,
                $validated['custom_priorities'] ?? []
            );
            
            return redirect()->route('payment-plans.show', $paymentPlan)
                ->with('success', 'Plano de pagamento criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar plano: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentPlan $paymentPlan): View
    {
        $this->authorize('view', $paymentPlan);
        
        $paymentPlan->load([
            'debts' => function($query) {
                $query->orderBy('debt_payment_plan.priority_order');
            },
            'paymentSchedules' => function($query) {
                $query->with('debt')
                      ->orderBy('due_date')
                      ->limit(12); // Próximos 12 pagamentos
            }
        ]);
        
        // Próximos pagamentos
        $upcomingPayments = $paymentPlan->paymentSchedules()
            ->with('debt')
            ->upcoming(30)
            ->orderBy('due_date')
            ->get();
            
        // Pagamentos em atraso
        $overduePayments = $paymentPlan->paymentSchedules()
            ->with('debt')
            ->overdue()
            ->orderBy('due_date')
            ->get();
            
        // Progresso por dívida
        $debtProgress = [];
        foreach ($paymentPlan->debts as $debt) {
            $debtProgress[] = [
                'debt' => $debt,
                'progress' => $debt->percentage_paid,
                'remaining' => $debt->current_balance,
                'priority' => $debt->pivot->priority_order
            ];
        }
        
        return view('payment-plans.show', compact(
            'paymentPlan', 
            'upcomingPayments', 
            'overduePayments', 
            'debtProgress'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentPlan $paymentPlan): View
    {
        $this->authorize('update', $paymentPlan);
        
        $paymentPlan->load('debts');
        $allDebts = auth()->user()->debts()->active()->get();
        
        return view('payment-plans.edit', compact('paymentPlan', 'allDebts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentPlan $paymentPlan): RedirectResponse
    {
        $this->authorize('update', $paymentPlan);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_budget' => 'required|numeric|min:1',
            'extra_payment' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,paused,completed,cancelled'
        ]);
        
        $paymentPlan->update($validated);
        
        // Se mudou o orçamento, recalcular cronograma
        if ($paymentPlan->wasChanged(['monthly_budget', 'extra_payment'])) {
            $this->debtService->generatePaymentSchedule($paymentPlan);
        }
        
        return redirect()->route('payment-plans.show', $paymentPlan)
            ->with('success', 'Plano de pagamento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentPlan $paymentPlan): RedirectResponse
    {
        $this->authorize('delete', $paymentPlan);
        
        $paymentPlan->delete();
        
        return redirect()->route('payment-plans.index')
            ->with('success', 'Plano de pagamento excluído com sucesso!');
    }

    /**
     * Pausa ou retoma um plano
     */
    public function toggleStatus(PaymentPlan $paymentPlan): RedirectResponse
    {
        $this->authorize('update', $paymentPlan);
        
        $newStatus = $paymentPlan->status === 'active' ? 'paused' : 'active';
        $paymentPlan->update(['status' => $newStatus]);
        
        $message = $newStatus === 'active' ? 'Plano reativado!' : 'Plano pausado!';
        
        return back()->with('success', $message);
    }

    /**
     * Simula diferentes estratégias
     */
    public function compareStrategies(Request $request): View
    {
        $validated = $request->validate([
            'debt_ids' => 'required|array|min:2',
            'debt_ids.*' => 'exists:debts,id',
            'monthly_budget' => 'required|numeric|min:1',
            'extra_payment' => 'nullable|numeric|min:0'
        ]);
        
        $debts = auth()->user()->debts()
            ->whereIn('id', $validated['debt_ids'])
            ->active()
            ->get();
            
        if ($debts->count() < 2) {
            return back()->with('error', 'Selecione pelo menos 2 dívidas para comparar estratégias.');
        }
        
        try {
            $comparison = $this->debtService->compareStrategies(
                $debts,
                $validated['monthly_budget'],
                $validated['extra_payment'] ?? 0
            );
            
            return view('payment-plans.compare', compact('debts', 'comparison', 'validated'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao comparar estratégias: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza prioridades das dívidas no plano
     */
    public function updatePriorities(Request $request, PaymentPlan $paymentPlan): RedirectResponse
    {
        $this->authorize('update', $paymentPlan);
        
        $validated = $request->validate([
            'priorities' => 'required|array',
            'priorities.*' => 'integer|min:1'
        ]);
        
        DB::transaction(function () use ($paymentPlan, $validated) {
            foreach ($validated['priorities'] as $debtId => $priority) {
                $paymentPlan->debts()->updateExistingPivot($debtId, [
                    'priority_order' => $priority
                ]);
            }
            
            // Recalcular cronograma com novas prioridades
            $this->debtService->generatePaymentSchedule($paymentPlan);
        });
        
        return back()->with('success', 'Prioridades atualizadas com sucesso!');
    }

    /**
     * Adiciona dívida ao plano existente
     */
    public function addDebt(Request $request, PaymentPlan $paymentPlan): RedirectResponse
    {
        $this->authorize('update', $paymentPlan);
        
        $validated = $request->validate([
            'debt_id' => 'required|exists:debts,id',
            'priority_order' => 'nullable|integer|min:1'
        ]);
        
        $debt = auth()->user()->debts()->findOrFail($validated['debt_id']);
        
        if ($paymentPlan->debts->contains($debt->id)) {
            return back()->with('error', 'Esta dívida já está no plano.');
        }
        
        $priority = $validated['priority_order'] 
            ?? ($paymentPlan->debts->max('pivot.priority_order') + 1);
            
        $paymentPlan->debts()->attach($debt->id, [
            'initial_balance' => $debt->current_balance,
            'priority_order' => $priority,
            'added_date' => now(),
            'status' => 'active'
        ]);
        
        // Atualizar totais do plano
        $paymentPlan->update([
            'total_debt_amount' => $paymentPlan->debts->sum('current_balance')
        ]);
        
        // Recalcular cronograma
        $this->debtService->generatePaymentSchedule($paymentPlan);
        
        return back()->with('success', 'Dívida adicionada ao plano com sucesso!');
    }

    /**
     * Remove dívida do plano
     */
    public function removeDebt(PaymentPlan $paymentPlan, Debt $debt): RedirectResponse
    {
        $this->authorize('update', $paymentPlan);
        
        if (!$paymentPlan->debts->contains($debt->id)) {
            return back()->with('error', 'Esta dívida não está no plano.');
        }
        
        $paymentPlan->debts()->detach($debt->id);
        
        // Atualizar totais do plano
        $paymentPlan->update([
            'total_debt_amount' => $paymentPlan->debts->sum('current_balance')
        ]);
        
        // Recalcular cronograma se ainda houver dívidas
        if ($paymentPlan->debts->count() > 0) {
            $this->debtService->generatePaymentSchedule($paymentPlan);
        } else {
            $paymentPlan->update(['status' => 'cancelled']);
        }
        
        return back()->with('success', 'Dívida removida do plano com sucesso!');
    }
}
