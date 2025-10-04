<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $period = $request->get('period', 'current_month');
        $startDate = $this->getStartDate($period);
        $endDate = $this->getEndDate($period);

        // Resumo geral
        $summary = $this->getSummaryData($startDate, $endDate);
        
        // Dados para gráficos
        $chartData = [
            'income_vs_expense' => $this->getIncomeVsExpenseData($startDate, $endDate),
            'category_breakdown' => $this->getCategoryBreakdownData($startDate, $endDate),
            'monthly_trend' => $this->getMonthlyTrendData(),
            'account_distribution' => $this->getAccountDistributionData(),
        ];

        return view('reports.index', compact('summary', 'chartData', 'period', 'startDate', 'endDate'));
    }

    public function transactions(Request $request): View
    {
        $period = $request->get('period', 'current_month');
        $categoryId = $request->get('category_id');
        $accountId = $request->get('account_id');
        $type = $request->get('type');
        
        $startDate = $this->getStartDate($period);
        $endDate = $this->getEndDate($period);

        $query = auth()->user()->transactions()
            ->with(['account', 'category'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'completed');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);
        
        $accounts = auth()->user()->accounts;
        $categories = auth()->user()->categories;
        
        // Resumo da pesquisa
        $searchSummary = [
            'total_income' => $query->clone()->where('type', 'income')->sum('amount'),
            'total_expense' => $query->clone()->where('type', 'expense')->sum('amount'),
            'count' => $query->clone()->count(),
        ];

        return view('reports.transactions', compact(
            'transactions', 
            'accounts', 
            'categories', 
            'period', 
            'categoryId', 
            'accountId', 
            'type',
            'searchSummary',
            'startDate',
            'endDate'
        ));
    }

    public function budgets(Request $request): View
    {
        $budgets = auth()->user()->budgets()
            ->with(['category'])
            ->current()
            ->active()
            ->get();

        // Calcular progresso dos orçamentos
        foreach ($budgets as $budget) {
            $budget->spent = $budget->spent_amount; // Usar o accessor que calcula dinamicamente
        }

        $budgetSummary = [
            'total_budgeted' => $budgets->sum('amount'),
            'total_spent' => $budgets->sum('spent'),
            'over_budget_count' => $budgets->filter(fn($b) => $b->spent > $b->amount)->count(),
            'on_track_count' => $budgets->filter(fn($b) => $b->spent <= $b->amount * 0.8)->count(),
        ];

        return view('reports.budgets', compact('budgets', 'budgetSummary'));
    }

    public function goals(Request $request): View
    {
        $goals = auth()->user()->goals()->get();
        
        $goalsSummary = [
            'total_goals' => $goals->count(),
            'active_goals' => $goals->where('status', 'active')->count(),
            'completed_goals' => $goals->where('status', 'completed')->count(),
            'total_target' => $goals->where('status', 'active')->sum('target_amount'),
            'total_saved' => $goals->where('status', 'active')->sum('current_amount'),
        ];

        return view('reports.goals', compact('goals', 'goalsSummary'));
    }

    public function exportPdf(Request $request)
    {
        // Implementar exportação PDF se necessário
        return response()->json(['message' => 'Funcionalidade em desenvolvimento']);
    }

    public function getChartData(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $period = $request->get('period', 'current_month');
        
        $startDate = $this->getStartDate($period);
        $endDate = $this->getEndDate($period);

        $data = match($type) {
            'income_vs_expense' => $this->getIncomeVsExpenseData($startDate, $endDate),
            'category_breakdown' => $this->getCategoryBreakdownData($startDate, $endDate),
            'monthly_trend' => $this->getMonthlyTrendData(),
            'account_distribution' => $this->getAccountDistributionData(),
            'daily_spending' => $this->getDailySpendingData($startDate, $endDate),
            default => []
        };

        return response()->json($data);
    }

    private function getSummaryData($startDate, $endDate): array
    {
        $transactions = auth()->user()->transactions()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'completed');

        $totalIncome = $transactions->clone()->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->clone()->where('type', 'expense')->sum('amount');
        $transactionCount = $transactions->count();
        $balance = $totalIncome - $totalExpense;

        $averageDailySpending = $totalExpense / max(1, Carbon::parse($startDate)->diffInDays($endDate));
        
        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
            'transaction_count' => $transactionCount,
            'average_daily_spending' => $averageDailySpending,
        ];
    }

    private function getIncomeVsExpenseData($startDate, $endDate): array
    {
        $transactions = auth()->user()->transactions()
            ->selectRaw('type, SUM(amount) as total')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('type')
            ->get();

        $income = $transactions->where('type', 'income')->first()->total ?? 0;
        $expense = $transactions->where('type', 'expense')->first()->total ?? 0;

        return [
            'labels' => ['Receitas', 'Despesas'],
            'data' => [$income, $expense],
            'backgroundColor' => ['#28a745', '#dc3545']
        ];
    }

    private function getCategoryBreakdownData($startDate, $endDate): array
    {
        $categoryData = auth()->user()->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('transactions.status', 'completed')
            ->where('transactions.type', 'expense')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $categoryData->pluck('name')->toArray(),
            'data' => $categoryData->pluck('total')->toArray(),
            'backgroundColor' => $categoryData->pluck('color')->toArray()
        ];
    }

    private function getMonthlyTrendData(): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $months[] = $date->format('M/Y');
            
            $income = auth()->user()->transactions()
                ->where('type', 'income')
                ->where('status', 'completed')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $expense = auth()->user()->transactions()
                ->where('type', 'expense')
                ->where('status', 'completed')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $incomeData[] = $income;
            $expenseData[] = $expense;
        }

        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'data' => $incomeData,
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.1)',
                    'fill' => true
                ],
                [
                    'label' => 'Despesas',
                    'data' => $expenseData,
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.1)',
                    'fill' => true
                ]
            ]
        ];
    }

    private function getAccountDistributionData(): array
    {
        $accounts = auth()->user()->accounts()->where('is_active', true)->get();
        
        return [
            'labels' => $accounts->pluck('name')->toArray(),
            'data' => $accounts->pluck('balance')->toArray(),
            'backgroundColor' => $accounts->pluck('color')->toArray()
        ];
    }

    private function getDailySpendingData($startDate, $endDate): array
    {
        $dailyData = auth()->user()->transactions()
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $dailyData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray(),
            'data' => $dailyData->pluck('total')->toArray(),
            'borderColor' => '#007bff',
            'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
            'fill' => true
        ];
    }

    private function getStartDate($period): Carbon
    {
        return match($period) {
            'current_month' => Carbon::now()->startOfMonth(),
            'last_month' => Carbon::now()->subMonth()->startOfMonth(),
            'current_year' => Carbon::now()->startOfYear(),
            'last_year' => Carbon::now()->subYear()->startOfYear(),
            'last_7_days' => Carbon::now()->subDays(7),
            'last_30_days' => Carbon::now()->subDays(30),
            'last_90_days' => Carbon::now()->subDays(90),
            default => Carbon::now()->startOfMonth()
        };
    }

    private function getEndDate($period): Carbon
    {
        return match($period) {
            'current_month' => Carbon::now()->endOfMonth(),
            'last_month' => Carbon::now()->subMonth()->endOfMonth(),
            'current_year' => Carbon::now()->endOfYear(),
            'last_year' => Carbon::now()->subYear()->endOfYear(),
            'last_7_days' => Carbon::now(),
            'last_30_days' => Carbon::now(),
            'last_90_days' => Carbon::now(),
            default => Carbon::now()->endOfMonth()
        };
    }
}
