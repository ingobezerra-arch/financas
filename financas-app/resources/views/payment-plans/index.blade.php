@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-calendar-alt"></i> Planos de Pagamento
                </h1>
                <a href="{{ route('payment-plans.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Plano
                </a>
            </div>

            <!-- Estatísticas Resumidas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-list-alt fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">{{ $stats['total_plans'] }}</h5>
                            <p class="card-text small text-muted">Total de Planos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-play-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">{{ $stats['active_plans'] }}</h5>
                            <p class="card-text small text-muted">Planos Ativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">{{ $stats['completed_plans'] }}</h5>
                            <p class="card-text small text-muted">Concluídos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">R$ {{ number_format($stats['total_debt_in_plans'], 2, ',', '.') }}</h5>
                            <p class="card-text small text-muted">Valor em Planos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Planos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Seus Planos de Pagamento ({{ $paymentPlans->total() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($paymentPlans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Plano</th>
                                        <th>Estratégia</th>
                                        <th>Dívidas</th>
                                        <th>Valor Total</th>
                                        <th>Orçamento Mensal</th>
                                        <th>Progresso</th>
                                        <th>Status</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentPlans as $plan)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $plan->name }}</strong>
                                                    <br><small class="text-muted">Criado em {{ $plan->created_at->format('d/m/Y') }}</small>
                                                    @if($plan->projected_end_date)
                                                        <br><small class="text-info">Previsão: {{ $plan->projected_end_date->format('d/m/Y') }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @switch($plan->strategy)
                                                    @case('snowball')
                                                        <span class="badge bg-info">Bola de Neve</span>
                                                        <br><small class="text-muted">Menor saldo primeiro</small>
                                                        @break
                                                    @case('avalanche')
                                                        <span class="badge bg-danger">Avalanche</span>
                                                        <br><small class="text-muted">Maior juros primeiro</small>
                                                        @break
                                                    @case('custom')
                                                        <span class="badge bg-primary">Personalizada</span>
                                                        <br><small class="text-muted">Ordem customizada</small>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <strong>{{ $plan->debts->count() }}</strong> dívidas
                                                <br><small class="text-muted">{{ $plan->debts->where('pivot.status', 'active')->count() }} ativas</small>
                                            </td>
                                            <td>
                                                <strong>R$ {{ number_format($plan->total_debt_amount, 2, ',', '.') }}</strong>
                                                <br><small class="text-muted">R$ {{ number_format($plan->total_remaining, 2, ',', '.') }} restante</small>
                                            </td>
                                            <td>
                                                R$ {{ number_format($plan->monthly_budget, 2, ',', '.') }}
                                                @if($plan->extra_payment > 0)
                                                    <br><small class="text-success">+ R$ {{ number_format($plan->extra_payment, 2, ',', '.') }} extra</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress mb-1" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $plan->progress_percentage }}%"
                                                         aria-valuenow="{{ $plan->progress_percentage }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ number_format($plan->progress_percentage, 1) }}%</small>
                                                @if($plan->is_on_track)
                                                    <i class="fas fa-check-circle text-success" title="No cronograma"></i>
                                                @else
                                                    <i class="fas fa-exclamation-triangle text-warning" title="Atrasado"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($plan->status)
                                                    @case('active')
                                                        <span class="badge bg-success">Ativo</span>
                                                        @break
                                                    @case('paused')
                                                        <span class="badge bg-warning">Pausado</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-primary">Concluído</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-secondary">Cancelado</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('payment-plans.show', $plan) }}" class="btn btn-outline-primary" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($plan->status == 'active')
                                                        <a href="{{ route('payment-plans.edit', $plan) }}" class="btn btn-outline-secondary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('payment-plans.toggle-status', $plan) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-outline-warning" title="Pausar">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($plan->status == 'paused')
                                                        <form action="{{ route('payment-plans.toggle-status', $plan) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-outline-success" title="Reativar">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $paymentPlans->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>Nenhum plano de pagamento encontrado</h5>
                            <p class="text-muted">Crie um plano para organizar o pagamento de suas dívidas de forma estratégica.</p>
                            <a href="{{ route('payment-plans.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Primeiro Plano
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Links Rápidos -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                            <h5>Próximos Pagamentos</h5>
                            <p class="text-muted">Veja os pagamentos programados para os próximos dias.</p>
                            <a href="{{ route('payment-schedules.upcoming') }}" class="btn btn-warning">
                                <i class="fas fa-calendar-week"></i> Ver Cronograma
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                            <h5>Pagamentos em Atraso</h5>
                            <p class="text-muted">Gerencie pagamentos que já venceram.</p>
                            <a href="{{ route('payment-schedules.overdue') }}" class="btn btn-danger">
                                <i class="fas fa-clock"></i> Ver Atrasados
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-balance-scale fa-2x text-info mb-3"></i>
                            <h5>Comparar Estratégias</h5>
                            <p class="text-muted">Compare diferentes métodos de pagamento.</p>
                            <a href="{{ route('debts.index') }}" class="btn btn-info">
                                <i class="fas fa-chart-line"></i> Comparar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection