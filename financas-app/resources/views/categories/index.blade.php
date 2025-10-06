@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Header com efeito de digitação -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2 typewriter"><i class="fas fa-tags mr-3"></i>Minhas Categorias</h1>
        <p class="text-gray-600 dark:text-gray-300 animate-fade-in">Organize suas finanças com categorias personalizadas</p>
        
        <div class="flex justify-between items-center mt-6">
            <div class="flex items-center space-x-4">
                <button class="px-6 py-3 bg-gradient-to-r from-purple-500 to-blue-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                    <a href="{{ route('categories.create') }}" class="text-white text-decoration-none">
                        <i class="fas fa-plus mr-2"></i>Nova Categoria
                    </a>
                </button>
            </div>
        </div>
    </div>

    <!-- Cards de estatísticas no topo -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stats-card bg-gradient-to-br from-blue-500 to-purple-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300" data-count="{{ $incomeCategories->count() + $expenseCategories->count() + $inactiveCategories->count() }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total de Categorias</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $incomeCategories->count() + $expenseCategories->count() + $inactiveCategories->count() }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-tags text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-green-500 to-emerald-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300" data-count="{{ $incomeCategories->count() }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Receitas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $incomeCategories->count() }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-arrow-up text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-red-500 to-pink-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300" data-count="{{ $expenseCategories->count() }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Despesas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $expenseCategories->count() }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-arrow-down text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card bg-gradient-to-br from-gray-500 to-slate-600 p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300" data-count="{{ $inactiveCategories->count() }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-100 text-sm font-medium">Inativas</p>
                    <p class="text-white text-2xl font-bold animate-count" data-target="{{ $inactiveCategories->count() }}">0</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <i class="fas fa-pause text-white text-xl"></i>
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

    <!-- Grid principal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Categorias de Receita -->
        <div class="category-section animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-l-4 border-green-500">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-arrow-up mr-3 text-2xl"></i>
                        Categorias de Receita ({{ $incomeCategories->count() }})
                    </h3>
                </div>
                
                <div class="p-0">
                    @if($incomeCategories->count() > 0)
                        <div class="space-y-0">
                            @foreach($incomeCategories as $index => $category)
                                <div class="category-item border-b border-gray-100 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 transform hover:scale-[1.02]" style="animation-delay: {{ $index * 0.1 }}s;">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="category-icon-modern" style="background: linear-gradient(135deg, {{ $category->color }}20, {{ $category->color }}40); border: 2px solid {{ $category->color }};">
                                                @if($category->icon)
                                                    <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }};"></i>
                                                @else
                                                    <i class="fas fa-tag text-xl" style="color: {{ $category->color }};"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 dark:text-white">{{ $category->name }}</h4>
                                                @if($category->description)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($category->description, 50) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-10">
                                                <a href="{{ route('categories.show', $category) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <i class="fas fa-eye mr-2"></i>Visualizar
                                                </a>
                                                <a href="{{ route('categories.edit', $category) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <i class="fas fa-edit mr-2"></i>Editar
                                                </a>
                                                <hr class="border-gray-200 dark:border-gray-600">
                                                <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        <i class="fas fa-pause mr-2"></i>Desativar
                                                    </button>
                                                </form>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="block" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                                        <i class="fas fa-trash mr-2"></i>Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-arrow-up text-6xl text-gray-300 dark:text-gray-600 mb-4 animate-float"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-lg">Nenhuma categoria de receita</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Categorias de Despesa -->
        <div class="category-section animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-l-4 border-red-500">
                <div class="bg-gradient-to-r from-red-500 to-pink-600 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-arrow-down mr-3 text-2xl"></i>
                        Categorias de Despesa ({{ $expenseCategories->count() }})
                    </h3>
                </div>
                
                <div class="p-0">
                    @if($expenseCategories->count() > 0)
                        <div class="space-y-0">
                            @foreach($expenseCategories as $index => $category)
                                <div class="category-item border-b border-gray-100 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 transform hover:scale-[1.02]" style="animation-delay: {{ ($index + 10) * 0.1 }}s;">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="category-icon-modern" style="background: linear-gradient(135deg, {{ $category->color }}20, {{ $category->color }}40); border: 2px solid {{ $category->color }};">
                                                @if($category->icon)
                                                    <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }};"></i>
                                                @else
                                                    <i class="fas fa-tag text-xl" style="color: {{ $category->color }};"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 dark:text-white">{{ $category->name }}</h4>
                                                @if($category->description)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($category->description, 50) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-10">
                                                <a href="{{ route('categories.show', $category) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <i class="fas fa-eye mr-2"></i>Visualizar
                                                </a>
                                                <a href="{{ route('categories.edit', $category) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <i class="fas fa-edit mr-2"></i>Editar
                                                </a>
                                                <hr class="border-gray-200 dark:border-gray-600">
                                                <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        <i class="fas fa-pause mr-2"></i>Desativar
                                                    </button>
                                                </form>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="block" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                                        <i class="fas fa-trash mr-2"></i>Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-arrow-down text-6xl text-gray-300 dark:text-gray-600 mb-4 animate-float"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-lg">Nenhuma categoria de despesa</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Categorias Inativas -->
    @if($inactiveCategories->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-l-4 border-gray-500 animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="bg-gradient-to-r from-gray-500 to-slate-600 p-6">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-pause mr-3 text-2xl"></i>
                    Categorias Inativas ({{ $inactiveCategories->count() }})
                </h3>
            </div>
            
            <div class="p-0">
                <div class="space-y-0">
                    @foreach($inactiveCategories as $index => $category)
                        <div class="category-item border-b border-gray-100 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 opacity-75" style="animation-delay: {{ ($index + 20) * 0.1 }}s;">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="category-icon-modern" style="background: linear-gradient(135deg, #6c757d20, #6c757d40); border: 2px solid #6c757d;">
                                        @if($category->icon)
                                            <i class="{{ $category->icon }} text-xl text-gray-500"></i>
                                        @else
                                            <i class="fas fa-tag text-xl text-gray-500"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-600 dark:text-gray-400">{{ $category->name }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-500">
                                            {{ $category->type === 'income' ? 'Receita' : 'Despesa' }}
                                            @if($category->description)
                                                • {{ Str::limit($category->description, 50) }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-10">
                                        <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900">
                                                <i class="fas fa-play mr-2"></i>Ativar
                                            </button>
                                        </form>
                                        <a href="{{ route('categories.edit', $category) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <i class="fas fa-edit mr-2"></i>Editar
                                        </a>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="block" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                                <i class="fas fa-trash mr-2"></i>Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.category-icon-modern {
    height: 3rem;
    width: 3rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.category-icon-modern:hover {
    transform: scale(1.1) rotate(5deg);
}

.typewriter {
    overflow: hidden;
    border-right: 0.15em solid #6366f1;
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
    50% { border-color: #6366f1; }
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

.category-item {
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
    background: linear-gradient(45deg, #6366f1, #8b5cf6);
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
                
                entry.target.classList.add('animate-fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.addEventListener('DOMContentLoaded', () => {
        // Observar cards de estatísticas
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach(card => observer.observe(card));

        // Observar seções de categorias
        const categorySections = document.querySelectorAll('.category-section');
        categorySections.forEach(section => observer.observe(section));

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
                        
                        const newCategorySections = node.querySelectorAll('.category-section');
                        newCategorySections.forEach(section => observer.observe(section));
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

        for (let i = 0; i < 20; i++) {
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
        
        if (e.target.closest('.category-item')) {
            const item = e.target.closest('.category-item');
            item.style.transform = 'translateX(5px) scale(1.02)';
        }
    });

    document.addEventListener('mouseout', (e) => {
        if (e.target.closest('.stats-card')) {
            const card = e.target.closest('.stats-card');
            card.style.transform = 'translateY(0) scale(1)';
            card.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        }
        
        if (e.target.closest('.category-item')) {
            const item = e.target.closest('.category-item');
            item.style.transform = 'translateX(0) scale(1)';
        }
    });
</script>
@endsection