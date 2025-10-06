@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto py-6 animate-slide-in-up">
    <!-- Header com animação -->
    <div class="card-modern animate-fade-in-scale mb-6">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-0 animate-on-scroll"><i class="fas fa-exchange-alt text-blue-500 mr-3"></i>{{ __('Minhas Transações') }}</h4>
            <a href="{{ route('transactions.create') }}" class="btn-gradient-primary animate-glow">
                <i class="fas fa-plus mr-2"></i> Nova Transação
            </a>
        </div>
        
        <!-- Cards de Resumo Dinâmicos -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 animate-slide-in-up">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 animate-slide-in-up">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Cards de Estatísticas Financeiras -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Receitas -->
                <div class="stats-card bg-gradient-to-r from-green-500 to-emerald-600 transform transition-all duration-300 hover:scale-105 animate-float">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white/80 text-sm font-medium mb-1">Total de Receitas</h3>
                            <p class="text-white text-2xl font-bold animate-number" data-value="{{ $totalIncome ?? 0 }}">R$ 0,00</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3 animate-bounce">
                            <i class="fas fa-arrow-up text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-white/70 text-xs">
                        <i class="fas fa-trending-up mr-1"></i>
                        <span>Total acumulado</span>
                    </div>
                </div>

                <!-- Total Despesas -->
                <div class="stats-card bg-gradient-to-r from-red-500 to-pink-600 transform transition-all duration-300 hover:scale-105 animate-float" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white/80 text-sm font-medium mb-1">Total de Despesas</h3>
                            <p class="text-white text-2xl font-bold animate-number" data-value="{{ $totalExpenses ?? 0 }}">R$ 0,00</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3 animate-pulse">
                            <i class="fas fa-arrow-down text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-white/70 text-xs">
                        <i class="fas fa-trending-down mr-1"></i>
                        <span>Total acumulado</span>
                    </div>
                </div>

                <!-- Saldo Líquido -->
                <div class="stats-card bg-gradient-to-r from-blue-500 to-indigo-600 transform transition-all duration-300 hover:scale-105 animate-float" style="animation-delay: 0.4s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white/80 text-sm font-medium mb-1">Saldo Líquido</h3>
                            <p class="text-white text-2xl font-bold animate-number" data-value="{{ ($totalIncome ?? 0) - ($totalExpenses ?? 0) }}">R$ 0,00</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3 animate-bounce">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-white/70 text-xs">
                        <i class="fas fa-balance-scale mr-1"></i>
                        <span>Receitas - Despesas</span>
                    </div>
                </div>
            </div>

            <!-- Filtros Modernos -->
            <div class="card-modern mb-6">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h6 class="text-lg font-semibold text-gray-900 dark:text-white mb-0 flex items-center">
                        <i class="fas fa-filter text-purple-500 mr-3"></i> Filtros Avançados
                    </h6>
                    <button type="button" class="btn-gradient-primary text-sm px-4 py-2" onclick="toggleFilters()">
                        <i class="fas fa-chevron-down" id="filter-icon"></i>
                    </button>
                </div>
                <div class="p-6" id="filters-container" style="display: none;">
                    <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label for="account_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-wallet mr-2"></i>Conta
                                </label>
                                <select id="account_id" name="account_id" class="form-control w-full">
                                    <option value="">Todas as contas</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-tags mr-2"></i>Categoria
                                </label>
                                <select id="category_id" name="category_id" class="form-control w-full">
                                    <option value="">Todas as categorias</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->type === 'income' ? 'Receita' : 'Despesa' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-exchange-alt mr-2"></i>Tipo
                                </label>
                                <select id="type" name="type" class="form-control w-full">
                                    <option value="">Todos</option>
                                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Receita</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Despesa</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-calendar-alt mr-2"></i>Data Inicial
                                </label>
                                <input type="date" id="start_date" name="start_date" class="form-control w-full" value="{{ request('start_date') }}">
                            </div>
                            <div class="form-group">
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-calendar-alt mr-2"></i>Data Final
                                </label>
                                <input type="date" id="end_date" name="end_date" class="form-control w-full" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button type="submit" class="btn-gradient-primary">
                                <i class="fas fa-search mr-2"></i> Filtrar
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn-gradient-secondary">
                                <i class="fas fa-times mr-2"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

                    <!-- Lista de Transações -->
                    @if($transactions->count() > 0)
                        <!-- Desktop Table -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Data</th>
                                            <th>Descrição</th>
                                            <th>Conta</th>
                                            <th>Categoria</th>
                                            <th>Tipo</th>
                                            <th class="text-end">Valor</th>
                                            <th>Status</th>
                                            <th width="100">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                                <td>
                                                    <strong>{{ $transaction->description }}</strong>
                                                    @if($transaction->notes)
                                                        <br><small class="text-muted">{{ Str::limit($transaction->notes, 50) }}</small>
                                                    @endif
                                                    @if($transaction->tags && count($transaction->tags) > 0)
                                                        <br>
                                                        @foreach($transaction->tags as $tag)
                                                            <span class="badge bg-light text-dark me-1">#{{ $tag }}</span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $transaction->account->color }}">
                                                        @if($transaction->account->icon)
                                                            <i class="{{ $transaction->account->icon }}"></i>
                                                        @endif
                                                        {{ $transaction->account->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $transaction->category->color }}">
                                                        @if($transaction->category->icon)
                                                            <i class="{{ $transaction->category->icon }}"></i>
                                                        @endif
                                                        {{ $transaction->category->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($transaction->type === 'income')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-arrow-up"></i> Receita
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-arrow-down"></i> Despesa
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                        {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    @if($transaction->status === 'completed')
                                                        <span class="badge bg-success">Concluída</span>
                                                    @elseif($transaction->status === 'pending')
                                                        <span class="badge bg-warning">Pendente</span>
                                                    @else
                                                        <span class="badge bg-secondary">Cancelada</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="{{ route('transactions.show', $transaction) }}">
                                                                <i class="fas fa-eye"></i> Visualizar
                                                            </a></li>
                                                            <li><a class="dropdown-item" href="{{ route('transactions.edit', $transaction) }}">
                                                                <i class="fas fa-edit"></i> Editar
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta transação?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash"></i> Excluir
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="d-lg-none">
                            @foreach($transactions as $transaction)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $transaction->description }}</h6>
                                                <small class="text-muted">{{ $transaction->transaction_date->format('d/m/Y') }}</small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                                </strong>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Conta:</small>
                                                <span class="badge" style="background-color: {{ $transaction->account->color }}; font-size: 0.75rem;">
                                                    @if($transaction->account->icon)
                                                        <i class="{{ $transaction->account->icon }}"></i>
                                                    @endif
                                                    {{ $transaction->account->name }}
                                                </span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Categoria:</small>
                                                <span class="badge" style="background-color: {{ $transaction->category->color }}; font-size: 0.75rem;">
                                                    @if($transaction->category->icon)
                                                        <i class="{{ $transaction->category->icon }}"></i>
                                                    @endif
                                                    {{ $transaction->category->name }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($transaction->type === 'income')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-arrow-up"></i> Receita
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-arrow-down"></i> Despesa
                                                    </span>
                                                @endif
                                                
                                                @if($transaction->status === 'completed')
                                                    <span class="badge bg-success">Concluída</span>
                                                @elseif($transaction->status === 'pending')
                                                    <span class="badge bg-warning">Pendente</span>
                                                @else
                                                    <span class="badge bg-secondary">Cancelada</span>
                                                @endif
                                            </div>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('transactions.show', $transaction) }}">
                                                        <i class="fas fa-eye"></i> Visualizar
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('transactions.edit', $transaction) }}">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta transação?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash"></i> Excluir
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        @if($transaction->notes)
                                            <div class="mt-2">
                                                <small class="text-muted">{{ $transaction->notes }}</small>
                                            </div>
                                        @endif
                                        
                                        @if($transaction->tags && count($transaction->tags) > 0)
                                            <div class="mt-2">
                                                @foreach($transaction->tags as $tag)
                                                    <span class="badge bg-light text-dark me-1">#{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Mostrando {{ $transactions->firstItem() }} a {{ $transactions->lastItem() }} 
                                de {{ $transactions->total() }} transações
                            </div>
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma transação encontrada</h5>
                            <p class="text-muted">Comece registrando sua primeira transação financeira.</p>
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Primeira Transação
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
// Animação de números para os cards de estatísticas
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

