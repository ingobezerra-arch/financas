@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Header com efeito de digitação -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2 typewriter"><i class="fas fa-bullseye mr-3"></i>Metas Financeiras</h1>
        <p class="text-gray-600 dark:text-gray-300 animate-fade-in">Alcance seus objetivos financeiros com planejamento e determinação</p>
        
        <div class="flex justify-between items-center mt-6">
            <div class="flex items-center space-x-4">
                <button class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                    <a href="{{ route('goals.create') }}" class="text-white text-decoration-none">
                        <i class="fas fa-plus mr-2"></i>Nova Meta
                    </a>
                </button>
            </div>
        </div>
    </div>

    <!-- Cards de estatísticas no topo -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-br from-blue-500 to-indigo-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total de Metas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $totalGoals }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-bullseye text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-green-500 to-emerald-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Ativas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $activeGoals }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-play text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-cyan-500 to-blue-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-cyan-100 text-sm font-medium">Concluídas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $completedGoals }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-yellow-500 to-orange-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Atrasadas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $overdueGoals }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-purple-500 to-pink-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-xs font-medium">Progresso Total</p>
                    <p class="text-white text-lg font-bold animate-currency" data-target="{{ $totalCurrentAmount }}">R$ 0,00</p>
                    <p class="text-purple-200 text-xs">de R$ {{ number_format($totalTargetAmount, 2, ',', '.') }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros modernos -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 animate-fade-in-up">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
            <i class="fas fa-filter mr-2 text-green-500"></i>
            Filtros
        </h3>
        <form method="GET" action="{{ route('goals.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluídas</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Atrasadas</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('goals.index') }}" class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center">
                        <i class="fas fa-times mr-2"></i>Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Metas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($goals as $index => $goal)
            <div class="goal-card bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                <!-- Header do Card -->
                <div class="goal-header p-6 bg-gradient-to-r" style="background: linear-gradient(135deg, {{ $goal->color }}20, {{ $goal->color }}40); border-left: 4px solid {{ $goal->color }};">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="goal-icon w-12 h-12 rounded-lg flex items-center justify-center" style="background: {{ $goal->color }}20; border: 2px solid {{ $goal->color }};">
                                <i class="{{ $goal->icon }} text-xl" style="color: {{ $goal->color }};"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 dark:text-white">{{ $goal->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($goal->status === 'completed')
                                        ✅ Concluída
                                    @elseif($goal->status === 'cancelled')
                                        ❌ Cancelada
                                    @elseif($goal->target_date < now())
                                        🚨 Atrasada
                                    @else
                                        🎯 Ativa
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="status-badge">
                            @if($goal->status === 'completed')
                                <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full flex items-center animate-bounce">
                                    <i class="fas fa-check mr-1"></i> Concluída
                                </span>
                            @elseif($goal->status === 'cancelled')
                                <span class="px-3 py-1 bg-gray-500 text-white text-xs rounded-full flex items-center">
                                    <i class="fas fa-times mr-1"></i> Cancelada
                                </span>
                            @elseif($goal->target_date < now())
                                <span class="px-3 py-1 bg-red-500 text-white text-xs rounded-full flex items-center animate-pulse">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Atrasada
                                </span>
                            @else
                                <span class="px-3 py-1 bg-blue-500 text-white text-xs rounded-full flex items-center">
                                    <i class="fas fa-play mr-1"></i> Ativa
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo do Card -->
                <div class="p-6">
                    @if($goal->description)
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($goal->description, 100) }}</p>
                    @endif
                    
                    <!-- Progresso -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Progresso</span>
                            <span class="text-sm font-semibold text-green-600">{{ number_format(($goal->current_amount / $goal->target_amount) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                            <div class="progress-bar h-full rounded-full transition-all duration-1000 ease-out bg-gradient-to-r from-green-400 to-green-600" 
                                 style="width: {{ min(($goal->current_amount / $goal->target_amount) * 100, 100) }}%; animation: progressAnimation 2s ease-out;"></div>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <!-- Valores -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Valor Atual</p>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400 animate-currency" data-target="{{ $goal->current_amount }}">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Meta</p>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400 animate-currency" data-target="{{ $goal->target_amount }}">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <!-- Informações adicionais -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                <i class="fas fa-calendar mr-2 text-blue-500"></i>
                                Prazo:
                            </span>
                            <span class="font-medium {{ $goal->target_date < now() && $goal->status !== 'completed' ? 'text-red-600' : 'text-gray-800 dark:text-white' }}">
                                {{ $goal->target_date->format('d/m/Y') }}
                                @if($goal->target_date < now() && $goal->status !== 'completed')
                                    (Atrasada)
                                @endif
                            </span>
                        </div>
                        
                        @if($goal->monthly_contribution)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                                    Contribuição:
                                </span>
                                <span class="font-medium text-purple-600 dark:text-purple-400">
                                    R$ {{ number_format($goal->monthly_contribution, 2, ',', '.') }}/mês
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Footer com Ações -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('goals.show', $goal) }}" class="px-3 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                            <i class="fas fa-eye mr-1"></i>Ver
                        </a>
                        <a href="{{ route('goals.edit', $goal) }}" class="px-3 py-2 bg-yellow-500 text-white text-xs rounded-lg hover:bg-yellow-600 transition-colors flex items-center">
                            <i class="fas fa-edit mr-1"></i>Editar
                        </a>
                        <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta meta?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition-colors flex items-center">
                                <i class="fas fa-trash mr-1"></i>Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                    <div class="animate-float">
                        <i class="fas fa-bullseye text-8xl text-gray-300 dark:text-gray-600 mb-6"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Nenhuma meta encontrada</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">Comece criando sua primeira meta financeira e acompanhe seu progresso!</p>
                    <a href="{{ route('goals.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i>Criar primeira meta
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginação -->
    @if($goals->hasPages())
        <div class="flex justify-center mt-8 bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
            <div class="pagination-wrapper">
                {{ $goals->links() }}
            </div>
        </div>
    @endif
</div>

<style>
.goal-icon {
    transition: all 0.3s ease;
}

.goal-icon:hover {
    transform: scale(1.1) rotate(5deg);
}

.typewriter {
    overflow: hidden;
    border-right: 0.15em solid #10b981;
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
    50% { border-color: #10b981; }
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

.goal-card {
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
    background: linear-gradient(45deg, #10b981, #059669);
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

        // Observar cards de metas
        const goalCards = document.querySelectorAll('.goal-card');
        goalCards.forEach(card => observer.observe(card));

        // Sistema de tema escuro dinâmico
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        }

        // Criar partículas de fundo
        createParticles();
        
        // Auto-submit do filtro quando mudar
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });
        
        // Observer para elementos dinâmicos
        const mutationObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1 && node.querySelector) {
                        const newStatsCards = node.querySelectorAll('.stats-card');
                        newStatsCards.forEach(card => observer.observe(card));
                        
                        const newGoalCards = node.querySelectorAll('.goal-card');
                        newGoalCards.forEach(card => observer.observe(card));
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

        for (let i = 0; i < 12; i++) {
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
        
        if (e.target.closest('.goal-card')) {
            const card = e.target.closest('.goal-card');
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
        
        if (e.target.closest('.goal-card')) {
            const card = e.target.closest('.goal-card');
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        }
    });
</script>
@endsection