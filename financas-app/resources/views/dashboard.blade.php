@extends('layouts.app')

@section('content')
<div class="space-y-6 py-6">
    <!-- Header com título e breadcrumb -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 flex items-center">
                    <i class="fas fa-tachometer-alt text-purple-500 mr-3" style="font-size: 2rem;"></i>
                    <span>Dashboard Financeiro</span>
                </h1>
                <p class="text-gray-600 dark:text-gray-300">Visão geral das suas finanças</p>
            </div>
            <div class="text-right">
                <span class="text-sm text-gray-500 dark:text-gray-400">Hoje</span>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Saldo Total -->
        <div class="stats-card bg-gradient-to-r from-purple-500 to-blue-600 transform transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white/80 text-sm font-medium mb-1">Saldo Total</h3>
                    <p class="text-white text-2xl font-bold animate-number" data-value="{{ $totalBalance }}">R$ 0,00</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3 animate-pulse">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-white/70 text-xs">
                <i class="fas fa-chart-line mr-1"></i>
                <span>Atualizado agora</span>
            </div>
        </div>

        <!-- Receitas do Mês -->
        <div class="stats-card stats-card-success bg-gradient-to-r from-green-500 to-emerald-600 transform transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white/80 text-sm font-medium mb-1">Receitas (Mês)</h3>
                    <p class="text-white text-2xl font-bold animate-number" data-value="{{ $monthlyIncome }}">R$ 0,00</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3 animate-bounce">
                    <i class="fas fa-arrow-up text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-white/70 text-xs">
                <i class="fas fa-trending-up mr-1"></i>
                <span>+12% vs mês anterior</span>
            </div>
        </div>

        <!-- Despesas do Mês -->
        <div class="stats-card stats-card-danger bg-gradient-to-r from-red-500 to-pink-600 transform transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white/80 text-sm font-medium mb-1">Despesas (Mês)</h3>
                    <p class="text-white text-2xl font-bold animate-number" data-value="{{ $monthlyExpenses }}">R$ 0,00</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3 animate-pulse">
                    <i class="fas fa-arrow-down text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-white/70 text-xs">
                <i class="fas fa-trending-down mr-1"></i>
                <span>-5% vs mês anterior</span>
            </div>
        </div>

        <!-- Saldo do Mês -->
        <div class="stats-card {{ $monthlyBalance >= 0 ? 'stats-card-info bg-gradient-to-r from-blue-500 to-indigo-600' : 'stats-card-warning bg-gradient-to-r from-yellow-500 to-orange-600' }} transform transition-all duration-300 hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white/80 text-sm font-medium mb-1">Saldo (Mês)</h3>
                    <p class="text-white text-2xl font-bold animate-number" data-value="{{ $monthlyBalance }}">R$ 0,00</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3 {{ $monthlyBalance >= 0 ? 'animate-bounce' : 'animate-pulse' }}">
                    <i class="fas {{ $monthlyBalance >= 0 ? 'fa-chart-line' : 'fa-exclamation-triangle' }} text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-white/70 text-xs">
                <i class="fas {{ $monthlyBalance >= 0 ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-1"></i>
                <span>{{ $monthlyBalance >= 0 ? 'Situação favorável' : 'Atenção necessária' }}</span>
            </div>
        </div>
    </div>

    <!-- Seção de Charts e Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Gráfico de Movimentação dos Últimos 7 Dias -->
        <div class="lg:col-span-2">
            <div class="card-modern">
                <div class="card-header-modern">
                    <h5 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Movimentação dos Últimos 7 Dias
                    </h5>
                </div>
                <div class="p-6" style="height: 400px;">
                    <canvas id="last7DaysChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Contas Ativas -->
        <div>
            <div class="card-modern">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-university text-purple-500 mr-2"></i>
                        Contas Ativas
                    </h6>
                    <a href="{{ route('accounts.index') }}" class="btn-gradient-primary text-sm px-4 py-2">
                        Ver Todas
                    </a>
                </div>
                <div class="p-6">
                    <!-- Gráfico de Pizza das Contas -->
                    <div class="mb-6 h-48">
                        <canvas id="accountsChart"></canvas>
                    </div>
                    
                    @forelse($activeAccounts as $account)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }} transform transition-all duration-200 hover:scale-102 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg px-2">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-purple-400 to-blue-500 flex items-center justify-center mr-3 animate-pulse">
                                <i class="{{ $account->icon }} text-white" style="color: white !important;"></i>
                            </div>
                            <div>
                                <h6 class="text-gray-900 dark:text-white font-medium">{{ $account->name }}</h6>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ ucfirst($account->type) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold {{ $account->balance >= 0 ? 'text-green-600' : 'text-red-600' }} animate-number" data-value="{{ $account->balance }}">
                                R$ {{ number_format($account->balance, 2, ',', '.') }}
                            </span>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-1">
                                <div class="bg-gradient-to-r from-purple-500 to-blue-600 h-1 rounded-full transition-all duration-1000" style="width: {{ min(abs($account->balance) / max($activeAccounts->max('balance'), 1) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-university text-gray-300 text-3xl mb-3 animate-bounce"></i>
                        <p class="text-gray-500 dark:text-gray-400">Nenhuma conta ativa encontrada.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Transações, Orçamentos e Metas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transações Recentes -->
        <div class="card-modern">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-exchange-alt text-purple-500 mr-2"></i>
                    Transações Recentes
                </h6>
                <a href="{{ route('transactions.index') }}" class="btn-gradient-primary text-sm px-4 py-2">
                    Ver Todas
                </a>
            </div>
            <div class="p-6">
                @forelse($recentTransactions as $transaction)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center mr-3">
                            <i class="fas {{ $transaction->type === 'income' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        </div>
                        <div>
                            <h6 class="text-gray-900 dark:text-white font-medium">{{ $transaction->description }}</h6>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                {{ $transaction->category->name }} • {{ $transaction->account->name }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </span>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-exchange-alt text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nenhuma transação encontrada.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Orçamentos do Mês -->
        <div class="card-modern">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                    Orçamentos do Mês
                </h6>
                <a href="{{ route('budgets.index') }}" class="btn-gradient-primary text-sm px-4 py-2">
                    Ver Todos
                </a>
            </div>
            <div class="p-6">
                @forelse($currentBudgets as $budget)
                @php
                    $spent = $budget->spent_amount;
                    $percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
                    $progressColor = $percentage <= 50 ? 'bg-green-500' : ($percentage <= 80 ? 'bg-yellow-500' : 'bg-red-500');
                @endphp
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h6 class="text-gray-900 dark:text-white font-medium">{{ $budget->category->name }}</h6>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
                        <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-300" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>R$ {{ number_format($spent, 2, ',', '.') }}</span>
                        <span>R$ {{ number_format($budget->amount, 2, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-chart-pie text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nenhum orçamento ativo encontrado.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Seção de Metas e Categorias -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Metas Ativas -->
        <div class="card-modern">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-bullseye text-purple-500 mr-2"></i>
                    Metas Ativas
                </h6>
                <a href="{{ route('goals.index') }}" class="btn-gradient-primary text-sm px-4 py-2">
                    Ver Todas
                </a>
            </div>
            <div class="p-6">
                @forelse($activeGoals as $goal)
                @php
                    $percentage = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                @endphp
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-400 to-blue-500 flex items-center justify-center mr-3">
                                <i class="{{ $goal->icon }} text-white text-sm" style="color: white !important;"></i>
                            </div>
                            <h6 class="text-gray-900 dark:text-white font-medium">{{ $goal->name }}</h6>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2">
                        <div class="bg-gradient-to-r from-purple-500 to-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</span>
                        <span>R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Meta: {{ $goal->target_date->format('d/m/Y') }}</p>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-bullseye text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nenhuma meta ativa encontrada.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Top Categorias de Gastos -->
        <div class="card-modern">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h6 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-tags text-purple-500 mr-2"></i>
                    Maiores Gastos do Mês
                </h6>
            </div>
            <div class="p-6">
                @forelse($topExpenseCategories as $categoryData)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mr-3">
                            <i class="{{ $categoryData->category->icon ?? 'fas fa-tag' }}" style="color: inherit !important;"></i>
                        </div>
                        <div>
                            <h6 class="text-gray-900 dark:text-white font-medium">{{ $categoryData->category->name }}</h6>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-red-600">
                            R$ {{ number_format($categoryData->total_amount, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-tags text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nenhum gasto registrado este mês.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
// Verificar se Font Awesome está carregado
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar um pouco para o Font Awesome carregar
    setTimeout(() => {
        const iconElement = document.querySelector('h1 i.fa-tachometer-alt');
        if (iconElement) {
            const computedStyle = window.getComputedStyle(iconElement, ':before');
            if (computedStyle.content === 'none' || computedStyle.content === '""') {
                console.warn('Font Awesome não carregou, aplicando fallback');
                // Adicionar fallback se necessário
                iconElement.innerHTML = '📊';
                iconElement.classList.remove('fas', 'fa-tachometer-alt');
                iconElement.style.fontSize = '2rem';
            }
        }
    }, 1000);
});
// Função para criar gradientes
function createGradient(ctx, color1, color2) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, color1);
    gradient.addColorStop(1, color2);
    return gradient;
}

// Função para animação de números
function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = 'R$ ' + current.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }, 16);
}

