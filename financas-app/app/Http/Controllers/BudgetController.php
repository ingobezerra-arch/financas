<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = auth()->user()->budgets()
            ->with(['category'])
            ->orderBy('start_date', 'desc');

        // Aplicar filtros
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->current()->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            } elseif ($request->status === 'over_budget') {
                $query->whereRaw('spent > amount');
            }
        }

        $budgets = $query->paginate(15);
        $categories = auth()->user()->categories()->where('type', 'expense')->get();

        // Atualizar gastos dos orçamentos ativos
        $this->updateBudgetSpending();

        // Estatísticas
        $activeBudgets = auth()->user()->budgets()->current()->active()->count();
        $totalBudget = auth()->user()->budgets()->current()->active()->sum('amount');
        $totalSpent = auth()->user()->budgets()->current()->active()->sum('spent');
        $overBudgetCount = auth()->user()->budgets()->current()->active()
            ->whereRaw('spent > amount')->count();

        return view('budgets.index', compact(
            'budgets',
            'categories',
            'activeBudgets',
            'totalBudget',
            'totalSpent',
            'overBudgetCount'
        ));
    }

    public function create(): View
    {
        $categories = auth()->user()->categories()->where('type', 'expense')->get();

        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'description' => 'nullable|string',
            'alert_percentage' => 'required|numeric|min:50|max:100',
        ]);

        // Verificar ownership da categoria
        $category = auth()->user()->categories()->findOrFail($validated['category_id']);

        // Verificar se a categoria é de despesa
        if ($category->type !== 'expense') {
            return back()->withErrors([
                'category_id' => 'Orçamentos só podem ser criados para categorias de despesa.'
            ])->withInput();
        }

        // Calcular data final baseado no período
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = match($validated['period']) {
            'weekly' => $startDate->copy()->addWeek()->subDay(),
            'monthly' => $startDate->copy()->addMonth()->subDay(),
            'quarterly' => $startDate->copy()->addQuarter()->subDay(),
            'yearly' => $startDate->copy()->addYear()->subDay(),
        };

        // Verificar se já existe orçamento para essa categoria no período
        $existingBudget = auth()->user()->budgets()
            ->where('category_id', $validated['category_id'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        if ($existingBudget) {
            return back()->withErrors([
                'category_id' => 'Já existe um orçamento para esta categoria no período selecionado.'
            ])->withInput();
        }

        $validated['user_id'] = auth()->id();
        $validated['end_date'] = $endDate;
        $validated['spent'] = 0;
        $validated['is_active'] = true;

        Budget::create($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Orçamento criado com sucesso!');
    }

    public function show(Budget $budget): View
    {
        $this->authorize('view', $budget);

        $budget->load(['category']);

        // Atualizar gastos
        $this->updateBudgetSpending($budget);

        // Buscar transações relacionadas a este orçamento
        $transactions = auth()->user()->transactions()
            ->where('category_id', $budget->category_id)
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        // Dados para gráfico de gastos diários
        $dailySpending = auth()->user()->transactions()
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->where('category_id', $budget->category_id)
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('budgets.show', compact('budget', 'transactions', 'dailySpending'));
    }

    public function edit(Budget $budget): View
    {
        $this->authorize('update', $budget);

        $categories = auth()->user()->categories()->where('type', 'expense')->get();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'description' => 'nullable|string',
            'alert_percentage' => 'required|numeric|min:50|max:100',
            'is_active' => 'boolean',
        ]);

        // Verificar ownership da categoria
        $category = auth()->user()->categories()->findOrFail($validated['category_id']);

        // Verificar se a categoria é de despesa
        if ($category->type !== 'expense') {
            return back()->withErrors([
                'category_id' => 'Orçamentos só podem ser criados para categorias de despesa.'
            ])->withInput();
        }

        // Calcular nova data final se necessário
        $startDate = Carbon::parse($validated['start_date']);
        if ($budget->start_date->ne($startDate) || $budget->period !== $validated['period']) {
            $endDate = match($validated['period']) {
                'weekly' => $startDate->copy()->addWeek()->subDay(),
                'monthly' => $startDate->copy()->addMonth()->subDay(),
                'quarterly' => $startDate->copy()->addQuarter()->subDay(),
                'yearly' => $startDate->copy()->addYear()->subDay(),
            };
            $validated['end_date'] = $endDate;
        }

        $validated['is_active'] = $request->has('is_active');

        $budget->update($validated);

        // Recalcular gastos se mudou categoria ou período
        if ($budget->wasChanged(['category_id', 'start_date', 'end_date'])) {
            $this->updateBudgetSpending($budget);
        }

        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Orçamento atualizado com sucesso!');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);

        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Orçamento excluído com sucesso!');
    }

    public function toggle(Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);

        $budget->update([
            'is_active' => !$budget->is_active
        ]);

        $status = $budget->is_active ? 'ativado' : 'desativado';

        return back()->with('success', "Orçamento {$status} com sucesso!");
    }

    /**
     * Atualizar gastos dos orçamentos
     */
    private function updateBudgetSpending(Budget $budget = null): void
    {
        $budgets = $budget ? collect([$budget]) : auth()->user()->budgets()->active()->get();

        foreach ($budgets as $budgetItem) {
            $spent = auth()->user()->transactions()
                ->where('category_id', $budgetItem->category_id)
                ->where('type', 'expense')
                ->where('status', 'completed')
                ->whereBetween('transaction_date', [$budgetItem->start_date, $budgetItem->end_date])
                ->sum('amount');

            $budgetItem->update(['spent' => $spent]);
        }
    }
}
