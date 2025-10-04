<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecurringTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = auth()->user()->recurringTransactions()
            ->with(['account', 'category'])
            ->orderBy('next_due_date', 'asc');

        // Aplicar filtros
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $recurringTransactions = $query->paginate(15);
        $accounts = auth()->user()->accounts;
        $categories = auth()->user()->categories;

        // Estatísticas
        $totalActive = auth()->user()->recurringTransactions()->active()->count();
        $totalDue = auth()->user()->recurringTransactions()->active()->due()->count();
        $monthlyIncome = auth()->user()->recurringTransactions()
            ->active()
            ->where('type', 'income')
            ->where('frequency', 'monthly')
            ->sum('amount');
        $monthlyExpenses = auth()->user()->recurringTransactions()
            ->active()
            ->where('type', 'expense')
            ->where('frequency', 'monthly')
            ->sum('amount');

        return view('recurring-transactions.index', compact(
            'recurringTransactions',
            'accounts',
            'categories',
            'totalActive',
            'totalDue',
            'monthlyIncome',
            'monthlyExpenses'
        ));
    }

    public function create(): View
    {
        $accounts = auth()->user()->accounts;
        $categories = auth()->user()->categories;

        return view('recurring-transactions.create', compact('accounts', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'interval' => 'required|integer|min:1|max:12',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'occurrences' => 'nullable|integer|min:1',
            'tags' => 'nullable|string',
        ]);

        // Verificar ownership da conta e categoria
        $account = auth()->user()->accounts()->findOrFail($validated['account_id']);
        $category = auth()->user()->categories()->findOrFail($validated['category_id']);

        // Verificar se o tipo da transação é compatível com a categoria
        if ($category->type !== $validated['type']) {
            return back()->withErrors([
                'category_id' => 'A categoria selecionada não é compatível com o tipo de transação.'
            ])->withInput();
        }

        // Processar tags
        $tags = null;
        if ($validated['tags']) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $tags = array_filter($tags); // Remove valores vazios
        }

        // Calcular next_due_date
        $nextDueDate = Carbon::parse($validated['start_date']);

        $validated['user_id'] = auth()->id();
        $validated['next_due_date'] = $nextDueDate;
        $validated['tags'] = $tags;
        $validated['occurrences_count'] = 0;
        $validated['is_active'] = true;

        RecurringTransaction::create($validated);

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Transação recorrente criada com sucesso!');
    }

    public function show(RecurringTransaction $recurringTransaction): View
    {
        $this->authorize('view', $recurringTransaction);

        $recurringTransaction->load(['account', 'category', 'transactions']);

        // Próximas 5 execuções previstas
        $nextExecutions = [];
        $date = Carbon::parse($recurringTransaction->next_due_date);
        for ($i = 0; $i < 5; $i++) {
            if ($recurringTransaction->end_date && $date->gt($recurringTransaction->end_date)) {
                break;
            }
            if ($recurringTransaction->occurrences && 
                ($recurringTransaction->occurrences_count + $i) >= $recurringTransaction->occurrences) {
                break;
            }
            $nextExecutions[] = $date->copy();
            $date = $date->copy();
            switch ($recurringTransaction->frequency) {
                case 'daily':
                    $date->addDays($recurringTransaction->interval);
                    break;
                case 'weekly':
                    $date->addWeeks($recurringTransaction->interval);
                    break;
                case 'monthly':
                    $date->addMonths($recurringTransaction->interval);
                    break;
                case 'yearly':
                    $date->addYears($recurringTransaction->interval);
                    break;
            }
        }

        return view('recurring-transactions.show', compact('recurringTransaction', 'nextExecutions'));
    }

    public function edit(RecurringTransaction $recurringTransaction): View
    {
        $this->authorize('update', $recurringTransaction);

        $accounts = auth()->user()->accounts;
        $categories = auth()->user()->categories;

        return view('recurring-transactions.edit', compact('recurringTransaction', 'accounts', 'categories'));
    }

    public function update(Request $request, RecurringTransaction $recurringTransaction): RedirectResponse
    {
        $this->authorize('update', $recurringTransaction);

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'interval' => 'required|integer|min:1|max:12',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'occurrences' => 'nullable|integer|min:1',
            'tags' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Verificar ownership da conta e categoria
        $account = auth()->user()->accounts()->findOrFail($validated['account_id']);
        $category = auth()->user()->categories()->findOrFail($validated['category_id']);

        // Verificar se o tipo da transação é compatível com a categoria
        if ($category->type !== $validated['type']) {
            return back()->withErrors([
                'category_id' => 'A categoria selecionada não é compatível com o tipo de transação.'
            ])->withInput();
        }

        // Processar tags
        $tags = null;
        if ($validated['tags']) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $tags = array_filter($tags); // Remove valores vazios
        }

        $validated['tags'] = $tags;
        $validated['is_active'] = $request->has('is_active');

        $recurringTransaction->update($validated);

        return redirect()->route('recurring-transactions.show', $recurringTransaction)
            ->with('success', 'Transação recorrente atualizada com sucesso!');
    }

    public function destroy(RecurringTransaction $recurringTransaction): RedirectResponse
    {
        $this->authorize('delete', $recurringTransaction);

        $recurringTransaction->delete();

        return redirect()->route('recurring-transactions.index')
            ->with('success', 'Transação recorrente excluída com sucesso!');
    }

    public function execute(RecurringTransaction $recurringTransaction): RedirectResponse
    {
        $this->authorize('update', $recurringTransaction);

        if (!$recurringTransaction->shouldExecute()) {
            return back()->with('error', 'Esta transação recorrente não está pronta para execução.');
        }

        DB::transaction(function () use ($recurringTransaction) {
            // Criar a transação
            $transaction = Transaction::create([
                'user_id' => $recurringTransaction->user_id,
                'account_id' => $recurringTransaction->account_id,
                'category_id' => $recurringTransaction->category_id,
                'recurring_transaction_id' => $recurringTransaction->id,
                'type' => $recurringTransaction->type,
                'amount' => $recurringTransaction->amount,
                'description' => $recurringTransaction->description,
                'notes' => $recurringTransaction->notes,
                'transaction_date' => Carbon::now(),
                'status' => 'completed',
                'tags' => $recurringTransaction->tags,
            ]);

            // Atualizar saldo da conta
            $account = Account::find($recurringTransaction->account_id);
            if ($recurringTransaction->type === 'income') {
                $account->increment('balance', $recurringTransaction->amount);
            } else {
                $account->decrement('balance', $recurringTransaction->amount);
            }

            // Atualizar a transação recorrente
            $recurringTransaction->increment('occurrences_count');
            $recurringTransaction->next_due_date = $recurringTransaction->calculateNextDueDate();

            // Verificar se deve desativar
            if ($recurringTransaction->isExpired()) {
                $recurringTransaction->is_active = false;
            }

            $recurringTransaction->save();
        });

        return back()->with('success', 'Transação executada com sucesso!');
    }

    public function toggle(RecurringTransaction $recurringTransaction): RedirectResponse
    {
        $this->authorize('update', $recurringTransaction);

        $recurringTransaction->update([
            'is_active' => !$recurringTransaction->is_active
        ]);

        $status = $recurringTransaction->is_active ? 'ativada' : 'desativada';

        return back()->with('success', "Transação recorrente {$status} com sucesso!");
    }
}
