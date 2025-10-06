@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Header com efeito de digitação -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2 typewriter"><i class="fas fa-chart-line mr-3"></i>Relatórios Financeiros</h1>
        <p class="text-gray-600 dark:text-gray-300 animate-fade-in">Analise suas finanças com gráficos e relatórios detalhados</p>
        
        <div class="flex justify-between items-center mt-6">
            <div class="flex items-center space-x-4">
                <select id="periodSelect" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                    <option value="current_month" {{ $period == 'current_month' ? 'selected' : '' }}>Mês Atual</option>
                    <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>Mês Passado</option>
                    <option value="last_7_days" {{ $period == 'last_7_days' ? 'selected' : '' }}>Últimos 7 dias</option>
                    <option value="last_30_days" {{ $period == 'last_30_days' ? 'selected' : '' }}>Últimos 30 dias</option>
                    <option value="last_90_days" {{ $period == 'last_90_days' ? 'selected' : '' }}>Últimos 90 dias</option>
                    <option value="current_year" {{ $period == 'current_year' ? 'selected' : '' }}>Ano Atual</option>
                    <option value="last_year" {{ $period == 'last_year' ? 'selected' : '' }}>Ano Passado</option>
                </select>
                <button type="button" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- Resumo Período -->
    <div class="bg-gradient-to-r from-purple-100 to-indigo-100 dark:from-purple-800 dark:to-indigo-800 rounded-xl p-6 mb-8 animate-fade-in-up">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-200 mb-1">
                    <i class="fas fa-calendar mr-2"></i>Período: {{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}
                </h3>
                <p class="text-purple-600 dark:text-purple-300 text-sm">{{ $startDate->diffInDays($endDate) + 1 }} dias de análise</p>
            </div>
            <div class="text-right">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de estatísticas no topo -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-br from-green-500 to-emerald-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Receitas</p>
                    <p class="text-white text-2xl font-bold animate-currency" data-target="{{ $summary['total_income'] }}">R$ 0,00</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-arrow-up text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-red-500 to-pink-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Total Despesas</p>
                    <p class="text-white text-2xl font-bold animate-currency" data-target="{{ $summary['total_expense'] }}">R$ 0,00</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-arrow-down text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br {{ $summary['balance'] >= 0 ? 'from-blue-500 to-indigo-600' : 'from-yellow-500 to-orange-600' }} p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-opacity-80 text-sm font-medium">Saldo do Período</p>
                    <p class="text-white text-2xl font-bold animate-currency" data-target="{{ $summary['balance'] }}">R$ 0,00</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-balance-scale text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-cyan-500 to-teal-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-cyan-100 text-xs font-medium">Transações</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $summary['transaction_count'] }}">0</p>
                    <p class="text-cyan-200 text-xs">Média: R$ {{ number_format($summary['average_daily_spending'], 2, ',', '.') }}/dia</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-exchange-alt text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegação de Relatórios -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 animate-fade-in-up">
        <div class="flex justify-center">
            <div class="flex flex-wrap justify-center space-x-1 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg" id="reportTabs" role="tablist">
                <button class="nav-tab active px-6 py-3 rounded-lg text-sm font-medium transition-all duration-300" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                    <i class="fas fa-chart-pie mr-2"></i>Visão Geral
                </button>
                <button class="nav-tab px-6 py-3 rounded-lg text-sm font-medium transition-all duration-300" id="trends-tab" data-bs-toggle="pill" data-bs-target="#trends" type="button" role="tab">
                    <i class="fas fa-chart-line mr-2"></i>Tendências
                </button>
                <button class="nav-tab px-6 py-3 rounded-lg text-sm font-medium transition-all duration-300" id="categories-tab" data-bs-toggle="pill" data-bs-target="#categories" type="button" role="tab">
                    <i class="fas fa-tags mr-2"></i>Categorias
                </button>
                <button class="nav-tab px-6 py-3 rounded-lg text-sm font-medium transition-all duration-300" id="accounts-tab" data-bs-toggle="pill" data-bs-target="#accounts" type="button" role="tab">
                    <i class="fas fa-wallet mr-2"></i>Contas
                </button>
            </div>
        </div>
    </div>

    <!-- Conteúdo das Abas -->
    <div class="tab-content" id="reportTabsContent">
        <!-- Visão Geral -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="chart-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Receitas vs Despesas
                        </h3>
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                            💰
                        </div>
                    </div>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-blue-500"></i>Gastos por Categoria
                        </h3>
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                            📊
                        </div>
                    </div>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tendências -->
        <div class="tab-pane fade" id="trends" role="tabpanel">
            <div class="chart-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>Tendência Mensal - Últimos 12 Meses
                    </h3>
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                        📈
                    </div>
                </div>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Categorias Detalhadas -->
        <div class="tab-pane fade" id="categories" role="tabpanel">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="chart-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                                <i class="fas fa-chart-doughnut mr-2 text-indigo-500"></i>Distribuição por Categoria
                            </h3>
                            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-800 rounded-lg flex items-center justify-center">
                                🍩
                            </div>
                        </div>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="categoryDetailChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                                <i class="fas fa-list mr-2 text-orange-500"></i>Top Categorias
                            </h3>
                            <div class="w-8 h-8 bg-orange-100 dark:bg-orange-800 rounded-lg flex items-center justify-center">
                                🏆
                            </div>
                        </div>
                        <div id="categoryList" class="space-y-3">
                            <!-- Lista será preenchida via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contas -->
        <div class="tab-pane fade" id="accounts" role="tabpanel">
            <div class="chart-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-teal-500"></i>Distribuição de Saldo por Conta
                    </h3>
                    <div class="w-8 h-8 bg-teal-100 dark:bg-teal-800 rounded-lg flex items-center justify-center">
                        💳
                    </div>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="accountChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Links Rápidos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mt-8">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                <i class="fas fa-link mr-2 text-purple-500"></i>Relatórios Detalhados
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('reports.transactions') }}" class="report-link-card bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-800 dark:to-blue-700 p-6 rounded-xl text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                <div class="mb-3">
                    <i class="fas fa-exchange-alt text-3xl text-blue-600 dark:text-blue-300"></i>
                </div>
                <h4 class="font-bold text-blue-800 dark:text-blue-200 mb-2">Relatório de Transações</h4>
                <p class="text-blue-600 dark:text-blue-300 text-sm">Análise detalhada de todas as transações</p>
            </a>
            
            <a href="{{ route('reports.budgets') }}" class="report-link-card bg-gradient-to-br from-green-100 to-green-200 dark:from-green-800 dark:to-green-700 p-6 rounded-xl text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                <div class="mb-3">
                    <i class="fas fa-chart-pie text-3xl text-green-600 dark:text-green-300"></i>
                </div>
                <h4 class="font-bold text-green-800 dark:text-green-200 mb-2">Relatório de Orçamentos</h4>
                <p class="text-green-600 dark:text-green-300 text-sm">Performance dos seus orçamentos</p>
            </a>
            
            <a href="{{ route('reports.goals') }}" class="report-link-card bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-800 dark:to-purple-700 p-6 rounded-xl text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                <div class="mb-3">
                    <i class="fas fa-bullseye text-3xl text-purple-600 dark:text-purple-300"></i>
                </div>
                <h4 class="font-bold text-purple-800 dark:text-purple-200 mb-2">Relatório de Metas</h4>
                <p class="text-purple-600 dark:text-purple-300 text-sm">Progresso das suas metas financeiras</p>
            </a>
            
            <button onclick="exportReport()" class="report-link-card bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 p-6 rounded-xl text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                <div class="mb-3">
                    <i class="fas fa-file-pdf text-3xl text-gray-600 dark:text-gray-300"></i>
                </div>
                <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Exportar PDF</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Baixe o relatório completo</p>
            </button>
        </div>
    </div>
