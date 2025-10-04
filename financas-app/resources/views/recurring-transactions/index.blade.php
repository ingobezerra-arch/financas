@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-0">{{ __('Transações Recorrentes') }}</h4>
                    <a href="{{ route('recurring-transactions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Transação Recorrente
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

                    <!-- Resumo Estatístico -->
                    <div class="row mb-4 g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-redo-alt fa-2x mb-2"></i>
                                    <h6 class="mb-1">Total Ativas</h6>
                                    <h4 class="mb-0">{{ $totalActive }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h6 class="mb-1">Aguardando Execução</h6>
                                    <h4 class="mb-0">{{ $totalDue }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                    <h6 class="mb-1">Receita Mensal</h6>
                                    <h4 class="mb-0">R$ {{ number_format($monthlyIncome, 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                    <h6 class="mb-1">Despesa Mensal</h6>
                                    <h4 class="mb-0">R$ {{ number_format($monthlyExpenses, 2, ',', '.') }}</h4>
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
                            <form method="GET" action="{{ route('recurring-transactions.index') }}">
                                <div class="row g-3">
                                    <div class="col-lg-2 col-md-4">
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
                                    <div class="col-lg-2 col-md-4">
                                        <label for="category_id" class="form-label">Categoria</label>
                                        <select id="category_id" name="category_id" class="form-select">
                                            <option value="">Todas as categorias</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
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
                                        <label for="frequency" class="form-label">Frequência</label>
                                        <select id="frequency" name="frequency" class="form-select">
                                            <option value="">Todas</option>
                                            <option value="daily" {{ request('frequency') == 'daily' ? 'selected' : '' }}>Diária</option>
                                            <option value="weekly" {{ request('frequency') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                            <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                            <option value="yearly" {{ request('frequency') == 'yearly' ? 'selected' : '' }}>Anual</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4">
                                        <label for="status" class="form-label">Status</label>
                                        <select id="status" name="status" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-8 d-flex align-items-end">
                                        <div class="w-100">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <a href="{{ route('recurring-transactions.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Limpar Filtros
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Transações Recorrentes -->
                    @if($recurringTransactions->count() > 0)
                        <!-- Desktop Table -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Status</th>
                                            <th>Descrição</th>
                                            <th>Conta</th>
                                            <th>Categoria</th>
                                            <th>Tipo</th>
                                            <th class="text-end">Valor</th>
                                            <th>Frequência</th>
                                            <th>Próxima Execução</th>
                                            <th width="120">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recurringTransactions as $recurring)
                                            <tr class="{{ $recurring->shouldExecute() ? 'table-warning' : '' }}">
                                                <td>
                                                    @if($recurring->is_active)
                                                        @if($recurring->shouldExecute())
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-clock"></i> Pendente
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check"></i> Ativa
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-pause"></i> Inativa
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $recurring->description }}</strong>
                                                    @if($recurring->notes)
                                                        <br><small class="text-muted">{{ Str::limit($recurring->notes, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $recurring->account->color }}">
                                                        @if($recurring->account->icon)
                                                            <i class="{{ $recurring->account->icon }}"></i>
                                                        @endif
                                                        {{ $recurring->account->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $recurring->category->color }}">
                                                        @if($recurring->category->icon)
                                                            <i class="{{ $recurring->category->icon }}"></i>
                                                        @endif
                                                        {{ $recurring->category->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($recurring->type === 'income')
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
                                                    <strong class="{{ $recurring->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                        {{ $recurring->type === 'income' ? '+' : '-' }}R$ {{ number_format($recurring->amount, 2, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $recurring->frequency_text }}</span>
                                                    @if($recurring->interval > 1)
                                                        <small class="text-muted d-block">A cada {{ $recurring->interval }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $recurring->next_due_date->format('d/m/Y') }}
                                                    @if($recurring->shouldExecute())
                                                        <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Vencida</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="{{ route('recurring-transactions.show', $recurring) }}">
                                                                <i class="fas fa-eye"></i> Visualizar
                                                            </a></li>
                                                            @if($recurring->shouldExecute())
                                                                <li>
                                                                    <form action="{{ route('recurring-transactions.execute', $recurring) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item text-warning">
                                                                            <i class="fas fa-play"></i> Executar Agora
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <form action="{{ route('recurring-transactions.toggle', $recurring) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        @if($recurring->is_active)
                                                                            <i class="fas fa-pause"></i> Pausar
                                                                        @else
                                                                            <i class="fas fa-play"></i> Ativar
                                                                        @endif
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li><a class="dropdown-item" href="{{ route('recurring-transactions.edit', $recurring) }}">
                                                                <i class="fas fa-edit"></i> Editar
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('recurring-transactions.destroy', $recurring) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta transação recorrente?')">
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
                            @foreach($recurringTransactions as $recurring)
                                <div class="card mb-3 {{ $recurring->shouldExecute() ? 'border-warning' : '' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $recurring->description }}</h6>
                                                <small class="text-muted">{{ $recurring->frequency_text }}</small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="{{ $recurring->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                    {{ $recurring->type === 'income' ? '+' : '-' }}R$ {{ number_format($recurring->amount, 2, ',', '.') }}
                                                </strong>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Status:</small>
                                                @if($recurring->is_active)
                                                    @if($recurring->shouldExecute())
                                                        <span class="badge bg-warning">Pendente</span>
                                                    @else
                                                        <span class="badge bg-success">Ativa</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Inativa</span>
                                                @endif
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Próxima:</small>
                                                <span class="small">{{ $recurring->next_due_date->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($recurring->type === 'income')
                                                    <span class="badge bg-success">Receita</span>
                                                @else
                                                    <span class="badge bg-danger">Despesa</span>
                                                @endif
                                            </div>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('recurring-transactions.show', $recurring) }}">
                                                        <i class="fas fa-eye"></i> Visualizar
                                                    </a></li>
                                                    @if($recurring->shouldExecute())
                                                        <li>
                                                            <form action="{{ route('recurring-transactions.execute', $recurring) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-warning">
                                                                    <i class="fas fa-play"></i> Executar
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                Mostrando {{ $recurringTransactions->firstItem() }} a {{ $recurringTransactions->lastItem() }} 
                                de {{ $recurringTransactions->total() }} transações recorrentes
                            </div>
                            {{ $recurringTransactions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-redo-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma transação recorrente encontrada</h5>
                            <p class="text-muted">Configure transações automáticas para receitas e despesas que se repetem regularmente.</p>
                            <a href="{{ route('recurring-transactions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Primeira Transação Recorrente
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