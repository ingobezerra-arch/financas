@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Header com efeito de digitação -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2 typewriter"><i class="fas fa-chart-pie mr-3"></i>Orçamentos</h1>
        <p class="text-gray-600 dark:text-gray-300 animate-fade-in">Controle seus gastos com orçamentos inteligentes</p>
        
        <div class="flex justify-between items-center mt-6">
            <div class="flex items-center space-x-4">
                <button class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                    <a href="{{ route('budgets.create') }}" class="text-white text-decoration-none">
                        <i class="fas fa-plus mr-2"></i>Novo Orçamento
                    </a>
                </button>
            </div>
        </div>
    </div>

    <!-- Cards de estatísticas no topo -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-br from-blue-500 to-indigo-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Orçamentos Ativos</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $activeBudgets }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-chart-pie text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-green-500 to-emerald-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Orçado</p>
                    <p class="text-white text-2xl font-bold animate-currency" data-target="{{ $totalBudget }}">R$ 0,00</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-bullseye text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-yellow-500 to-orange-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Total Gasto</p>
                    <p class="text-white text-2xl font-bold animate-currency" data-target="{{ $totalSpent }}">R$ 0,00</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-red-500 to-pink-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Estourados</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $overBudgetCount }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 animate-slide-down" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 animate-slide-down" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Filtros modernos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 animate-fade-in-up">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-filter mr-2 text-indigo-500"></i>
            Filtros
        </h3>
        <form method="GET" action="{{ route('budgets.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Categoria</label>
                    <select id="category_id" name="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Todas as categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Período</label>
                    <select id="period" name="period" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                        <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                        <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Anual</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirado</option>
                        <option value="over_budget" {{ request('status') == 'over_budget' ? 'selected' : '' }}>Estourado</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('budgets.index') }}" class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                        <i class="fas fa-times mr-2"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Orçamentos -->
    @if($budgets->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($budgets as $index => $budget)
                @php
                    $percentageUsed = $budget->percentage_used;
                    $isOverBudget = $budget->is_over_budget;
                    $shouldAlert = $budget->should_alert;
                    $isExpired = $budget->end_date < now();
                @endphp
                <div class="budget-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                    <!-- Header do Card -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="budget-icon w-12 h-12 rounded-lg bg-gradient-to-r" style="background: linear-gradient(135deg, {{ $budget->category->color }}20, {{ $budget->category->color }}40); border: 2px solid {{ $budget->category->color }};">
                                @if($budget->category->icon)
                                    <i class="{{ $budget->category->icon }} text-xl" style="color: {{ $budget->category->color }};"></i>
                                @else
                                    <i class="fas fa-chart-pie text-xl" style="color: {{ $budget->category->color }};"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 dark:text-white">{{ $budget->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $budget->category->name }}</p>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="status-badge">
                            @if(!$budget->is_active)
                                <span class="px-3 py-1 bg-gray-500 text-white text-xs rounded-full flex items-center animate-pulse">
                                    <i class="fas fa-pause mr-1"></i> Inativo
                                </span>
                            @elseif($isExpired)
                                <span class="px-3 py-1 bg-gray-700 text-white text-xs rounded-full flex items-center animate-pulse">
                                    <i class="fas fa-calendar-times mr-1"></i> Expirado
                                </span>
                            @elseif($isOverBudget)
                                <span class="px-3 py-1 bg-red-500 text-white text-xs rounded-full flex items-center animate-bounce">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Estourado
                                </span>
                            @elseif($shouldAlert)
                                <span class="px-3 py-1 bg-yellow-500 text-white text-xs rounded-full flex items-center animate-pulse">
                                    <i class="fas fa-exclamation mr-1"></i> Atenção
                                </span>
                            @else
                                <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full flex items-center">
                                    <i class="fas fa-check mr-1"></i> OK
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Descrição -->
                    @if($budget->description)
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($budget->description, 80) }}</p>
                    @endif
                    
                    <!-- Valores -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Orçado</p>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400 animate-currency" data-target="{{ $budget->amount }}">R$ {{ number_format($budget->amount, 2, ',', '.') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Gasto</p>
                            <p class="text-lg font-bold {{ $isOverBudget ? 'text-red-600' : 'text-orange-600' }} animate-currency" data-target="{{ $budget->spent }}">R$ {{ number_format($budget->spent, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <!-- Barra de Progresso -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Progresso</span>
                            <span class="text-sm font-semibold {{ $isOverBudget ? 'text-red-600' : ($shouldAlert ? 'text-yellow-600' : 'text-green-600') }}">{{ number_format($percentageUsed, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="progress-bar h-full rounded-full transition-all duration-1000 ease-out {{ $isOverBudget ? 'bg-gradient-to-r from-red-500 to-red-600' : ($shouldAlert ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : 'bg-gradient-to-r from-green-500 to-green-600') }}" 
                                 style="width: {{ min($percentageUsed, 100) }}%; animation: progressAnimation 2s ease-out;"></div>
                        </div>
                        @if($isOverBudget)
                            <p class="text-xs text-red-600 mt-1 animate-pulse">
                                Excesso: R$ {{ number_format($budget->spent - $budget->amount, 2, ',', '.') }}
                            </p>
                        @else
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Restante: R$ {{ number_format($budget->remaining_amount, 2, ',', '.') }}
                            </p>
                        @endif
                    </div>
                    
                    <!-- Período e Ações -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full">
                                @switch($budget->period)
                                    @case('weekly') 📅 Semanal @break
                                    @case('monthly') 📅 Mensal @break
                                    @case('quarterly') 📅 Trimestral @break
                                    @case('yearly') 📅 Anual @break
                                @endswitch
                            </span>
                        </div>
                        
                        <!-- Dropdown de Ações -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-10">
                                <a href="{{ route('budgets.show', $budget) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <i class="fas fa-eye mr-2"></i>Visualizar
                                </a>
                                <form action="{{ route('budgets.toggle', $budget) }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                        @if($budget->is_active)
                                            <i class="fas fa-pause mr-2"></i>Pausar
                                        @else
                                            <i class="fas fa-play mr-2"></i>Ativar
                                        @endif
                                    </button>
                                </form>
                                <a href="{{ route('budgets.edit', $budget) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <i class="fas fa-edit mr-2"></i>Editar
                                </a>
                                <hr class="border-gray-200 dark:border-gray-600">
                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="block" onsubmit="return confirm('Tem certeza que deseja excluir este orçamento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                        <i class="fas fa-trash mr-2"></i>Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datas -->
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>📅 Início: {{ $budget->start_date->format('d/m/Y') }}</span>
                            <span>📅 Fim: {{ $budget->end_date->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
                                            </small>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('budgets.show', $budget) }}">
                                                        <i class="fas fa-eye"></i> Visualizar
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('budgets.edit', $budget) }}">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a></li>
        
        <!-- Paginação -->
        <div class="flex justify-between items-center mt-8 bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <div class="text-gray-600 dark:text-gray-400">
                Mostrando {{ $budgets->firstItem() }} a {{ $budgets->lastItem() }} 
                de {{ $budgets->total() }} orçamentos
            </div>
            <div class="pagination-wrapper">
                {{ $budgets->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="animate-float">
                <i class="fas fa-chart-pie text-8xl text-gray-300 dark:text-gray-600 mb-6"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Nenhum orçamento encontrado</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">Crie orçamentos para controlar seus gastos por categoria e alcançar suas metas financeiras.</p>
            <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                <i class="fas fa-plus mr-2"></i>Criar Primeiro Orçamento
            </a>
        </div>
    @endif
</div>

<style>
.budget-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.budget-icon:hover {
    transform: scale(1.1) rotate(5deg);
}

.typewriter {
    overflow: hidden;
    border-right: 0.15em solid #3b82f6;
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
    50% { border-color: #3b82f6; }
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

.budget-card {
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
    background: linear-gradient(45deg, #3b82f6, #6366f1);
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

        // Observar cards de orçamento
        const budgetCards = document.querySelectorAll('.budget-card');
        budgetCards.forEach(card => observer.observe(card));

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
                        
                        const newBudgetCards = node.querySelectorAll('.budget-card');
                        newBudgetCards.forEach(card => observer.observe(card));
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
        
        if (e.target.closest('.budget-card')) {
            const card = e.target.closest('.budget-card');
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
        
        if (e.target.closest('.budget-card')) {
            const card = e.target.closest('.budget-card');
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        }
    });
</script>
@endsection