</div>

<style>
.typewriter {
    overflow: hidden;
    border-right: 0.15em solid #8b5cf6;
    white-space: nowrap;
    margin: 0 auto;
    letter-spacing: 0.15em;
    animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
}

@keyframes typing {
    from { width: 0; }
    to { width: 100%; }
}

@keyframes blink-caret {
    from, to { border-color: transparent; }
    50% { border-color: #8b5cf6; }
}

.stats-card {
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.6s;
    opacity: 0;
}

.stats-card:hover::before {
    animation: shimmer 1.5s ease-in-out;
    opacity: 1;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.nav-tab {
    color: #6b7280;
    background: transparent;
    border: none;
    cursor: pointer;
}

.nav-tab:hover {
    color: #8b5cf6;
    background: rgba(139, 92, 246, 0.1);
}

.nav-tab.active {
    color: white;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3);
}

.chart-card {
    position: relative;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.chart-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(59, 130, 246, 0.1));
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
    border-radius: 12px;
}

.chart-card:hover::before {
    opacity: 1;
}

.report-link-card {
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
}

.report-link-card:hover {
    text-decoration: none;
}

/* Sistema de partículas de fundo */
.bg-particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: linear-gradient(45deg, #8b5cf6, #6366f1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.7;
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
        opacity: 1;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados dos gráficos vindos do backend - uso de JSON encode para evitar problemas com linter
    const chartDataString = '{!! json_encode($chartData) !!}';
    const chartData = JSON.parse(chartDataString);
    
    // Animação de contadores
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                start = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(start);
        }, 16);
    }

    // Animação de valores monetários
    function animateCurrency(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                start = target;
                clearInterval(timer);
            }
            element.textContent = 'R$ ' + start.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }, 16);
    }

    // Observer para animar elementos quando entrarem na tela
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Animar contadores
                const counter = entry.target.querySelector('.animate-count');
                if (counter) {
                    const target = parseInt(counter.dataset.target);
                    animateCounter(counter, target);
                }
                
                // Animar valores monetários
                const currency = entry.target.querySelector('.animate-currency');
                if (currency) {
                    const target = parseFloat(currency.dataset.target);
                    animateCurrency(currency, target);
                }
                
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observar cards de estatísticas
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(card => observer.observe(card));

    // Sistema de tema escuro dinâmico
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }

    // Criar partículas de fundo
    createParticles();
    
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
        const url = '{{ route("reports.index") }}';
        window.location.href = url + '?period=' + newPeriod;
    });
});

function fillCategoryList(categoryData) {
    const categoryList = document.getElementById('categoryList');
    let html = '';
    
    categoryData.labels.forEach((label, index) => {
        const value = categoryData.data[index];
        const color = categoryData.backgroundColor[index];
        
        html += `
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 rounded-full" style="background-color: ${color};"></div>
                    <span class="text-gray-800 dark:text-gray-200 font-medium">${label}</span>
                </div>
                <span class="text-gray-600 dark:text-gray-400 font-bold">R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span>
            </div>
        `;
    });
    
    categoryList.innerHTML = html;
}

function exportReport() {
    // Implementar exportação
    alert('Funcionalidade de exportação em desenvolvimento');
}

// Criar sistema de partículas
function createParticles() {
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'bg-particles';
    document.body.appendChild(particlesContainer);

    for (let i = 0; i < 15; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 6 + 's';
        particle.style.animationDuration = (Math.random() * 4 + 4) + 's';
        particlesContainer.appendChild(particle);
    }
}

// Tornar gráficos responsivos
window.addEventListener('resize', function() {
    Chart.helpers.each(Chart.instances, function(instance) {
        instance.resize();
    });
});
</script>
@endsection