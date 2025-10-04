<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $accounts = auth()->user()->accounts()->latest()->get();
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $account = auth()->user()->accounts()->create($request->validated());

        return redirect()->route('accounts.index')
            ->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account): View
    {
        $this->authorize('view', $account);
        
        $recentTransactions = $account->transactions()
            ->with(['category'])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        return view('accounts.show', compact('account', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account): View
    {
        $this->authorize('update', $account);
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->authorize('update', $account);
        
        $account->update($request->validated());

        return redirect()->route('accounts.index')
            ->with('success', 'Conta atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);
        
        if ($account->transactions()->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'Não é possível excluir uma conta que possui transações.');
        }
        
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Conta excluída com sucesso!');
    }

    /**
     * Toggle account status (active/inactive)
     */
    public function toggleStatus(Account $account): RedirectResponse
    {
        $this->authorize('update', $account);
        
        $account->update(['is_active' => !$account->is_active]);
        
        $status = $account->is_active ? 'ativada' : 'desativada';
        
        return redirect()->route('accounts.index')
            ->with('success', "Conta {$status} com sucesso!");
    }
}