// Configuração global do Chart.js para efeitos visuais
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6B7280';
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 24, 39, 0.95)';
Chart.defaults.plugins.tooltip.titleColor = '#F9FAFB';
Chart.defaults.plugins.tooltip.bodyColor = '#F9FAFB';
Chart.defaults.plugins.tooltip.borderColor = '#374151';
Chart.defaults.plugins.tooltip.borderWidth = 1;
Chart.defaults.plugins.tooltip.cornerRadius = 12;
Chart.defaults.plugins.tooltip.displayColors = true;
Chart.defaults.plugins.tooltip.padding = 12;

// Gráfico dos últimos 7 dias com efeitos visuais avançados
const ctx = document.getElementById('last7DaysChart').getContext('2d');
const chartData = @json($last7DaysData);

// Criar gradientes para receitas e despesas
const incomeGradient = createGradient(ctx, 'rgba(16, 185, 129, 0.8)', 'rgba(16, 185, 129, 0.1)');
const expenseGradient = createGradient(ctx, 'rgba(239, 68, 68, 0.8)', 'rgba(239, 68, 68, 0.1)');

const last7DaysChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('pt-BR', {weekday: 'short', day: '2-digit'});
        }),
        datasets: [
            {
                label: 'Receitas',
                data: chartData.map(item => item.income),
                backgroundColor: incomeGradient,
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                tension: 0.4,
                pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8,
                hoverBackgroundColor: 'rgba(16, 185, 129, 0.9)',
                hoverBorderColor: 'rgba(16, 185, 129, 1)',
                hoverBorderWidth: 3
            },
            {
                label: 'Despesas',
                data: chartData.map(item => item.expenses),
                backgroundColor: expenseGradient,
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                tension: 0.4,
                pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8,
                hoverBackgroundColor: 'rgba(239, 68, 68, 0.9)',
                hoverBorderColor: 'rgba(239, 68, 68, 1)',
                hoverBorderWidth: 3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart',
            onComplete: function() {
                // Animar números dos cards após o gráfico carregar
                document.querySelectorAll('.animate-number').forEach(el => {
                    const finalValue = parseFloat(el.dataset.value);
                    animateValue(el, 0, finalValue, 1500);
                });
            }
        },
        hover: {
            animationDuration: 300,
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#9CA3AF',
                    font: {
                        size: 12,
                        weight: '500'
                    }
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(156, 163, 175, 0.1)',
                    drawBorder: false
                },
                ticks: {
                    color: '#9CA3AF',
                    font: {
                        size: 12
                    },
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                    },
                    padding: 10
                }
            }
        },
        plugins: {
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                titleColor: '#F9FAFB',
                bodyColor: '#F9FAFB',
                borderColor: '#6366F1',
                borderWidth: 2,
                cornerRadius: 12,
                displayColors: true,
                padding: 16,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                filter: function(tooltipItem) {
                    return tooltipItem.parsed.y !== 0;
                },
                callbacks: {
                    title: function(context) {
                        const date = new Date(chartData[context[0].dataIndex].date);
                        return date.toLocaleDateString('pt-BR', {weekday: 'long', day: '2-digit', month: 'long'});
                    },
                    label: function(context) {
                        const value = context.parsed.y;
                        const emoji = context.dataset.label === 'Receitas' ? '💰' : '💸';
                        return emoji + ' ' + context.dataset.label + ': R$ ' + 
                               value.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    },
                    afterLabel: function(context) {
                        const data = chartData[context.dataIndex];
                        const balance = data.income - data.expenses;
                        const emoji = balance >= 0 ? '✅' : '⚠️';
                        const text = balance >= 0 ? 'Lucro' : 'Déficit';
                        return emoji + ' ' + text + ': R$ ' + Math.abs(balance).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }
                }
            },
            legend: {
                display: true,
                position: 'top',
                align: 'center',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 20,
                    font: {
                        size: 13,
                        weight: '600'
                    },
                    color: '#374151'
                }
            }
        },
        onHover: (event, activeElements) => {
            event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
        }
    },
    plugins: [{
        id: 'customCanvasBackgroundColor',
        beforeDraw: (chart, args, options) => {
            const {ctx} = chart;
            ctx.save();
            ctx.globalCompositeOperation = 'destination-over';
            const gradient = ctx.createLinearGradient(0, 0, 0, chart.height);
            gradient.addColorStop(0, 'rgba(255, 255, 255, 0.1)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0.05)');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, chart.width, chart.height);
            ctx.restore();
        }
    }]
});

