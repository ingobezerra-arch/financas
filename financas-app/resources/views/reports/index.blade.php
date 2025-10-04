@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-0">{{ __('Relatórios Financeiros') }}</h4>
                    <div class="d-flex gap-2 flex-wrap">
                        <select id="periodSelect" class="form-select" style="width: auto;">
                            <option value="current_month" {{ $period == 'current_month' ? 'selected' : '' }}>Mês Atual</option>
                            <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Mês Passado</option>
                            <option value="last_7_days" {{ $period == 'last_7_days' ? 'selected' : '' }}>Últimos 7 dias</option>
                            <option value="last_30_days" {{ $period == 'last_30_days' ? 'selected' : '' }}>Últimos 30 dias</option>
                            <option value="last_90_days" {{ $period == 'last_90_days' ? 'selected' : '' }}>Últimos 90 dias</option>
                            <option value="current_year" {{ $period == 'current_year' ? 'selected' : '' }}>Ano Atual</option>
                            <option value="last_year" {{ $period == 'last_year' ? 'selected' : '' }}>Ano Passado</option>
                        </select>
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Resumo Período -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 class="mb-1"><i class="fas fa-calendar"></i> Período: 
                                    {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}
                                </h6>
                                <small class="text-muted">{{ $startDate->diffInDays($endDate) + 1 }} dias</small>
                            </div>
                        </div>
                    </div>

                    <!-- Cards de Resumo -->
                    <div class="row mb-4 g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                    <h6 class="mb-1">Total Receitas</h6>
                                    <h4 class="mb-0">R$ {{ number_format($summary['total_income'], 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                    <h6 class="mb-1">Total Despesas</h6>
                                    <h4 class="mb-0">R$ {{ number_format($summary['total_expense'], 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-{{ $summary['balance'] >= 0 ? 'primary' : 'warning' }} text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-balance-scale fa-2x mb-2"></i>
                                    <h6 class="mb-1">Saldo do Período</h6>
                                    <h4 class="mb-0">R$ {{ number_format($summary['balance'], 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-exchange-alt fa-2x mb-2"></i>
                                    <h6 class="mb-1">Transações</h6>
                                    <h4 class="mb-0">{{ $summary['transaction_count'] }}</h4>
                                    <small>Média: R$ {{ number_format($summary['average_daily_spending'], 2, ',', '.') }}/dia</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navegação de Relatórios -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <ul class="nav nav-pills justify-content-center" id="reportTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                                        <i class="fas fa-chart-pie"></i> Visão Geral
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="trends-tab" data-bs-toggle="pill" data-bs-target="#trends" type="button" role="tab">
                                        <i class="fas fa-chart-line"></i> Tendências
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="categories-tab" data-bs-toggle="pill" data-bs-target="#categories" type="button" role="tab">
                                        <i class="fas fa-tags"></i> Categorias
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="accounts-tab" data-bs-toggle="pill" data-bs-target="#accounts" type="button" role="tab">
                                        <i class="fas fa-wallet"></i> Contas
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="reportTabsContent">
                        <!-- Visão Geral -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Receitas vs Despesas</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="incomeExpenseChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Gastos por Categoria</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="categoryChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tendências -->
                        <div class="tab-pane fade" id="trends" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-chart-line"></i> Tendência Mensal - Últimos 12 Meses</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="trendChart" height="400"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Categorias Detalhadas -->
                        <div class="tab-pane fade" id="categories" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-chart-doughnut"></i> Distribuição por Categoria</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="categoryDetailChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-list"></i> Top Categorias</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="categoryList" class="list-group list-group-flush">
                                                <!-- Lista será preenchida via JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contas -->
                        <div class="tab-pane fade" id="accounts" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Distribuição de Saldo por Conta</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="accountChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Links Rápidos -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-link"></i> Relatórios Detalhados</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-lg-3 col-md-6">
                                            <a href="{{ route('reports.transactions') }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-exchange-alt"></i><br>
                                                Relatório de Transações
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <a href="{{ route('reports.budgets') }}" class="btn btn-outline-success w-100">
                                                <i class="fas fa-chart-pie"></i><br>
                                                Relatório de Orçamentos
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <a href="{{ route('reports.goals') }}" class="btn btn-outline-info w-100">
                                                <i class="fas fa-bullseye"></i><br>
                                                Relatório de Metas
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <button class="btn btn-outline-secondary w-100" onclick="exportReport()">
                                                <i class="fas fa-file-pdf"></i><br>
                                                Exportar PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados dos gráficos vindos do backend
    const chartData = @json($chartData);
    
    // Configurações globais do Chart.js
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    
    // Gráfico Receitas vs Despesas (Pizza)
    const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(incomeExpenseCtx, {
        type: 'doughnut',
        data: chartData.income_vs_expense,
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Gráfico Categorias (Barra)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: chartData.category_breakdown,
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico de Tendência Mensal (Linha)
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: chartData.monthly_trend,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico Detalhado de Categorias (Doughnut)
    const categoryDetailCtx = document.getElementById('categoryDetailChart').getContext('2d');
    new Chart(categoryDetailCtx, {
        type: 'doughnut',
        data: chartData.category_breakdown,
        options: {
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Gráfico de Contas (Pizza)
    const accountCtx = document.getElementById('accountChart').getContext('2d');
    new Chart(accountCtx, {
        type: 'pie',
        data: chartData.account_distribution,
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Preencher lista de categorias
    fillCategoryList(chartData.category_breakdown);
    
    // Event listener para mudança de período
    document.getElementById('periodSelect').addEventListener('change', function() {
        const newPeriod = this.value;
        window.location.href = `{{ route('reports.index') }}?period=${newPeriod}`;
    });
});

function fillCategoryList(categoryData) {
    const categoryList = document.getElementById('categoryList');
    let html = '';
    
    categoryData.labels.forEach((label, index) => {
        const value = categoryData.data[index];
        const color = categoryData.backgroundColor[index];
        
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge me-2" style="background-color: ${color}; width: 12px; height: 12px;"></span>
                    ${label}
                </div>
                <strong>R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</strong>
            </div>
        `;
    });
    
    categoryList.innerHTML = html;
}

function exportReport() {
    // Implementar exportação
    alert('Funcionalidade de exportação em desenvolvimento');
}

// Tornar gráficos responsivos
window.addEventListener('resize', function() {
    Chart.helpers.each(Chart.instances, function(instance) {
        instance.resize();
    });
});
</script>
@endpush