@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Financeiro</h1>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Saldo Total</h6>
                            <h3 class="mb-0">R$ {{ number_format($totalBalance, 2, ',', '.') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Receitas (Mês)</h6>
                            <h3 class="mb-0">R$ {{ number_format($monthlyIncome, 2, ',', '.') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Despesas (Mês)</h6>
                            <h3 class="mb-0">R$ {{ number_format($monthlyExpenses, 2, ',', '.') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card {{ $monthlyBalance >= 0 ? 'bg-info' : 'bg-warning' }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Saldo (Mês)</h6>
                            <h3 class="mb-0">R$ {{ number_format($monthlyBalance, 2, ',', '.') }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas {{ $monthlyBalance >= 0 ? 'fa-chart-line' : 'fa-exclamation-triangle' }} fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Movimentação dos Últimos 7 Dias -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Movimentação dos Últimos 7 Dias</h5>
                </div>
                <div class="card-body">
                    <canvas id="last7DaysChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Contas Ativas -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-university me-2"></i>Contas Ativas</h6>
                    <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body">
                    @forelse($activeAccounts as $account)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="{{ $account->icon }} me-2" style="color: {{ $account->color }};"></i>
                            <div>
                                <h6 class="mb-0">{{ $account->name }}</h6>
                                <small class="text-muted">{{ ucfirst($account->type) }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold {{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($account->balance, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Nenhuma conta ativa encontrada.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Transações Recentes -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Transações Recentes</h6>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body">
                    @forelse($recentTransactions as $transaction)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas {{ $transaction->type === 'income' ? 'fa-arrow-up text-success' : 'fa-arrow-down text-danger' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $transaction->description }}</h6>
                                <small class="text-muted">
                                    {{ $transaction->category->name }} • {{ $transaction->account->name }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted">{{ $transaction->transaction_date->format('d/m/Y') }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Nenhuma transação encontrada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Orçamentos do Mês -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Orçamentos do Mês</h6>
                    <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    @forelse($currentBudgets as $budget)
                    @php
                        $spent = $budget->spent_amount;
                        $percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
                        $progressColor = $percentage <= 50 ? 'bg-success' : ($percentage <= 80 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">{{ $budget->category->name }}</h6>
                            <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                        </div>
                        <div class="progress mb-1" style="height: 6px;">
                            <div class="progress-bar {{ $progressColor }}" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">R$ {{ number_format($spent, 2, ',', '.') }}</small>
                            <small class="text-muted">R$ {{ number_format($budget->amount, 2, ',', '.') }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Nenhum orçamento ativo encontrado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Metas Ativas -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Metas Ativas</h6>
                    <a href="{{ route('goals.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body">
                    @forelse($activeGoals as $goal)
                    @php
                        $percentage = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <i class="{{ $goal->icon }} me-2" style="color: {{ $goal->color }};"></i>
                                <h6 class="mb-0">{{ $goal->name }}</h6>
                            </div>
                            <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                        </div>
                        <div class="progress mb-1" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ min($percentage, 100) }}%; background-color: {{ $goal->color }};"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</small>
                            <small class="text-muted">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</small>
                        </div>
                        <small class="text-muted">Meta: {{ $goal->target_date->format('d/m/Y') }}</small>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Nenhuma meta ativa encontrada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Categorias de Gastos -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Maiores Gastos do Mês</h6>
                </div>
                <div class="card-body">
                    @forelse($topExpenseCategories as $categoryData)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="{{ $categoryData->category->icon ?? 'fas fa-tag' }} me-2" 
                               style="color: {{ $categoryData->category->color ?? '#6c757d' }};"></i>
                            <div>
                                <h6 class="mb-0">{{ $categoryData->category->name }}</h6>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-danger">
                                R$ {{ number_format($categoryData->total_amount, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">Nenhum gasto registrado este mês.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico dos últimos 7 dias
const ctx = document.getElementById('last7DaysChart').getContext('2d');
const chartData = @json($last7DaysData);

const last7DaysChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(item => item.date),
        datasets: [
            {
                label: 'Receitas',
                data: chartData.map(item => item.income),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            },
            {
                label: 'Despesas',
                data: chartData.map(item => item.expenses),
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': R$ ' + 
                               context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }
                }
            },
            legend: {
                display: true,
                position: 'top'
            }
        }
    }
});
</script>
@endpush