// Efeito de parallax no scroll para os cards
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const cards = document.querySelectorAll('.stats-card');
    
    cards.forEach((card, index) => {
        const rate = scrolled * -0.1 * (index + 1);
        card.style.transform = `translateY(${rate}px)`;
    });
});

// Adicionar efeito de hover nos cards de estatísticas
document.addEventListener('DOMContentLoaded', function() {
    const statsCards = document.querySelectorAll('.stats-card');
    
    statsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
    });
    
    // Adicionar efeito de typing para títulos
    const titles = document.querySelectorAll('h1, h2, h3');
    titles.forEach(title => {
        const text = title.textContent;
        title.textContent = '';
        title.style.borderRight = '2px solid #6366F1';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                title.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 50);
            } else {
                setTimeout(() => {
                    title.style.borderRight = 'none';
                }, 500);
            }
        };
        
        // Iniciar typing quando o elemento entrar na tela
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(typeWriter, 200);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(title);
    });
    
    // Gráfico de Pizza das Contas
    const accountsCtx = document.getElementById('accountsChart');
    if (accountsCtx) {
        const accountsData = @json($activeAccounts->map(fn($account) => ['name' => $account->name, 'balance' => abs($account->balance), 'color' => $account->color ?? '#6366F1']));
        
        const accountsChart = new Chart(accountsCtx, {
            type: 'doughnut',
            data: {
                labels: accountsData.map(account => account.name),
                datasets: [{
                    data: accountsData.map(account => account.balance),
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderColor: [
                        'rgba(99, 102, 241, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)'
                    ],
                    borderWidth: 2,
                    hoverBorderWidth: 4,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#F9FAFB',
                        bodyColor: '#F9FAFB',
                        borderColor: '#6366F1',
                        borderWidth: 2,
                        cornerRadius: 12,
                        displayColors: true,
                        padding: 16,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return '💰 ' + context.label + ': R$ ' + 
                                       value.toLocaleString('pt-BR', {minimumFractionDigits: 2}) +
                                       ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                onHover: (event, activeElements) => {
                    event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
                }
            }
        });
    }
});

// Adicionar efeito de partículas ao fundo
function createParticles() {
    const canvas = document.createElement('canvas');
    canvas.id = 'particles-canvas';
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '-1';
    canvas.style.opacity = '0.1';
    document.body.appendChild(canvas);
    
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    
    const particles = [];
    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            vx: (Math.random() - 0.5) * 0.5,
            vy: (Math.random() - 0.5) * 0.5,
            size: Math.random() * 3 + 1,
            color: `hsl(${Math.random() * 60 + 240}, 70%, 60%)`
        });
    }
    
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(particle => {
            particle.x += particle.vx;
            particle.y += particle.vy;
            
            if (particle.x < 0 || particle.x > canvas.width) particle.vx *= -1;
            if (particle.y < 0 || particle.y > canvas.height) particle.vy *= -1;
            
            ctx.beginPath();
            ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            ctx.fillStyle = particle.color;
            ctx.fill();
        });
        
        requestAnimationFrame(animate);
    }
    
    animate();
    
    window.addEventListener('resize', () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
}

// Inicializar partículas após o carregamento da página
setTimeout(createParticles, 1000);
</script>
@endpush