// Aplicar tema escuro dinamicamente se necessário
function applyDarkThemeToElements() {
    if (document.body.getAttribute('data-theme') === 'dark') {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        if (node.classList.contains('badge') || node.classList.contains('alert')) {
                            node.style.filter = 'brightness(0.9)';
                        }
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar animações de números
    document.querySelectorAll('.animate-number').forEach(el => {
        const finalValue = parseFloat(el.dataset.value);
        setTimeout(() => {
            animateValue(el, 0, finalValue, 2000);
        }, 500);
    });
    
    // Aplicar tema escuro a elementos dinâmicos
    applyDarkThemeToElements();
    
    // Adicionar efeitos de hover nas linhas da tabela
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
            this.style.transition = 'all 0.3s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Animação de entrada para elementos com scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
    
    // Função para toggle dos filtros
    window.toggleFilters = function() {
        const container = document.getElementById('filters-container');
        const icon = document.getElementById('filter-icon');
        
        if (container.style.display === 'none') {
            container.style.display = 'block';
            container.classList.add('animate-slide-in-up');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            container.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    };
    
    // Animação dos números nos cards
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
    
    // Inicializar animações dos números
    document.querySelectorAll('.animate-number').forEach(el => {
        const finalValue = parseFloat(el.dataset.value) || 0;
        animateValue(el, 0, finalValue, 2000);
    });
    
    // Melhorar interação dos cards
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.03)';
            this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
        });
    });
    
    // Efeito de typing no título
    const title = document.querySelector('h4.animate-on-scroll');
    if (title) {
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
        
        setTimeout(typeWriter, 200);
    }
    
    // Adicionar efeito de shimmer nos badges
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.classList.add('animate-shimmer');
        });
        
        badge.addEventListener('mouseleave', function() {
            this.classList.remove('animate-shimmer');
        });
    });
});
</script>
@endpush