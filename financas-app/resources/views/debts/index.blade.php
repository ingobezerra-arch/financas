@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-credit-card"></i> Gerenciamento de Dívidas
                </h1>
                <a href="{{ route('debts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Dívida
                </a>
            </div>

            <!-- Estatísticas Resumidas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-list-alt fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">{{ $stats['total_debts'] }}</h5>
                            <p class="card-text small text-muted">Total de Dívidas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-exclamation-circle fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">{{ $stats['active_debts'] }}</h5>
                            <p class="card-text small text-muted">Dívidas Ativas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave fa-2x text-danger mb-2"></i>
                            <h5 class="card-title">R$ {{ number_format($stats['total_balance'], 2, ',', '.') }}</h5>
                            <p class="card-text small text-muted">Total Devedor</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar-week fa-2x text-info mb-2"></i>
                            <h5 class="card-title">R$ {{ number_format($stats['total_minimum_payments'], 2, ',', '.') }}</h5>
                            <p class="card-text small text-muted">Pagamentos Mínimos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-danger mb-2"></i>
                            <h5 class="card-title">{{ $stats['overdue_debts'] }}</h5>
                            <p class="card-text small text-muted">Em Atraso</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5 class="card-title">{{ $stats['paid_debts'] }}</h5>
                            <p class="card-text small text-muted">Quitadas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('debts.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Nome, credor ou descrição...">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Quitada</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Em Atraso</option>
                                    <option value="negotiated" {{ request('status') == 'negotiated' ? 'selected' : '' }}>Negociada</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="debt_type" class="form-label">Tipo</label>
                                <select class="form-select" id="debt_type" name="debt_type">
                                    <option value="">Todos</option>
                                    <option value="credit_card" {{ request('debt_type') == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                    <option value="loan" {{ request('debt_type') == 'loan' ? 'selected' : '' }}>Empréstimo</option>
                                    <option value="financing" {{ request('debt_type') == 'financing' ? 'selected' : '' }}>Financiamento</option>
                                    <option value="invoice" {{ request('debt_type') == 'invoice' ? 'selected' : '' }}>Fatura</option>
                                    <option value="other" {{ request('debt_type') == 'other' ? 'selected' : '' }}>Outro</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                    <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Dívidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Suas Dívidas ({{ $debts->total() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($debts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Dívida</th>
                                        <th>Tipo</th>
                                        <th>Saldo Atual</th>
                                        <th>Taxa Juros</th>
                                        <th>Pagamento Mínimo</th>
                                        <th>Status</th>
                                        <th>Progresso</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($debts as $debt)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $debt->name }}</strong>
                                                    @if($debt->creditor)
                                                        <br><small class="text-muted">{{ $debt->creditor }}</small>
                                                    @endif
                                                    @if($debt->is_overdue)
                                                        <span class="badge bg-danger ms-1">Vencida</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @switch($debt->debt_type)
                                                    @case('credit_card')
                                                        <span class="badge bg-primary">Cartão</span>
                                                        @break
                                                    @case('loan')
                                                        <span class="badge bg-warning">Empréstimo</span>
                                                        @break
                                                    @case('financing')
                                                        <span class="badge bg-info">Financiamento</span>
                                                        @break
                                                    @case('invoice')
                                                        <span class="badge bg-secondary">Fatura</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">Outro</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <strong class="text-danger">R$ {{ number_format($debt->current_balance, 2, ',', '.') }}</strong>
                                                @if($debt->original_amount > $debt->current_balance)
                                                    <br><small class="text-muted">de R$ {{ number_format($debt->original_amount, 2, ',', '.') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $debt->interest_rate > 5 ? 'danger' : ($debt->interest_rate > 2 ? 'warning' : 'success') }}">
                                                    {{ number_format($debt->interest_rate, 2) }}%
                                                </span>
                                            </td>
                                            <td>R$ {{ number_format($debt->minimum_payment, 2, ',', '.') }}</td>
                                            <td>
                                                @switch($debt->status)
                                                    @case('active')
                                                        <span class="badge bg-warning">Ativa</span>
                                                        @break
                                                    @case('paid')
                                                        <span class="badge bg-success">Quitada</span>
                                                        @break
                                                    @case('overdue')
                                                        <span class="badge bg-danger">Em Atraso</span>
                                                        @break
                                                    @case('negotiated')
                                                        <span class="badge bg-info">Negociada</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $debt->percentage_paid }}%"
                                                         aria-valuenow="{{ $debt->percentage_paid }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ number_format($debt->percentage_paid, 1) }}% pago</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-primary" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('debts.edit', $debt) }}" class="btn btn-outline-secondary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('debts.simulate', $debt) }}" class="btn btn-outline-info" title="Simular">
                                                        <i class="fas fa-calculator"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $debts->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5>Nenhuma dívida encontrada</h5>
                            <p class="text-muted">Você não possui dívidas cadastradas ou nenhuma corresponde aos filtros aplicados.</p>
                            <a href="{{ route('debts.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Cadastrar Primeira Dívida
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Links Rápidos -->
            @if($stats['active_debts'] > 1)
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-magic fa-2x text-primary mb-3"></i>
                            <h5>Criar Plano de Pagamento</h5>
                            <p class="text-muted">Use estratégias como Bola de Neve ou Avalanche para quitar suas dívidas mais rapidamente.</p>
                            <a href="{{ route('payment-plans.create') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-alt"></i> Criar Plano
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-2x text-success mb-3"></i>
                            <h5>Comparar Estratégias</h5>
                            <p class="text-muted">Compare diferentes estratégias de pagamento e veja qual economiza mais tempo e dinheiro.</p>
                            <form action="{{ route('payment-plans.compare-strategies') }}" method="POST" style="display: inline;">
                                @csrf
                                @foreach(auth()->user()->debts()->active()->get() as $debt)
                                    <input type="hidden" name="debt_ids[]" value="{{ $debt->id }}">
                                @endforeach
                                <input type="hidden" name="monthly_budget" value="1000">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-balance-scale"></i> Comparar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection