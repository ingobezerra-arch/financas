@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto py-6 animate-slide-in-up">
    <!-- Header Principal Melhorado -->
    <div class="card-modern animate-fade-in-scale mb-8">
        <div class="flex items-center justify-between p-8 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl flex items-center justify-center animate-float">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-university text-purple-500 mr-3"></i>{{ __('Minhas Contas') }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Gerencie suas contas financeiras</p>
                </div>
            </div>
            <a href="{{ route('accounts.create') }}" class="btn-gradient-primary animate-glow transform hover:scale-105 transition-all duration-300">
                <i class="fas fa-plus mr-2"></i> Nova Conta
            </a>
        </div>
        
        <!-- Cards de Estatísticas Aprimorados -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Geral -->
                <div class="stats-card bg-gradient-to-r from-indigo-500 to-purple-600 transform transition-all duration-300 hover:scale-105 animate-float">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white/80 text-sm font-medium mb-1">Saldo Total</h3>
                            <p class="text-white text-3xl font-bold animate-number" data-value="{{ $accounts->sum('balance') }}">R$ 0,00</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 animate-bounce">
                            <i class="fas fa-coins text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-white/70 text-sm">
                        <i class="fas fa-chart-line mr-2"></i>
                        <span>Todas as contas</span>
                    </div>
                </div>

                <!-- Contas Ativas -->
                <div class="stats-card bg-gradient-to-r from-green-500 to-emerald-600 transform transition-all duration-300 hover:scale-105 animate-float" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white/80 text-sm font-medium mb-1">Contas Ativas</h3>
                            <p class="text-white text-3xl font-bold">{{ $accounts->where('is_active', true)->count() }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 animate-pulse">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-white/70 text-sm">
                        <i class="fas fa-play mr-2"></i>
                        <span>Em funcionamento</span>
                    </div>
                </div>

                <!-- Total de Contas -->
                <div class="stats-card bg-gradient-to-r from-blue-500 to-cyan-600 transform transition-all duration-300 hover:scale-105 animate-float" style="animation-delay: 0.4s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white/80 text-sm font-medium mb-1">Total de Contas</h3>
                            <p class="text-white text-3xl font-bold">{{ $accounts->count() }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-4 animate-bounce">
                            <i class="fas fa-list text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-white/70 text-sm">
                        <i class="fas fa-database mr-2"></i>
                        <span>Cadastradas</span>
                    </div>
                </div>
            </div>
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 animate-slide-in-up">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2 animate-bounce"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 animate-slide-in-up">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 animate-pulse"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if($accounts->count() > 0)
                <!-- Grid de Contas com Animações -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($accounts as $index => $account)
                        <div class="card-modern transform transition-all duration-500 hover:scale-105 animate-fade-in-scale" style="animation-delay: {{ $index * 0.1 }}s;">
                            <div class="relative overflow-hidden">
                                <!-- Borda lateral colorida -->
                                <div class="absolute left-0 top-0 w-1 h-full bg-gradient-to-b" style="background: linear-gradient(to bottom, {{ $account->color }}, {{ $account->color }}80);"></div>
                                
                                <div class="p-6">
                                    <!-- Header do Card -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-purple-400 to-blue-500 flex items-center justify-center animate-float" style="animation-delay: {{ $index * 0.2 }}s;">
                                                @if($account->icon)
                                                    <i class="{{ $account->icon }} text-white text-lg"></i>
                                                @else
                                                    <i class="fas fa-wallet text-white text-lg"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $account->name }}</h5>
                                                <span class="text-sm text-gray-500 dark:text-gray-400 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">
                                                    {{ ucfirst(str_replace('_', ' ', $account->type)) }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Dropdown com efeitos -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 transform hover:scale-110">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-10">
                                                <a href="{{ route('accounts.show', $account) }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                    <i class="fas fa-eye mr-2"></i> Visualizar
                                                </a>
                                                <a href="{{ route('accounts.edit', $account) }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                    <i class="fas fa-edit mr-2"></i> Editar
                                                </a>
                                                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                                                <form action="{{ route('accounts.toggle-status', $account) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                        @if($account->is_active)
                                                            <i class="fas fa-pause mr-2"></i> Desativar
                                                        @else
                                                            <i class="fas fa-play mr-2"></i> Ativar
                                                        @endif
                                                    </button>
                                                </form>
                                                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                                                <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="block" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        <i class="fas fa-trash mr-2"></i> Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Saldo com animação -->
                                    <div class="mb-4">
                                        <h3 class="text-2xl font-bold {{ $account->balance < 0 ? 'text-red-600' : 'text-green-600' }} animate-number" data-value="{{ $account->balance }}">
                                            R$ 0,00
                                        </h3>
                                        
                                        <!-- Barra de progresso do saldo -->
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                                            <div class="{{ $account->balance >= 0 ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-red-400 to-red-600' }} h-2 rounded-full transition-all duration-1000 animate-pulse" style="width: {{ min(abs($account->balance) / max($accounts->max('balance'), 1) * 100, 100) }}%"></div>
                                        </div>
                                    </div>

                                    @if($account->description)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 italic">{{ $account->description }}</p>
                                    @endif

                                    <!-- Status e Ícone -->
                                    <div class="flex items-center justify-between">
                                        @if($account->is_active)
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full text-xs font-medium animate-pulse">
                                                <div class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-ping"></div>
                                                Ativa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">
                                                <div class="w-2 h-2 bg-gray-400 rounded-full mr-1"></div>
                                                Inativa
                                            </span>
                                        @endif
                                        
                                        <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-calendar mr-1"></i>
                                            Criada em {{ $account->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Efeito shimmer -->
                                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-1000 group-hover:translate-x-full"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                        <div class="mt-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5>Total Geral</h5>
                                            <h3 class="text-primary">
                                                R$ {{ number_format($accounts->sum('balance'), 2, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5>Contas Ativas</h5>
                                            <h3 class="text-success">{{ $accounts->where('is_active', true)->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5>Total de Contas</h5>
                                            <h3 class="text-info">{{ $accounts->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Estado Vazio Melhorado -->
                        <div class="text-center py-20 animate-fade-in-scale">
                            <div class="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full flex items-center justify-center animate-float">
                                <i class="fas fa-wallet text-purple-500 text-5xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                <i class="fas fa-info-circle text-blue-500 mr-3"></i>Nenhuma conta cadastrada
                            </h3>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                                Comece sua jornada financeira criando sua primeira conta. 
                                Organize melhor suas finanças!
                            </p>
                            <a href="{{ route('accounts.create') }}" class="btn-gradient-primary transform hover:scale-105 transition-all duration-300 inline-flex items-center px-8 py-4 text-lg">
                                <i class="fas fa-plus mr-2"></i> Criar Primeira Conta
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animação de números melhorada
    function animateValue(element, start, end, duration = 2500) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = formatCurrency(current);
        }, 16);
    }

    function formatCurrency(value) {
        return 'R$ ' + value.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Inicializar animações dos números
    document.querySelectorAll('.animate-number').forEach(el => {
        const finalValue = parseFloat(el.dataset.value) || 0;
        animateValue(el, 0, finalValue);
    });

    // Efeitos de hover aprimorados para cards
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.05) rotateY(5deg)';
            this.style.boxShadow = '0 30px 60px rgba(0, 0, 0, 0.25)';
            this.style.zIndex = '10';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1) rotateY(0deg)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
            this.style.zIndex = '1';
        });
    });

    // Efeitos para cards de contas
    const accountCards = document.querySelectorAll('.card-modern');
    accountCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
    });

    // Efeito parallax suave no scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const floatingElements = document.querySelectorAll('.animate-float');
        
        floatingElements.forEach((element, index) => {
            const rate = scrolled * -0.05 * (index + 1);
            element.style.transform = `translateY(${rate}px)`;
        });
    });

    // Intersection Observer para animações de entrada
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Aplicar observer a elementos animáveis
    document.querySelectorAll('.animate-fade-in-scale, .animate-slide-in-up').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });

    // Efeito de digitação no título principal
    const mainTitle = document.querySelector('h1');
    if (mainTitle) {
        const text = mainTitle.innerHTML;
        mainTitle.innerHTML = '';
        mainTitle.style.borderRight = '3px solid #8B5CF6';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                if (text[i] === '<') {
                    // Pular tags HTML
                    let tag = '';
                    while (i < text.length && text[i] !== '>') {
                        tag += text[i];
                        i++;
                    }
                    tag += text[i]; // adicionar '>'
                    mainTitle.innerHTML += tag;
                } else {
                    mainTitle.innerHTML += text[i];
                }
                i++;
                setTimeout(typeWriter, 30);
            } else {
                setTimeout(() => {
                    mainTitle.style.borderRight = 'none';
                }, 500);
            }
        };
        
        setTimeout(typeWriter, 1000);
    }
});
</script>
@endpush

@endsection