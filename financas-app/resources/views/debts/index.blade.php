@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Header com efeito de digitação -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2 typewriter"><i class="fas fa-credit-card mr-3"></i>Gerenciamento de Dívidas</h1>
        <p class="text-gray-600 dark:text-gray-300 animate-fade-in">Controle suas dívidas e crie estratégias para quitá-las mais rápido</p>
        
        <div class="flex justify-between items-center mt-6">
            <div class="flex items-center space-x-4">
                <button class="px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                    <a href="{{ route('debts.create') }}" class="text-white text-decoration-none">
                        <i class="fas fa-plus mr-2"></i>Nova Dívida
                    </a>
                </button>
            </div>
        </div>
    </div>

    <!-- Cards de estatísticas no topo -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-br from-blue-500 to-indigo-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total de Dívidas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $stats['total_debts'] }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-list-alt text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-yellow-500 to-orange-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Dívidas Ativas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $stats['active_debts'] }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-exclamation-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-red-500 to-pink-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-xs font-medium">Total Devedor</p>
                    <p class="text-white text-lg font-bold animate-currency" data-target="{{ $stats['total_balance'] }}">R$ 0,00</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-cyan-500 to-blue-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-cyan-100 text-xs font-medium">Pagamentos Mínimos</p>
                    <p class="text-white text-lg font-bold animate-currency" data-target="{{ $stats['total_minimum_payments'] }}">R$ 0,00</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-calendar-week text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-purple-500 to-indigo-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Em Atraso</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $stats['overdue_debts'] }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-green-500 to-emerald-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Quitadas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $stats['paid_debts'] }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros modernos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 animate-fade-in-up">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-filter mr-2 text-red-500"></i>
            Filtros
        </h3>
        <form method="GET" action="{{ route('debts.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" 
                           id="search" name="search" value="{{ request('search') }}" placeholder="Nome, credor ou descrição...">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Quitada</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Em Atraso</option>
                        <option value="negotiated" {{ request('status') == 'negotiated' ? 'selected' : '' }}>Negociada</option>
                    </select>
                </div>
                <div>
                    <label for="debt_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white" id="debt_type" name="debt_type">
                        <option value="">Todos</option>
                        <option value="credit_card" {{ request('debt_type') == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="loan" {{ request('debt_type') == 'loan' ? 'selected' : '' }}>Empréstimo</option>
                        <option value="financing" {{ request('debt_type') == 'financing' ? 'selected' : '' }}>Financiamento</option>
                        <option value="invoice" {{ request('debt_type') == 'invoice' ? 'selected' : '' }}>Fatura</option>
                        <option value="other" {{ request('debt_type') == 'other' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('debts.index') }}" class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                        <i class="fas fa-times mr-2"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Dívidas -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Suas Dívidas ({{ $debts->total() }})</h3>
        </div>
        
        @if($debts->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
                @foreach($debts as $index => $debt)
                    <div class="debt-card bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transform hover:scale-105 transition-all duration-300 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                        <!-- Header do Card -->
                        <div class="debt-header p-6 bg-gradient-to-r {{ $debt->status === 'paid' ? 'from-green-100 to-green-200 dark:from-green-800 dark:to-green-700' : ($debt->status === 'overdue' ? 'from-red-100 to-red-200 dark:from-red-800 dark:to-red-700' : 'from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600') }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="debt-icon w-12 h-12 rounded-lg bg-red-500 bg-opacity-20 border-2 border-red-500 flex items-center justify-center">
                                        @switch($debt->debt_type)
                                            @case('credit_card')
                                                <i class="fas fa-credit-card text-red-500 text-xl"></i>
                                                @break
                                            @case('loan')
                                                <i class="fas fa-hand-holding-usd text-red-500 text-xl"></i>
                                                @break
                                            @case('financing')
                                                <i class="fas fa-home text-red-500 text-xl"></i>
                                                @break
                                            @case('invoice')
                                                <i class="fas fa-file-invoice text-red-500 text-xl"></i>
                                                @break
                                            @default
                                                <i class="fas fa-money-bill-wave text-red-500 text-xl"></i>
                                        @endswitch
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 dark:text-white">{{ $debt->name }}</h3>
                                        @if($debt->creditor)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $debt->creditor }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Status Badge -->
                                <div class="status-badge">
                                    @switch($debt->status)
                                        @case('active')
                                            <span class="px-3 py-1 bg-yellow-500 text-white text-xs rounded-full flex items-center">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Ativa
                                            </span>
                                            @break
                                        @case('paid')
                                            <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full flex items-center animate-bounce">
                                                <i class="fas fa-check-circle mr-1"></i> Quitada
                                            </span>
                                            @break
                                        @case('overdue')
                                            <span class="px-3 py-1 bg-red-500 text-white text-xs rounded-full flex items-center animate-pulse">
                                                <i class="fas fa-clock mr-1"></i> Em Atraso
                                            </span>
                                            @break
                                        @case('negotiated')
                                            <span class="px-3 py-1 bg-blue-500 text-white text-xs rounded-full flex items-center">
                                                <i class="fas fa-handshake mr-1"></i> Negociada
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                            
                            <!-- Tipo de Dívida -->
                            <div class="flex items-center space-x-2">
                                @switch($debt->debt_type)
                                    @case('credit_card')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">💳 Cartão de Crédito</span>
                                        @break
                                    @case('loan')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">💰 Empréstimo</span>
                                        @break
                                    @case('financing')
                                        <span class="px-3 py-1 bg-cyan-100 text-cyan-700 text-xs rounded-full">🏠 Financiamento</span>
                                        @break
                                    @case('invoice')
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">🧾 Fatura</span>
                                        @break
                                    @default
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">📜 Outro</span>
                                @endswitch
                                
                                @if($debt->is_overdue)
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full animate-pulse">⚠️ Vencida</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Conteúdo do Card -->
                        <div class="p-6">
                            <!-- Valores principais -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Saldo Atual</p>
                                    <p class="text-lg font-bold text-red-600 dark:text-red-400 animate-currency" data-target="{{ $debt->current_balance }}">R$ {{ number_format($debt->current_balance, 2, ',', '.') }}</p>
                                    @if($debt->original_amount > $debt->current_balance)
                                        <p class="text-xs text-gray-400">de R$ {{ number_format($debt->original_amount, 2, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pagamento Mínimo</p>
                                    <p class="text-lg font-bold text-orange-600 dark:text-orange-400 animate-currency" data-target="{{ $debt->minimum_payment }}">R$ {{ number_format($debt->minimum_payment, 2, ',', '.') }}</p>
                                </div>
                            </div>
                            
                            <!-- Taxa de Juros -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Taxa de Juros:</span>
                                <span class="px-2 py-1 text-xs rounded-full {{ $debt->interest_rate > 5 ? 'bg-red-100 text-red-700' : ($debt->interest_rate > 2 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                    {{ number_format($debt->interest_rate, 2) }}% a.m.
                                </span>
                            </div>
                            
                            <!-- Progresso de Pagamento -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Progresso de Pagamento</span>
                                    <span class="text-sm font-semibold text-green-600">{{ number_format($debt->percentage_paid, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                    <div class="progress-bar h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-green-400 to-green-600" 
                                         style="width: {{ $debt->percentage_paid }}%; animation: progressAnimation 2s ease-out;"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($debt->percentage_paid, 1) }}% pago</p>
                            </div>
                        </div>
                        
                        <!-- Footer com Ações -->
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center">
                                <a href="{{ route('debts.show', $debt) }}" class="px-3 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </a>
                                <a href="{{ route('debts.edit', $debt) }}" class="px-3 py-2 bg-gray-500 text-white text-xs rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                                <a href="{{ route('debts.simulate', $debt) }}" class="px-3 py-2 bg-purple-500 text-white text-xs rounded-lg hover:bg-purple-600 transition-colors flex items-center">
                                    <i class="fas fa-calculator mr-1"></i>Simular
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Paginação -->
            <div class="flex justify-center p-6 border-t border-gray-200 dark:border-gray-700">
                {{ $debts->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="animate-float">
                    <i class="fas fa-credit-card text-8xl text-gray-300 dark:text-gray-600 mb-6"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Nenhuma dívida encontrada</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">Você não possui dívidas cadastradas ou nenhuma corresponde aos filtros aplicados.</p>
                <a href="{{ route('debts.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-plus mr-2"></i>Cadastrar Primeira Dívida
                </a>
            </div>
        @endif
    </div>

    <!-- Links Rápidos -->
    @if($stats['active_debts'] > 1)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center transform hover:scale-105 transition-all duration-300">
                <div class="mb-4">
                    <i class="fas fa-magic text-4xl text-blue-500 mb-3 animate-float"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Criar Plano de Pagamento</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Use estratégias como Bola de Neve ou Avalanche para quitar suas dívidas mais rapidamente.</p>
                <a href="{{ route('payment-plans.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-calendar-alt mr-2"></i>Criar Plano
                </a>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center transform hover:scale-105 transition-all duration-300">
                <div class="mb-4">
                    <i class="fas fa-chart-line text-4xl text-green-500 mb-3 animate-float"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Comparar Estratégias</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Compare diferentes estratégias de pagamento e veja qual economiza mais tempo e dinheiro.</p>
                <form action="{{ route('payment-plans.compare-strategies') }}" method="POST" style="display: inline;">
                    @csrf
                    @foreach(auth()->user()->debts()->active()->get() as $debt)
                        <input type="hidden" name="debt_ids[]" value="{{ $debt->id }}">
                    @endforeach
                    <input type="hidden" name="monthly_budget" value="1000">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-balance-scale mr-2"></i>Comparar
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<style>
.debt-icon {
    transition: all 0.3s ease;
}

.debt-icon:hover {
    transform: scale(1.1) rotate(5deg);
}

.typewriter {
    overflow: hidden;
    border-right: 0.15em solid #ef4444;
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
    50% { border-color: #ef4444; }
}

@keyframes progressAnimation {
    from { width: 0%; }
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

.debt-card {
    transform: translateY(20px);
    opacity: 0;
    animation: slideUp 0.6s ease-out forwards;
}

@keyframes slideUp {
    to {
        transform: translateY(0);
        opacity: 1;
    }
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
    background: linear-gradient(45deg, #ef4444, #dc2626);
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

<script>
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

    document.addEventListener('DOMContentLoaded', () => {
        // Observar cards de estatísticas
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach(card => observer.observe(card));

        // Observar cards de dívidas
        const debtCards = document.querySelectorAll('.debt-card');
        debtCards.forEach(card => observer.observe(card));

        // Sistema de tema escuro dinâmico
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        }

        // Criar partículas de fundo
        createParticles();
        
        // Observer para elementos dinâmicos
        const mutationObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1 && node.querySelector) {
                        const newStatsCards = node.querySelectorAll('.stats-card');
                        newStatsCards.forEach(card => observer.observe(card));
                        
                        const newDebtCards = node.querySelectorAll('.debt-card');
                        newDebtCards.forEach(card => observer.observe(card));
                    }
                });
            });
        });

        mutationObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

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

    // Efeitos de hover aprimorados
    document.addEventListener('mouseover', (e) => {
        if (e.target.closest('.stats-card')) {
            const card = e.target.closest('.stats-card');
            card.style.transform = 'translateY(-8px) scale(1.05)';
            card.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
        }
        
        if (e.target.closest('.debt-card')) {
            const card = e.target.closest('.debt-card');
            card.style.transform = 'translateY(-10px) scale(1.05)';
            card.style.boxShadow = '0 25px 50px rgba(0,0,0,0.15)';
        }
    });

    document.addEventListener('mouseout', (e) => {
        if (e.target.closest('.stats-card')) {
            const card = e.target.closest('.stats-card');
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        }
        
        if (e.target.closest('.debt-card')) {
            const card = e.target.closest('.debt-card');
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        }
    });
</script>
@endsection