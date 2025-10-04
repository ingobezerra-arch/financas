@extends('layouts.app')

@section('title', 'Relatório de Metas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-bullseye text-info"></i>
                        Relatório de Metas
                    </h1>
                    <p class="text-muted">Acompanhe o progresso das suas metas financeiras</p>
                </div>
                <div>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar aos Relatórios
                    </a>
                </div>
            </div>

            <!-- Resumo das Metas -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-bullseye fa-2x mb-2"></i>
                            <h6 class="mb-1">Total de Metas</h6>
                            <h4 class="mb-0">{{ $goalsSummary['total_goals'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-play-circle fa-2x mb-2"></i>
                            <h6 class="mb-1">Metas Ativas</h6>
                            <h4 class="mb-0">{{ $goalsSummary['active_goals'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h6 class="mb-1">Metas Concluídas</h6>
                            <h4 class="mb-0">{{ $goalsSummary['completed_goals'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-piggy-bank fa-2x mb-2"></i>
                            <h6 class="mb-1">Total Economizado</h6>
                            <h4 class="mb-0">R$ {{ number_format($goalsSummary['total_saved'], 2, ',', '.') }}</h4>
                            <small>de R$ {{ number_format($goalsSummary['total_target'], 2, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progresso Geral -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Progresso Geral das Metas</h5>
                </div>
                <div class="card-body">
                    @if($goalsSummary['total_target'] > 0)
                        @php
                            $overallPercentage = ($goalsSummary['total_saved'] / $goalsSummary['total_target']) * 100;
                            $progressClass = $overallPercentage >= 100 ? 'success' : ($overallPercentage >= 75 ? 'info' : ($overallPercentage >= 50 ? 'warning' : 'danger'));
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><strong>Progresso Geral</strong></span>
                            <span class="text-{{ $progressClass }}">
                                <strong>{{ number_format($overallPercentage, 1) }}%</strong>
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 15px;">
                            <div class="progress-bar bg-{{ $progressClass }}" 
                                 role="progressbar" 
                                 style="width: {{ min($overallPercentage, 100) }}%"
                                 aria-valuenow="{{ $overallPercentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($overallPercentage, 1) }}%
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Economizado: R$ {{ number_format($goalsSummary['total_saved'], 2, ',', '.') }}</small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Meta: R$ {{ number_format($goalsSummary['total_target'], 2, ',', '.') }}</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhuma meta ativa encontrada.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lista Detalhada de Metas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Todas as Metas</h5>
                </div>
                <div class="card-body">
                    @if($goals->count() > 0)
                        <div class="row">
                            @foreach($goals as $goal)
                                @php
                                    $percentage = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                                    $progressClass = $percentage >= 100 ? 'success' : ($percentage >= 75 ? 'info' : ($percentage >= 50 ? 'warning' : 'danger'));
                                    $remaining = $goal->target_amount - $goal->current_amount;
                                    $daysRemaining = $goal->target_date ? \Carbon\Carbon::now()->diffInDays($goal->target_date, false) : 0;
                                @endphp
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card h-100 {{ $goal->status === 'completed' ? 'border-success' : ($goal->status === 'active' ? 'border-primary' : 'border-secondary') }}">
                                        <div class="card-header {{ $goal->status === 'completed' ? 'bg-success' : ($goal->status === 'active' ? 'bg-primary' : 'bg-secondary') }} text-white">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <i class="{{ $goal->icon ?? 'fas fa-bullseye' }}" style="color: {{ $goal->color ?? '#ffffff' }};"></i>
                                                    {{ Str::limit($goal->name, 20) }}
                                                </h6>
                                                <span class="badge bg-light text-dark">
                                                    {{ number_format($percentage, 1) }}%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- Descrição -->
                                            @if($goal->description)
                                                <p class="text-muted small mb-3">{{ Str::limit($goal->description, 80) }}</p>
                                            @endif
                                            
                                            <!-- Progresso -->
                                            <div class="progress mb-3" style="height: 10px;">
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
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <h6 class="text-success mb-0">R$ {{ number_format($goal->current_amount, 2, ',', '.') }}</h6>
                                                        <small class="text-muted">Economizado</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <h6 class="text-primary mb-0">R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</h6>
                                                    <small class="text-muted">Meta</small>
                                                </div>
                                            </div>

                                            @if($goal->status === 'active')
                                                <!-- Restante para atingir a meta -->
                                                <div class="text-center mb-3">
                                                    <h6 class="text-{{ $remaining > 0 ? 'warning' : 'success' }} mb-0">
                                                        R$ {{ number_format(abs($remaining), 2, ',', '.') }}
                                                    </h6>
                                                    <small class="text-muted">{{ $remaining > 0 ? 'restante' : 'excedente' }}</small>
                                                </div>

                                                <!-- Contribuição mensal -->
                                                @if($goal->monthly_contribution && $goal->monthly_contribution > 0)
                                                    <div class="text-center mb-3">
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-calendar-plus"></i>
                                                            R$ {{ number_format($goal->monthly_contribution, 2, ',', '.') }}/mês
                                                        </span>
                                                    </div>
                                                @endif

                                                <!-- Data limite -->
                                                @if($goal->target_date)
                                                    <div class="text-center">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            Meta: {{ $goal->target_date->format('d/m/Y') }}
                                                            @if($daysRemaining > 0)
                                                                <br>
                                                                <span class="text-{{ $daysRemaining <= 30 ? 'danger' : ($daysRemaining <= 90 ? 'warning' : 'info') }}">
                                                                    {{ $daysRemaining }} dias restantes
                                                                </span>
                                                            @elseif($daysRemaining < 0)
                                                                <br>
                                                                <span class="text-danger">
                                                                    Prazo vencido há {{ abs($daysRemaining) }} dias
                                                                </span>
                                                            @else
                                                                <br>
                                                                <span class="text-warning">Vence hoje!</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                @endif
                                            @endif

                                            <!-- Status -->
                                            <div class="mt-3">
                                                @if($goal->status === 'completed')
                                                    <span class="badge bg-success w-100">
                                                        <i class="fas fa-trophy"></i>
                                                        Meta Concluída
                                                    </span>
                                                @elseif($goal->status === 'active')
                                                    @if($percentage >= 90)
                                                        <span class="badge bg-success w-100">
                                                            <i class="fas fa-star"></i>
                                                            Quase lá! {{ number_format($percentage, 1) }}%
                                                        </span>
                                                    @elseif($percentage >= 50)
                                                        <span class="badge bg-info w-100">
                                                            <i class="fas fa-chart-line"></i>
                                                            Bom progresso - {{ number_format($percentage, 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning w-100">
                                                            <i class="fas fa-hourglass-half"></i>
                                                            Precisa acelerar - {{ number_format($percentage, 1) }}%
                                                        </span>
                                                    @endif
                                                @elseif($goal->status === 'paused')
                                                    <span class="badge bg-secondary w-100">
                                                        <i class="fas fa-pause"></i>
                                                        Meta Pausada
                                                    </span>
                                                @else
                                                    <span class="badge bg-dark w-100">
                                                        <i class="fas fa-times"></i>
                                                        Meta Cancelada
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Período -->
                                            @if($goal->start_date)
                                                <div class="text-center mt-2">
                                                    <small class="text-muted">
                                                        Iniciado em {{ $goal->start_date->format('d/m/Y') }}
                                                        @if($goal->target_date)
                                                            · Duração: {{ $goal->start_date->diffInDays($goal->target_date) }} dias
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver Detalhes
                                                </a>
                                                @if($goal->status === 'active')
                                                    <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma meta cadastrada</h5>
                            <p class="text-muted">
                                Você ainda não possui metas financeiras. 
                                <a href="{{ route('goals.create') }}">Criar uma meta</a> ajuda a organizar seus objetivos financeiros.
                            </p>
                            <a href="{{ route('goals.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Primeira Meta
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
    // Adicionar tooltips aos badges de progresso
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        if (badge.textContent.includes('Quase lá')) {
            badge.setAttribute('data-bs-toggle', 'tooltip');
            badge.setAttribute('title', 'Você está muito perto de atingir sua meta!');
        } else if (badge.textContent.includes('Bom progresso')) {
            badge.setAttribute('data-bs-toggle', 'tooltip');
            badge.setAttribute('title', 'Está no caminho certo para atingir sua meta');
        } else if (badge.textContent.includes('Precisa acelerar')) {
            badge.setAttribute('data-bs-toggle', 'tooltip');
            badge.setAttribute('title', 'Considere aumentar suas contribuições mensais');
        }
    });

    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Destacar metas com prazo próximo
    const warningCards = document.querySelectorAll('.text-danger, .text-warning');
    warningCards.forEach(element => {
        if (element.textContent.includes('dias restantes') || element.textContent.includes('Vence hoje')) {
            element.closest('.card').classList.add('shadow-lg');
        }
    });
});
</script>
@endpush