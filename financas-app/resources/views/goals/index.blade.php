@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-bullseye me-2"></i>Metas Financeiras</h1>
                <a href="{{ route('goals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nova Meta
                </a>
            </div>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $totalGoals }}</h5>
                            <p class="card-text mb-0">Total de Metas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $activeGoals }}</h5>
                            <p class="card-text mb-0">Ativas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $completedGoals }}</h5>
                            <p class="card-text mb-0">Concluídas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $overdueGoals }}</h5>
                            <p class="card-text mb-0">Atrasadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h5 class="card-title">R$ {{ number_format($totalCurrentAmount, 2, ',', '.') }}</h5>
                            <p class="card-text mb-0">de R$ {{ number_format($totalTargetAmount, 2, ',', '.') }}</p>
                            <small>Progresso Total</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('goals.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluídas</option>
                                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Atrasadas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Metas -->
            <div class="row">
                @forelse($goals as $goal)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center" 
                             style="background-color: {{ $goal->color }}20; border-left: 4px solid {{ $goal->color }};">
                            <div class="d-flex align-items-center">
                                <i class="{{ $goal->icon }} me-2" style="color: {{ $goal->color }};"></i>
                                <h6 class="mb-0">{{ $goal->name }}</h6>
                            </div>
                            <span class="badge {{ $goal->status === 'completed' ? 'bg-success' : ($goal->status === 'cancelled' ? 'bg-secondary' : ($goal->target_date < now() ? 'bg-danger' : 'bg-primary')) }}">
                                {{ ucfirst($goal->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if($goal->description)
                            <p class="card-text text-muted small">{{ Str::limit($goal->description, 100) }}</p>
                            @endif
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Progresso:</small>
                                    <small>{{ number_format(($goal->current_amount / $goal->target_amount) * 100, 1) }}%</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ min(($goal->current_amount / $goal->target_amount) * 100, 100) }}%; background-color: {{ $goal->color }};"
                                         role="progressbar"></div>
                                </div>
                            </div>

                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <h6 class="mb-0">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</h6>
                                    <small class="text-muted">Atual</small>
                                </div>
                                <div class="col-6">
                                    <h6 class="mb-0">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</h6>
                                    <small class="text-muted">Meta</small>
                                </div>
                            </div>

                            <div class="text-center mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Prazo: {{ $goal->target_date->format('d/m/Y') }}
                                    @if($goal->target_date < now() && $goal->status !== 'completed')
                                        <span class="text-danger ms-1">(Atrasada)</span>
                                    @endif
                                </small>
                            </div>

                            @if($goal->monthly_contribution)
                            <div class="text-center mb-3">
                                <small class="text-info">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    R$ {{ number_format($goal->monthly_contribution, 2, ',', '.') }}/mês
                                </small>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                                <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                                <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta meta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i>Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                            <h4>Nenhuma meta encontrada</h4>
                            <p class="text-muted">Comece criando sua primeira meta financeira!</p>
                            <a href="{{ route('goals.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Criar primeira meta
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação -->
            @if($goals->hasPages())
            <div class="d-flex justify-content-center">
                {{ $goals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit do filtro quando mudar
document.getElementById('status').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endpush