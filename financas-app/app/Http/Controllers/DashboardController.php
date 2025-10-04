<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = auth()->user();
        
        // Resumo financeiro
        $totalBalance = $user->accounts()->where('is_active', true)->sum('balance');
        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        
        $monthlyExpenses = $user->transactions()
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        
        $monthlyBalance = $monthlyIncome - $monthlyExpenses;
        
        // Contas ativas
        $activeAccounts = $user->accounts()->where('is_active', true)->take(4)->get();
        
        // Transações recentes
        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->take(5)
            ->get();
        
        // Orçamentos do mês atual
        $currentBudgets = $user->budgets()
            ->with('category')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->take(3)
            ->get();
        
        // Metas ativas
        $activeGoals = $user->goals()
            ->where('status', 'active')
            ->orderBy('target_date')
            ->take(3)
            ->get();
        
        // Estatísticas de categorias (top 5 gastos do mês)
        $topExpenseCategories = $user->transactions()
            ->select('category_id')
            ->selectRaw('SUM(amount) as total_amount')
            ->with('category')
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->groupBy('category_id')
            ->orderByDesc('total_amount')
            ->take(5)
            ->get();
        
        // Dados para gráficos
        $last7DaysData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayIncome = $user->transactions()
                ->where('type', 'income')
                ->where('status', 'completed')
                ->whereDate('transaction_date', $date)
                ->sum('amount');
            
            $dayExpenses = $user->transactions()
                ->where('type', 'expense')
                ->where('status', 'completed')
                ->whereDate('transaction_date', $date)
                ->sum('amount');
            
            $last7DaysData->push([
                'date' => $date->format('d/m'),
                'income' => $dayIncome,
                'expenses' => $dayExpenses,
                'balance' => $dayIncome - $dayExpenses
            ]);
        }
        
        return view('dashboard', compact(
            'totalBalance',
            'monthlyIncome',
            'monthlyExpenses',
            'monthlyBalance',
            'activeAccounts',
            'recentTransactions',
            'currentBudgets',
            'activeGoals',
            'topExpenseCategories',
            'last7DaysData'
        ));
    }
}
