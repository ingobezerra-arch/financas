@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="{{ $goal->icon }} me-2" style="color: {{ $goal->color }};"></i>{{ $goal->name }}</h1>
                <div>
                    <a href="{{ route('goals.edit', $goal) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações Principais -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header" style="background-color: {{ $goal->color }}20; border-left: 4px solid {{ $goal->color }};">
                            <h5 class="mb-0">Informações da Meta</h5>
                        </div>
                        <div class="card-body">
                            @if($goal->description)
                            <p class="card-text">{{ $goal->description }}</p>
                            @endif

                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <h4 class="text-primary">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</h4>
                                    <small class="text-muted">Valor Atual</small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <h4 class="text-success">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</h4>
                                    <small class="text-muted">Meta</small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <h4 class="text-info">R$ {{ number_format($goal->target_amount - $goal->current_amount, 2, ',', '.') }}</h4>
                                    <small class="text-muted">Restante</small>
                                </div>
                                <div class="col-md-3 text-center">
                                    <h4 style="color: {{ $goal->color }};">{{ number_format(($goal->current_amount / $goal->target_amount) * 100, 1) }}%</h4>
                                    <small class="text-muted">Progresso</small>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Progresso da Meta:</span>
                                    <span>{{ number_format(($goal->current_amount / $goal->target_amount) * 100, 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" style="width: {{ min(($goal->current_amount / $goal->target_amount) * 100, 100) }}%; background-color: {{ $goal->color }};"
                                         role="progressbar"></div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <p><strong>Data de Início:</strong> {{ $goal->start_date->format('d/m/Y') }}</p>
                                    <p><strong>Data da Meta:</strong> {{ $goal->target_date->format('d/m/Y') }}</p>
                                    @if($goal->monthly_contribution)
                                    <p><strong>Contribuição Mensal:</strong> R$ {{ number_format($goal->monthly_contribution, 2, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge {{ $goal->status === 'completed' ? 'bg-success' : ($goal->status === 'cancelled' ? 'bg-secondary' : 'bg-primary') }}">
                                            {{ ucfirst($goal->status) }}
                                        </span>
                                    </p>
                                    <p><strong>Dias para a Meta:</strong> 
                                        @php
                                            $daysToGoal = now()->diffInDays($goal->target_date, false);
                                        @endphp
                                        @if($daysToGoal > 0)
                                            <span class="text-info">{{ $daysToGoal }} dias</span>
                                        @elseif($daysToGoal === 0)
                                            <span class="text-warning">Hoje!</span>
                                        @else
                                            <span class="text-danger">{{ abs($daysToGoal) }} dias atrasada</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas para Contribuições -->
                    @if($goal->status === 'active')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Gerenciar Contribuições</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Adicionar Contribuição</h6>
                                    <form action="{{ route('goals.addContribution', $goal) }}" method="POST">
                                        @csrf
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="Valor" required>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-plus"></i> Adicionar
                                            </button>
                                        </div>
                                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Descrição (opcional)">
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <h6>Remover Valor</h6>
                                    <form action="{{ route('goals.removeContribution', $goal) }}" method="POST">
                                        @csrf
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" min="0.01" max="{{ $goal->current_amount }}" name="amount" class="form-control" placeholder="Valor" required>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-minus"></i> Remover
                                            </button>
                                        </div>
                                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Motivo (opcional)">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Status e Ações -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Ações</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <form action="{{ route('goals.toggleStatus', $goal) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        @if($goal->status === 'active')
                                            <i class="fas fa-pause me-1"></i>Cancelar Meta
                                        @elseif($goal->status === 'cancelled')
                                            <i class="fas fa-play me-1"></i>Reativar Meta
                                        @else
                                            <i class="fas fa-play me-1"></i>Reativar Meta
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('goals.destroy', $goal) }}" method="POST" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta meta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash me-1"></i>Excluir Meta
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Calculadora de Progresso -->
                    @if($goal->status === 'active' && $goal->monthly_contribution)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Previsão de Conclusão</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $remainingAmount = $goal->target_amount - $goal->current_amount;
                                $monthsNeeded = $remainingAmount > 0 && $goal->monthly_contribution > 0 
                                    ? ceil($remainingAmount / $goal->monthly_contribution) 
                                    : 0;
                                $estimatedDate = now()->addMonths($monthsNeeded);
                            @endphp

                            @if($monthsNeeded > 0)
                            <p class="mb-2">
                                <strong>Faltam:</strong><br>
                                <span class="text-primary">{{ $monthsNeeded }} meses</span>
                            </p>
                            <p class="mb-2">
                                <strong>Data estimada:</strong><br>
                                <span class="text-success">{{ $estimatedDate->format('d/m/Y') }}</span>
                            </p>
                            <small class="text-muted">
                                Baseado na contribuição mensal de R$ {{ number_format($goal->monthly_contribution, 2, ',', '.') }}
                            </small>
                            @else
                            <p class="text-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Meta já alcançada!
                            </p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection