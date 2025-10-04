<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = auth()->user()->transactions()->with(['account', 'category']);
        
        // Filtros
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        
        $transactions = $query->latest('transaction_date')->paginate(20);
        
        // Dados para filtros
        $accounts = auth()->user()->accounts()->where('is_active', true)->get();
        $categories = auth()->user()->categories()->where('is_active', true)->get();
        
        // Resumo
        $totalIncome = auth()->user()->transactions()
            ->where('type', 'income')
            ->where('status', 'completed')
            ->sum('amount');
            
        $totalExpenses = auth()->user()->transactions()
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        return view('transactions.index', compact('transactions', 'accounts', 'categories', 'totalIncome', 'totalExpenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $accounts = auth()->user()->accounts()->where('is_active', true)->get();
        $categories = auth()->user()->categories()->where('is_active', true)->get();
        
        return view('transactions.create', compact('accounts', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            // Criar a transação
            $transaction = auth()->user()->transactions()->create($data);
            
            // Atualizar saldo da conta
            $account = Account::find($data['account_id']);
            if ($data['type'] === 'income') {
                $account->increment('balance', $data['amount']);
            } else {
                $account->decrement('balance', $data['amount']);
            }
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transação criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): View
    {
        $this->authorize('view', $transaction);
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);
        
        $accounts = auth()->user()->accounts()->where('is_active', true)->get();
        $categories = auth()->user()->categories()->where('is_active', true)->get();
        
        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);
        
        DB::transaction(function () use ($request, $transaction) {
            $data = $request->validated();
            $oldAmount = $transaction->amount;
            $oldType = $transaction->type;
            $oldAccountId = $transaction->account_id;
            
            // Reverter o saldo anterior
            $oldAccount = Account::find($oldAccountId);
            if ($oldType === 'income') {
                $oldAccount->decrement('balance', $oldAmount);
            } else {
                $oldAccount->increment('balance', $oldAmount);
            }
            
            // Atualizar a transação
            $transaction->update($data);
            
            // Aplicar o novo saldo
            $newAccount = Account::find($data['account_id']);
            if ($data['type'] === 'income') {
                $newAccount->increment('balance', $data['amount']);
            } else {
                $newAccount->decrement('balance', $data['amount']);
            }
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);
        
        DB::transaction(function () use ($transaction) {
            // Reverter o saldo
            $account = $transaction->account;
            if ($transaction->type === 'income') {
                $account->decrement('balance', $transaction->amount);
            } else {
                $account->increment('balance', $transaction->amount);
            }
            
            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transação excluída com sucesso!');
    }
}
