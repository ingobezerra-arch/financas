<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Services\DebtManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
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
        $query = auth()->user()->debts();
        
        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('debt_type')) {
            $query->where('debt_type', $request->debt_type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('creditor', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}");
            });
        }
        
        $debts = $query->latest()->paginate(20);
        
        // Estatísticas
        $stats = [
            'total_debts' => auth()->user()->debts()->count(),
            'active_debts' => auth()->user()->debts()->active()->count(),
            'total_balance' => auth()->user()->debts()->active()->sum('current_balance'),
            'total_minimum_payments' => auth()->user()->debts()->active()->sum('minimum_payment'),
            'overdue_debts' => auth()->user()->debts()->overdue()->count(),
            'paid_debts' => auth()->user()->debts()->where('status', 'paid')->count()
        ];
        
        return view('debts.index', compact('debts', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('debts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'original_amount' => 'required|numeric|min:0.01',
            'current_balance' => 'required|numeric|min:0|lte:original_amount',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'minimum_payment' => 'required|numeric|min:0.01',
            'due_date' => 'nullable|date|after:today',
            'debt_type' => 'required|in:credit_card,loan,financing,invoice,other',
            'creditor' => 'nullable|string|max:255',
            'installments_total' => 'nullable|integer|min:1',
            'installments_paid' => 'nullable|integer|min:0|lte:installments_total',
            'contract_date' => 'nullable|date|before_or_equal:today'
        ]);
        
        $validated['user_id'] = auth()->id();
        
        auth()->user()->debts()->create($validated);
        
        return redirect()->route('debts.index')
            ->with('success', 'Dívida criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt): View
    {
        $this->authorize('view', $debt);
        
        $debt->load(['paymentPlans.paymentSchedules', 'paymentSchedules' => function($query) {
            $query->latest('due_date')->limit(10);
        }]);
        
        // Calcular previsões
        $projections = [
            'months_to_payoff' => $debt->calculateMonthsToPayoff(),
            'total_interest' => 0,
            'monthly_interest' => $debt->calculateMonthlyInterest()
        ];
        
        // Simular pagamentos
        $simulations = [
            'minimum_only' => $debt->simulatePayment($debt->minimum_payment),
            'double_minimum' => $debt->simulatePayment($debt->minimum_payment * 2),
            'extra_100' => $debt->simulatePayment($debt->minimum_payment + 100)
        ];
        
        return view('debts.show', compact('debt', 'projections', 'simulations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt): View
    {
        $this->authorize('update', $debt);
        
        return view('debts.edit', compact('debt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Debt $debt): RedirectResponse
    {
        $this->authorize('update', $debt);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'current_balance' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'minimum_payment' => 'required|numeric|min:0.01',
            'due_date' => 'nullable|date',
            'debt_type' => 'required|in:credit_card,loan,financing,invoice,other',
            'creditor' => 'nullable|string|max:255',
            'status' => 'required|in:active,paid,overdue,negotiated',
            'installments_total' => 'nullable|integer|min:1',
            'installments_paid' => 'nullable|integer|min:0|lte:installments_total',
            'contract_date' => 'nullable|date|before_or_equal:today'
        ]);
        
        $debt->update($validated);
        
        return redirect()->route('debts.show', $debt)
            ->with('success', 'Dívida atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt): RedirectResponse
    {
        $this->authorize('delete', $debt);
        
        $debt->delete();
        
        return redirect()->route('debts.index')
            ->with('success', 'Dívida excluída com sucesso!');
    }

    /**
     * Simula diferentes cenários de pagamento
     */
    public function simulate(Request $request, Debt $debt): View
    {
        $this->authorize('view', $debt);
        
        $scenarios = [];
        
        // Cenário atual (pagamento mínimo)
        $scenarios['current'] = [
            'name' => 'Pagamento Mínimo Atual',
            'payment' => $debt->minimum_payment,
            'months' => $debt->calculateMonthsToPayoff(),
            'total_paid' => $debt->minimum_payment * $debt->calculateMonthsToPayoff(),
            'total_interest' => ($debt->minimum_payment * $debt->calculateMonthsToPayoff()) - $debt->current_balance
        ];
        
        // Cenários com valores extras
        $extraAmounts = [50, 100, 200, 500];
        foreach ($extraAmounts as $extra) {
            $totalPayment = $debt->minimum_payment + $extra;
            $months = $this->calculateMonthsWithExtraPayment($debt, $extra);
            $totalPaid = $totalPayment * $months;
            
            $scenarios["extra_{$extra}"] = [
                'name' => "+ R$ {$extra} por mês",
                'payment' => $totalPayment,
                'months' => $months,
                'total_paid' => $totalPaid,
                'total_interest' => $totalPaid - $debt->current_balance,
                'savings' => $scenarios['current']['total_paid'] - $totalPaid,
                'months_saved' => $scenarios['current']['months'] - $months
            ];
        }
        
        return view('debts.simulate', compact('debt', 'scenarios'));
    }

    /**
     * Calcula meses para quitar com pagamento extra
     */
    protected function calculateMonthsWithExtraPayment(Debt $debt, float $extraPayment): int
    {
        $balance = $debt->current_balance;
        $monthlyPayment = $debt->minimum_payment + $extraPayment;
        $monthlyRate = $debt->interest_rate / 100;
        $months = 0;
        
        while ($balance > 0.01 && $months < 360) {
            $interestAmount = $balance * $monthlyRate;
            $principalAmount = $monthlyPayment - $interestAmount;
            $balance -= $principalAmount;
            $months++;
            
            if ($principalAmount <= 0) {
                return 999; // Nunca será pago
            }
        }
        
        return $months;
    }

    /**
     * Marca pagamento manual
     */
    public function recordPayment(Request $request, Debt $debt): RedirectResponse
    {
        $this->authorize('update', $debt);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);
        
        DB::transaction(function () use ($debt, $validated) {
            // Simular pagamento
            $payment = $debt->simulatePayment($validated['amount']);
            
            // Atualizar saldo da dívida
            $debt->update([
                'current_balance' => $payment['new_balance'],
                'status' => $payment['is_paid_off'] ? 'paid' : $debt->status
            ]);
            
            // Registrar na transação
            auth()->user()->transactions()->create([
                'description' => "Pagamento: {$debt->name}",
                'amount' => $validated['amount'],
                'type' => 'expense',
                'category_id' => null, // Ou categoria específica para dívidas
                'account_id' => auth()->user()->accounts()->first()->id ?? null,
                'transaction_date' => $validated['payment_date'],
                'status' => 'completed'
            ]);
        });
        
        return redirect()->route('debts.show', $debt)
            ->with('success', 'Pagamento registrado com sucesso!');
    }
}
