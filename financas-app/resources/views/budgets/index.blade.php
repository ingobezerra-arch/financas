@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="mb-0">{{ __('Orçamentos') }}</h4>
                    <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Orçamento
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
                                    <i class="fas fa-chart-pie fa-2x mb-2"></i>
                                    <h6 class="mb-1">Orçamentos Ativos</h6>
                                    <h4 class="mb-0">{{ $activeBudgets }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-bullseye fa-2x mb-2"></i>
                                    <h6 class="mb-1">Total Orçado</h6>
                                    <h4 class="mb-0">R$ {{ number_format($totalBudget, 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                    <h6 class="mb-1">Total Gasto</h6>
                                    <h4 class="mb-0">R$ {{ number_format($totalSpent, 2, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <h6 class="mb-1">Estourados</h6>
                                    <h4 class="mb-0">{{ $overBudgetCount }}</h4>
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
                            <form method="GET" action="{{ route('budgets.index') }}">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-6">
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
                                    <div class="col-lg-2 col-md-6">
                                        <label for="period" class="form-label">Período</label>
                                        <select id="period" name="period" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                            <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                                            <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Anual</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-6">
                                        <label for="status" class="form-label">Status</label>
                                        <select id="status" name="status" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirado</option>
                                            <option value="over_budget" {{ request('status') == 'over_budget' ? 'selected' : '' }}>Estourado</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 d-flex align-items-end">
                                        <div class="w-100">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 d-flex align-items-end">
                                        <div class="w-100">
                                            <a href="{{ route('budgets.index') }}" class="btn btn-secondary w-100">
                                                <i class="fas fa-times"></i> Limpar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Orçamentos -->
                    @if($budgets->count() > 0)
                        <!-- Desktop Table -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Status</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th>Período</th>
                                            <th class="text-end">Orçado</th>
                                            <th class="text-end">Gasto</th>
                                            <th class="text-center">Progresso</th>
                                            <th>Validade</th>
                                            <th width="120">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($budgets as $budget)
                                            @php
                                                $percentageUsed = $budget->percentage_used;
                                                $isOverBudget = $budget->is_over_budget;
                                                $shouldAlert = $budget->should_alert;
                                                $isExpired = $budget->end_date < now();
                                            @endphp
                                            <tr class="{{ $isOverBudget ? 'table-danger' : ($shouldAlert ? 'table-warning' : '') }}">
                                                <td>
                                                    @if(!$budget->is_active)
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-pause"></i> Inativo
                                                        </span>
                                                    @elseif($isExpired)
                                                        <span class="badge bg-dark">
                                                            <i class="fas fa-calendar-times"></i> Expirado
                                                        </span>
                                                    @elseif($isOverBudget)
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-exclamation-triangle"></i> Estourado
                                                        </span>
                                                    @elseif($shouldAlert)
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-exclamation"></i> Atenção
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> OK
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $budget->name }}</strong>
                                                    @if($budget->description)
                                                        <br><small class="text-muted">{{ Str::limit($budget->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $budget->category->color }}">
                                                        @if($budget->category->icon)
                                                            <i class="{{ $budget->category->icon }}"></i>
                                                        @endif
                                                        {{ $budget->category->name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        @switch($budget->period)
                                                            @case('weekly') Semanal @break
                                                            @case('monthly') Mensal @break
                                                            @case('quarterly') Trimestral @break
                                                            @case('yearly') Anual @break
                                                        @endswitch
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-primary">R$ {{ number_format($budget->amount, 2, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="{{ $isOverBudget ? 'text-danger' : 'text-warning' }}">
                                                        R$ {{ number_format($budget->spent, 2, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <div class="progress mb-1" style="height: 20px;">
                                                        <div class="progress-bar {{ $isOverBudget ? 'bg-danger' : ($shouldAlert ? 'bg-warning' : 'bg-success') }}" 
                                                             role="progressbar" 
                                                             style="width: {{ min($percentageUsed, 100) }}%"
                                                             aria-valuenow="{{ $percentageUsed }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ number_format($percentageUsed, 1) }}%
                                                        </div>
                                                    </div>
                                                    @if($isOverBudget)
                                                        <small class="text-danger">
                                                            +R$ {{ number_format($budget->spent - $budget->amount, 2, ',', '.') }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">
                                                            Resta: R$ {{ number_format($budget->remaining_amount, 2, ',', '.') }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $budget->start_date->format('d/m/Y') }}</small>
                                                    <br>
                                                    <small>{{ $budget->end_date->format('d/m/Y') }}</small>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="{{ route('budgets.show', $budget) }}">
                                                                <i class="fas fa-eye"></i> Visualizar
                                                            </a></li>
                                                            <li>
                                                                <form action="{{ route('budgets.toggle', $budget) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        @if($budget->is_active)
                                                                            <i class="fas fa-pause"></i> Pausar
                                                                        @else
                                                                            <i class="fas fa-play"></i> Ativar
                                                                        @endif
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li><a class="dropdown-item" href="{{ route('budgets.edit', $budget) }}">
                                                                <i class="fas fa-edit"></i> Editar
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este orçamento?')">
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
                            @foreach($budgets as $budget)
                                @php
                                    $percentageUsed = $budget->percentage_used;
                                    $isOverBudget = $budget->is_over_budget;
                                    $shouldAlert = $budget->should_alert;
                                    $isExpired = $budget->end_date < now();
                                @endphp
                                <div class="card mb-3 {{ $isOverBudget ? 'border-danger' : ($shouldAlert ? 'border-warning' : '') }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">{{ $budget->name }}</h6>
                                                <small class="text-muted">{{ $budget->category->name }}</small>
                                            </div>
                                            <div class="text-end">
                                                @if(!$budget->is_active)
                                                    <span class="badge bg-secondary">Inativo</span>
                                                @elseif($isExpired)
                                                    <span class="badge bg-dark">Expirado</span>
                                                @elseif($isOverBudget)
                                                    <span class="badge bg-danger">Estourado</span>
                                                @elseif($shouldAlert)
                                                    <span class="badge bg-warning">Atenção</span>
                                                @else
                                                    <span class="badge bg-success">OK</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar {{ $isOverBudget ? 'bg-danger' : ($shouldAlert ? 'bg-warning' : 'bg-success') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ min($percentageUsed, 100) }}%">
                                                {{ number_format($percentageUsed, 1) }}%
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2 mb-2 small">
                                            <div class="col-6">
                                                <span class="text-muted">Orçado:</span>
                                                <strong class="text-primary">R$ {{ number_format($budget->amount, 2, ',', '.') }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Gasto:</span>
                                                <strong class="{{ $isOverBudget ? 'text-danger' : 'text-warning' }}">
                                                    R$ {{ number_format($budget->spent, 2, ',', '.') }}
                                                </strong>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                {{ $budget->start_date->format('d/m/Y') }} - {{ $budget->end_date->format('d/m/Y') }}
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
                                Mostrando {{ $budgets->firstItem() }} a {{ $budgets->lastItem() }} 
                                de {{ $budgets->total() }} orçamentos
                            </div>
                            {{ $budgets->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum orçamento encontrado</h5>
                            <p class="text-muted">Crie orçamentos para controlar seus gastos por categoria e alcançar suas metas financeiras.</p>
                            <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Primeiro Orçamento
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