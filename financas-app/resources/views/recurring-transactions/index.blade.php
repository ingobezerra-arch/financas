@extends('layouts.app')

@section('content')
<div class="max-w-screen-2xl mx-auto min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-gray-900 dark:via-blue-900 dark:to-indigo-900 relative overflow-hidden">
    <!-- Animated Background Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-40 left-40 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="container-fluid px-4 py-8 relative z-10">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2 typewriter" data-text="Transações Recorrentes">
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent"><i class="fas fa-redo-alt mr-3"></i>Transações Recorrentes</span>
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Gerencie suas receitas e despesas que se repetem automaticamente</p>
                </div>
                <a href="{{ route('recurring-transactions.create') }}" 
                   class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center gap-2 group">
                    <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                    <span>Nova Transação Recorrente</span>
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm animate-slide-down">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm animate-slide-down">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <i class="fas fa-redo-alt text-3xl animate-pulse"></i>
                                <div class="text-right">
                                    <div class="text-sm opacity-80">Total Ativas</div>
                                    <div class="text-3xl font-bold counter" data-target="{{ $totalActive }}">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6 text-white relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <i class="fas fa-clock text-3xl animate-bounce"></i>
                                <div class="text-right">
                                    <div class="text-sm opacity-80">Aguardando Execução</div>
                                    <div class="text-3xl font-bold counter" data-target="{{ $totalDue }}">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-6 text-white relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <i class="fas fa-arrow-up text-3xl animate-pulse"></i>
                                <div class="text-right">
                                    <div class="text-sm opacity-80">Receita Mensal</div>
                                    <div class="text-2xl font-bold currency-counter" data-value="{{ $monthlyIncome }}">R$ 0,00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="group">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="bg-gradient-to-r from-red-500 to-pink-500 p-6 text-white relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-white opacity-10 rounded-full -mr-10 -mt-10"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <i class="fas fa-arrow-down text-3xl animate-pulse"></i>
                                <div class="text-right">
                                    <div class="text-sm opacity-80">Despesa Mensal</div>
                                    <div class="text-2xl font-bold currency-counter" data-value="{{ $monthlyExpenses }}">R$ 0,00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <h3 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    Filtros
                </h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('recurring-transactions.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                        <div class="space-y-2">
                            <label for="account_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Conta</label>
                            <select id="account_id" name="account_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Todas as contas</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                            <select id="category_id" name="category_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Todas as categorias</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                            <select id="type" name="type" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Todos</option>
                                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Receita</option>
                                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Despesa</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Frequência</label>
                            <select id="frequency" name="frequency" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Todas</option>
                                <option value="daily" {{ request('frequency') == 'daily' ? 'selected' : '' }}>Diária</option>
                                <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                <option value="yearly" {{ request('frequency') == 'yearly' ? 'selected' : '' }}>Anual</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select id="status" name="status" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">&nbsp;</label>
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fas fa-search"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('recurring-transactions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Limpar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recurring Transactions List -->
        @if($recurringTransactions->count() > 0)
            <!-- Desktop View -->
            <div class="hidden lg:block">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Descrição</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Conta</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Categoria</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Frequência</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Próxima Execução</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($recurringTransactions as $recurring)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 {{ $recurring->shouldExecute() ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($recurring->is_active)
                                                @if($recurring->shouldExecute())
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                        <i class="fas fa-clock mr-1"></i> Pendente
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        <i class="fas fa-check mr-1"></i> Ativa
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                                    <i class="fas fa-pause mr-1"></i> Inativa
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $recurring->description }}</div>
                                            @if($recurring->notes)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($recurring->notes, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium text-white" style="background-color: {{ $recurring->account->color }}">
                                                @if($recurring->account->icon)
                                                    <i class="{{ $recurring->account->icon }} mr-1"></i>
                                                @endif
                                                {{ $recurring->account->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium text-white" style="background-color: {{ $recurring->category->color }}">
                                                @if($recurring->category->icon)
                                                    <i class="{{ $recurring->category->icon }} mr-1"></i>
                                                @endif
                                                {{ $recurring->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($recurring->type === 'income')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <i class="fas fa-arrow-up mr-1"></i> Receita
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    <i class="fas fa-arrow-down mr-1"></i> Despesa
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-lg font-bold {{ $recurring->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $recurring->type === 'income' ? '+' : '-' }}R$ {{ number_format($recurring->amount, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                {{ $recurring->frequency_text }}
                                            </span>
                                            @if($recurring->interval > 1)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">A cada {{ $recurring->interval }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $recurring->next_due_date->format('d/m/Y') }}</div>
                                            @if($recurring->shouldExecute())
                                                <div class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center mt-1">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i> Vencida
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                                <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-50" x-transition>
                                                    <div class="py-1">
                                                        <a href="{{ route('recurring-transactions.show', $recurring) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <i class="fas fa-eye mr-2"></i> Visualizar
                                                        </a>
                                                        @if($recurring->shouldExecute())
                                                            <form action="{{ route('recurring-transactions.execute', $recurring) }}" method="POST" class="block">
                                                                @csrf
                                                                <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-yellow-700 dark:text-yellow-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                    <i class="fas fa-play mr-2"></i> Executar Agora
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('recurring-transactions.toggle', $recurring) }}" method="POST" class="block">
                                                            @csrf
                                                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                @if($recurring->is_active)
                                                                    <i class="fas fa-pause mr-2"></i> Pausar
                                                                @else
                                                                    <i class="fas fa-play mr-2"></i> Ativar
                                                                @endif
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('recurring-transactions.edit', $recurring) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <i class="fas fa-edit mr-2"></i> Editar
                                                        </a>
                                                        <div class="border-t border-gray-100 dark:border-gray-600"></div>
                                                        <form action="{{ route('recurring-transactions.destroy', $recurring) }}" method="POST" class="block" onsubmit="return confirm('Tem certeza que deseja excluir esta transação recorrente?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                <i class="fas fa-trash mr-2"></i> Excluir
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Cards -->
            <div class="lg:hidden space-y-4">
                @foreach($recurringTransactions as $recurring)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl transform hover:scale-105 transition-all duration-300 {{ $recurring->shouldExecute() ? 'ring-2 ring-yellow-400' : '' }}">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $recurring->description }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $recurring->frequency_text }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold {{ $recurring->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $recurring->type === 'income' ? '+' : '-' }}R$ {{ number_format($recurring->amount, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status:</p>
                                    @if($recurring->is_active)
                                        @if($recurring->shouldExecute())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                <i class="fas fa-clock mr-1"></i> Pendente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                <i class="fas fa-check mr-1"></i> Ativa
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                            <i class="fas fa-pause mr-1"></i> Inativa
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Próxima:</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $recurring->next_due_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex gap-2">
                                    @if($recurring->type === 'income')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            <i class="fas fa-arrow-up mr-1"></i> Receita
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            <i class="fas fa-arrow-down mr-1"></i> Despesa
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-50" x-transition>
                                        <div class="py-1">
                                            <a href="{{ route('recurring-transactions.show', $recurring) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <i class="fas fa-eye mr-2"></i> Visualizar
                                            </a>
                                            @if($recurring->shouldExecute())
                                                <form action="{{ route('recurring-transactions.execute', $recurring) }}" method="POST" class="block">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-yellow-700 dark:text-yellow-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <i class="fas fa-play mr-2"></i> Executar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-8 gap-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Mostrando {{ $recurringTransactions->firstItem() }} a {{ $recurringTransactions->lastItem() }} 
                    de {{ $recurringTransactions->total() }} transações recorrentes
                </div>
                <div class="flex">
                    {{ $recurringTransactions->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 py-16">
                <div class="text-center">
                    <div class="mb-6">
                        <i class="fas fa-redo-alt text-6xl text-gray-400 dark:text-gray-500 animate-pulse"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Nenhuma transação recorrente encontrada</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">Configure transações automáticas para receitas e despesas que se repetem regularmente.</p>
                    <a href="{{ route('recurring-transactions.create') }}" 
                       class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 inline-flex items-center gap-2 group">
                        <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                        <span>Criar Primeira Transação Recorrente</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Typewriter effect
    const typewriterElements = document.querySelectorAll('.typewriter');
    typewriterElements.forEach(element => {
        const text = element.getAttribute('data-text');
        element.textContent = '';
        let i = 0;
        function typeWriter() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        }
        typeWriter();
    });

    // Counter animation
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.classList.contains('counter')) {
                    animateCounter(entry.target);
                } else if (entry.target.classList.contains('currency-counter')) {
                    animateCurrency(entry.target);
                }
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe counters
    document.querySelectorAll('.counter, .currency-counter').forEach(counter => {
        observer.observe(counter);
    });

    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 2000;
        const start = performance.now();
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.floor(progress * target);
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        }
        
        requestAnimationFrame(updateCounter);
    }

    function animateCurrency(element) {
        const target = parseFloat(element.getAttribute('data-value'));
        const duration = 2000;
        const start = performance.now();
        
        function updateCurrency(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = progress * target;
            element.textContent = 'R$ ' + current.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            if (progress < 1) {
                requestAnimationFrame(updateCurrency);
            } else {
                element.textContent = 'R$ ' + target.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }
        
        requestAnimationFrame(updateCurrency);
    }

    // Add floating animation to cards
    const cards = document.querySelectorAll('.group');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate-float');
    });

    // Background particles animation
    function createParticle() {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.cssText = `
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            pointer-events: none;
            animation: float-particle 6s linear infinite;
            left: ${Math.random() * 100}%;
            top: 100%;
            opacity: 0.6;
        `;
        
        document.body.appendChild(particle);
        
        setTimeout(() => {
            particle.remove();
        }, 6000);
    }

    // Create particles periodically
    setInterval(createParticle, 2000);

    // Add particle animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes float-particle {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.6;
            }
            90% {
                opacity: 0.6;
            }
            100% {
                transform: translateY(-100vh) translateX(50px) rotate(360deg);
                opacity: 0;
            }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
    `;
    document.head.appendChild(style);

    // Dynamic theme toggle effect
    const themeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const isDark = document.documentElement.classList.contains('dark');
                document.body.style.transition = 'background-color 0.3s ease';
            }
        });
    });
    
    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});
</script>
@endpush