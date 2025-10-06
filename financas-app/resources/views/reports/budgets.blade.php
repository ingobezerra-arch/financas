@extends('layouts.app')

@section('title', 'Relatório de Orçamentos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-pie text-success"></i>
                        Relatório de Orçamentos
                    </h1>
                    <p class="text-muted">Acompanhe o desempenho dos seus orçamentos</p>
                </div>
                <div>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar aos Relatórios
                    </a>
                </div>
            </div>

            <!-- Resumo dos Orçamentos -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-pie fa-2x mb-2"></i>
                            <h6 class="mb-1">Total Orçado</h6>
                            <h4 class="mb-0">R$ {{ number_format($budgetSummary['total_budgeted'], 2, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <h6 class="mb-1">Total Gasto</h6>
                            <h4 class="mb-0">R$ {{ number_format($budgetSummary['total_spent'], 2, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h6 class="mb-1">No Caminho Certo</h6>
                            <h4 class="mb-0">{{ $budgetSummary['on_track_count'] }}</h4>
                            <small>orçamentos (≤80%)</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h6 class="mb-1">Estourados</h6>
                            <h4 class="mb-0">{{ $budgetSummary['over_budget_count'] }}</h4>
                            <small>orçamentos</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progresso Geral -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i> Progresso Geral dos Orçamentos</h5>
                </div>
                <div class="card-body">
                    @if($budgetSummary['total_budgeted'] > 0)
                        @php
                            $overallPercentage = ($budgetSummary['total_spent'] / $budgetSummary['total_budgeted']) * 100;
                            $progressClass = $overallPercentage > 100 ? 'danger' : ($overallPercentage > 80 ? 'warning' : 'success');
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><strong>Progresso Geral</strong></span>
                            <span class="text-{{ $progressClass }}">
                                <strong>{{ number_format($overallPercentage, 1) }}%</strong>
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-{{ $progressClass }}" 
                                 role="progressbar" 
                                 style="width: {{ min($overallPercentage, 100) }}%"
                                 aria-valuenow="{{ $overallPercentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Gasto: R$ {{ number_format($budgetSummary['total_spent'], 2, ',', '.') }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Orçado: R$ {{ number_format($budgetSummary['total_budgeted'], 2, ',', '.') }}</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhum orçamento ativo encontrado.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lista Detalhada de Orçamentos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list mr-2"></i> Orçamentos Ativos</h5>
                </div>
                <div class="card-body">
                    @if($budgets->count() > 0)
                        <div class="row">
                            @foreach($budgets as $budget)
                                @php
                                    $percentage = $budget->amount > 0 ? ($budget->spent / $budget->amount) * 100 : 0;
                                    $progressClass = $percentage > 100 ? 'danger' : ($percentage > 80 ? 'warning' : 'success');
                                    $remaining = $budget->amount - $budget->spent;
                                @endphp
                                <div class="col-lg-6 col-md-12 mb-4">
                                    <div class="card h-100 border-{{ $progressClass }}">
                                        <div class="card-header bg-{{ $progressClass }} text-white">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    @if($budget->category)
                                                        <i class="{{ $budget->category->icon }}"></i>
                                                        {{ $budget->category->name }}
                                                    @else
                                                        <i class="fas fa-question-circle"></i>
                                                        Categoria não encontrada
                                                    @endif
                                                </h6>
                                                <span class="badge bg-light text-dark">
                                                    {{ number_format($percentage, 1) }}%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $budget->name }}</h6>
                                            
                                            <!-- Progresso -->
                                            <div class="progress mb-3" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $progressClass }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min($percentage, 100) }}%"
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>

                                            <!-- Valores -->
                                            <div class="row text-center mb-3">
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="text-success mb-0">R$ {{ number_format($budget->amount, 2, ',', '.') }}</h6>
                                                        <small class="text-muted">Orçado</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="text-{{ $percentage > 100 ? 'danger' : 'warning' }} mb-0">
                                                            R$ {{ number_format($budget->spent, 2, ',', '.') }}
                                                        </h6>
                                                        <small class="text-muted">Gasto</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="text-{{ $remaining >= 0 ? 'info' : 'danger' }} mb-0">
                                                        R$ {{ number_format(abs($remaining), 2, ',', '.') }}
                                                    </h6>
                                                    <small class="text-muted">{{ $remaining >= 0 ? 'Restante' : 'Excesso' }}</small>
                                                </div>
                                            </div>

                                            <!-- Período -->
                                            <div class="d-flex justify-content-between align-items-center text-muted">
                                                <small>
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $budget->start_date->format('d/m/Y') }} - {{ $budget->end_date->format('d/m/Y') }}
                                                </small>
                                                <small>
                                                    <i class="fas fa-clock"></i>
                                                    {{ ucfirst($budget->period) }}
                                                </small>
                                            </div>

                                            <!-- Status -->
                                            <div class="mt-2">
                                                @if($percentage > 100)
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        Orçamento Estourado
                                                    </span>
                                                @elseif($percentage > 80)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        Atenção - {{ number_format($percentage, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i>
                                                        No Caminho Certo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver Detalhes
                                                </a>
                                                <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum orçamento ativo</h5>
                            <p class="text-muted">
                                Você não possui orçamentos ativos no momento. 
                                <a href="{{ route('budgets.create') }}">Criar um orçamento</a> ajuda a controlar melhor seus gastos.
                            </p>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar tooltips aos badges de status
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        if (badge.textContent.includes('Estourado')) {
            badge.setAttribute('data-bs-toggle', 'tooltip');
            badge.setAttribute('title', 'Este orçamento ultrapassou o limite estabelecido');
        } else if (badge.textContent.includes('Atenção')) {
            badge.setAttribute('data-bs-toggle', 'tooltip');
            badge.setAttribute('title', 'Você já gastou mais de 80% do orçamento');
        } else if (badge.textContent.includes('Caminho Certo')) {
            badge.setAttribute('data-bs-toggle', 'tooltip');
            badge.setAttribute('title', 'Seus gastos estão dentro do previsto');
        }
    });

    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush