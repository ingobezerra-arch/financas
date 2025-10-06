@extends('layouts.app')

@section('title', 'Integrações Bancárias')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Header com efeito de digitação -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2 typewriter"><i class="fas fa-university mr-3"></i>Integrações Bancárias</h1>
        <p class="text-gray-600 dark:text-gray-300 animate-fade-in">Conecte suas contas bancárias e importe transações automaticamente</p>
        
        <div class="flex justify-between items-center mt-6">
            <div class="flex items-center space-x-4">
                @php
                    $isRealMode = !config('open_finance.sandbox_mode', true) && config('open_finance.production.use_real_apis', false);
                @endphp
                
                @if(!$isRealMode)
                    <a href="{{ route('bank-integrations.setup') }}" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300" title="Configurar bancos reais">
                        <i class="fas fa-cog mr-2"></i>Bancos Reais
                    </a>
                @endif
                
                <button class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 animate-pulse-glow">
                    <a href="{{ route('bank-integrations.create') }}" class="text-white text-decoration-none">
                        <i class="fas fa-plus mr-2"></i>Nova Integração
                    </a>
                </button>
            </div>
        </div>
    </div>

    @if($integrations->count() > 0)
        <!-- Grid de Integrações -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach($integrations as $index => $integration)
                <div class="integration-card bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300 animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                    <!-- Header do Card -->
                    <div class="integration-header p-6 {{ $integration->isOperational() ? 'bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-800 dark:to-emerald-700' : 'bg-gradient-to-r from-yellow-100 to-orange-100 dark:from-yellow-800 dark:to-orange-700' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="bank-icon w-14 h-14 rounded-full {{ $integration->isOperational() ? 'bg-green-500' : 'bg-yellow-500' }} bg-opacity-20 border-2 {{ $integration->isOperational() ? 'border-green-500' : 'border-yellow-500' }} flex items-center justify-center">
                                    <i class="fas fa-university {{ $integration->isOperational() ? 'text-green-500' : 'text-yellow-500' }} text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800 dark:text-white">{{ $integration->bank_name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Integração Bancária</p>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="status-badge">
                                @if($integration->isOperational())
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full flex items-center animate-pulse">
                                        <i class="fas fa-check-circle mr-1"></i> Ativa
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-500 text-white text-xs rounded-full flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Inativa
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conteúdo do Card -->
                    <div class="p-6">
                        <!-- Estatísticas -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-exchange-alt text-blue-600 dark:text-blue-300"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white animate-count" data-target="{{ $integration->syncedTransactions->count() }}">0</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Transações</p>
                            </div>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-wallet text-purple-600 dark:text-purple-300"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white animate-count" data-target="{{ count($integration->getAvailableAccounts()) }}">0</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Contas</p>
                            </div>
                        </div>
                        
                        <!-- Última Sincronização -->
                        @if($integration->last_sync_at)
                            <div class="flex items-center justify-center mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <i class="fas fa-sync-alt text-gray-500 dark:text-gray-400 mr-2"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Última sincronização: {{ $integration->last_sync_at->diffForHumans() }}
                                </p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Footer com Ações -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-center space-x-2">
                            <a href="{{ route('bank-integrations.show', $integration) }}" class="flex-1 px-3 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors text-center">
                                <i class="fas fa-eye mr-1"></i>Detalhes
                            </a>
                            
                            @if($integration->isOperational())
                                <button type="button" class="flex-1 px-3 py-2 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition-colors sync-btn" data-integration-id="{{ $integration->id }}">
                                    <i class="fas fa-sync-alt mr-1"></i>Sincronizar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Lista de transações recentes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                    <i class="fas fa-clock mr-2 text-indigo-500"></i>Transações Sincronizadas Recentes
                </h3>
            </div>
            
            @php
                $recentTransactions = \App\Models\SyncedTransaction::where('user_id', auth()->id())
                    ->with(['bankIntegration', 'category'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            @endphp

            @if($recentTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Banco</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $transaction->transaction_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-university mr-2 text-blue-500"></i>
                                            <span class="text-sm text-gray-900 dark:text-white">{{ $transaction->bankIntegration->bank_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ $transaction->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type === 'credit' ? '+' : '-' }}
                                            R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($transaction->is_processed)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                <i class="fas fa-check mr-1"></i>Processada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 animate-pulse">
                                                <i class="fas fa-clock mr-1"></i>Pendente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if(!$transaction->is_processed)
                                            <a href="{{ route('bank-integrations.transactions') }}?unprocessed=1" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Processar
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 text-center">
                    <a href="{{ route('bank-integrations.transactions') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-800 dark:text-indigo-100 dark:hover:bg-indigo-700 transition-colors duration-200">
                        Ver Todas as Transações
                    </a>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-exchange-alt text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nenhuma transação sincronizada ainda.</p>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
            <div class="animate-float mb-6">
                <i class="fas fa-university text-8xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Nenhuma integração bancária</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                Conecte suas contas bancárias para importar transações automaticamente.
            </p>
            <a href="{{ route('bank-integrations.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                <i class="fas fa-plus mr-2"></i>Adicionar Primeira Integração
            </a>
        </div>
    @endif
</div>

<!-- Modal de confirmação para sincronização -->
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="syncModal">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="flex items-center justify-between pb-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Sincronizar Transações</h3>
                <button type="button" class="close-modal text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">Deseja sincronizar as transações desta integração bancária?</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Isso pode levar alguns minutos dependendo da quantidade de transações.</p>
            </div>
            <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                <form id="syncForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-sync-alt mr-2"></i>Sincronizar
                    </button>
                </form>
                <button type="button" class="close-modal mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
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

.bank-icon {
    transition: all 0.3s ease;
}

.bank-icon:hover {
    transform: scale(1.1) rotate(5deg);
}

.integration-card {
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

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
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
    animation: particleFloat 6s ease-in-out infinite;
}

@keyframes particleFloat {
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
document.addEventListener('DOMContentLoaded', function() {
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

    // Observar cards de integração
    const integrationCards = document.querySelectorAll('.integration-card');
    integrationCards.forEach(card => observer.observe(card));

    // Sistema de tema escuro dinâmico
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }

    // Criar partículas de fundo
    createParticles();
    
    // Função para abrir modal de sincronização
    function syncIntegration(integrationId) {
        const modal = document.getElementById('syncModal');
        const form = document.getElementById('syncForm');
        
        if (modal && form) {
            form.action = `/bank-integrations/${integrationId}/sync`;
            modal.classList.remove('hidden');
        }
    }
    
    // Função para fechar modal
    function closeModal() {
        const modal = document.getElementById('syncModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    // Adicionar eventos aos botões de sincronização
    const syncButtons = document.querySelectorAll('.sync-btn');
    syncButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const integrationId = this.getAttribute('data-integration-id');
            syncIntegration(integrationId);
        });
    });
    
    // Eventos para fechar modal
    const closeButtons = document.querySelectorAll('.close-modal');
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });
    
    // Fechar modal ao clicar fora
    document.getElementById('syncModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
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
</script>
@endsection