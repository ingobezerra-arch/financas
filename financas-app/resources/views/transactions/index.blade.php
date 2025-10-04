@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Minhas Transações') }}</h4>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Transação
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Resumo Financeiro -->
                    <div class="row mb-4 g-3">
                        <div class="col-lg-4 col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                    <h6 class="mb-1">Total de Receitas</h6>
                                    <h4 class="mb-0">R$ {{ number_format($totalIncome ?? 0, 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                    <h6 class="mb-1">Total de Despesas</h6>
                                    <h4 class="mb-0">R$ {{ number_format($totalExpenses ?? 0, 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                                    <h6 class="mb-1">Saldo Líquido</h6>
                                    <h4 class="mb-0">R$ {{ number_format(($totalIncome ?? 0) - ($totalExpenses ?? 0), 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('transactions.index') }}">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-6">
                                        <label for="account_id" class="form-label">Conta</label>
                                        <select id="account_id" name="account_id" class="form-select">
                                            <option value="">Todas as contas</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                                    {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label for="category_id" class="form-label">Categoria</label>
                                        <select id="category_id" name="category_id" class="form-select">
                                            <option value="">Todas as categorias</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }} ({{ $category->type === 'income' ? 'Receita' : 'Despesa' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4">
                                        <label for="type" class="form-label">Tipo</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Receita</option>
                                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Despesa</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4">
                                        <label for="start_date" class="form-label">Data Inicial</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-lg-2 col-md-4">
                                        <label for="end_date" class="form-label">Data Final</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search"></i> Filtrar
                                        </button>
                                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Limpar
                                        </a>
                                    </div>
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
@endpush