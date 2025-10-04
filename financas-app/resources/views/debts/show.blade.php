@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-eye"></i> Detalhes da Dívida: {{ $debt->name }}
                </h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('debts.edit', $debt) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('debts.simulate', $debt) }}" class="btn btn-info">
                        <i class="fas fa-calculator"></i> Simular
                    </a>
                    <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações Principais -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informações da Dívida</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nome:</strong></td>
                                            <td>{{ $debt->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo:</strong></td>
                                            <td>
                                                @switch($debt->debt_type)
                                                    @case('credit_card')
                                                        <span class="badge bg-primary">Cartão de Crédito</span>
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
                                        </tr>
                                        @if($debt->creditor)
                                        <tr>
                                            <td><strong>Credor:</strong></td>
                                            <td>{{ $debt->creditor }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Status:</strong></td>
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
                                                @if($debt->is_overdue)
                                                    <span class="badge bg-danger ms-1">Vencida</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($debt->due_date)
                                        <tr>
                                            <td><strong>Vencimento:</strong></td>
                                            <td>{{ $debt->due_date->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Valor Original:</strong></td>
                                            <td class="text-primary">R$ {{ number_format($debt->original_amount, 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Saldo Atual:</strong></td>
                                            <td class="text-danger">
                                                <strong>R$ {{ number_format($debt->current_balance, 2, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Taxa de Juros:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $debt->interest_rate > 5 ? 'danger' : ($debt->interest_rate > 2 ? 'warning' : 'success') }}">
                                                    {{ number_format($debt->interest_rate, 2) }}% a.m.
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pagamento Mínimo:</strong></td>
                                            <td>R$ {{ number_format($debt->minimum_payment, 2, ',', '.') }}</td>
                                        </tr>
                                        @if($debt->installments_total)
                                        <tr>
                                            <td><strong>Parcelas:</strong></td>
                                            <td>{{ $debt->installments_paid }}/{{ $debt->installments_total }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if($debt->description)
                            <hr>
                            <div>
                                <strong>Descrição:</strong>
                                <p class="mt-2">{{ $debt->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Chart -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Progresso de Pagamento</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $debt->percentage_paid }}%"
                                             aria-valuenow="{{ $debt->percentage_paid }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($debt->percentage_paid, 1) }}%
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <small class="text-muted">Valor Pago</small>
                                            <div class="text-success">
                                                R$ {{ number_format($debt->original_amount - $debt->current_balance, 2, ',', '.') }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Saldo Restante</small>
                                            <div class="text-danger">
                                                R$ {{ number_format($debt->current_balance, 2, ',', '.') }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Progresso</small>
                                            <div class="text-primary">
                                                {{ number_format($debt->percentage_paid, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="bg-light p-3 rounded">
                                        <h4 class="text-danger mb-1">{{ $projections['months_to_payoff'] }}</h4>
                                        <small class="text-muted">meses para quitar<br>(pagamento mínimo)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Simulações -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Simulações de Pagamento</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Cenário</th>
                                            <th>Valor do Pagamento</th>
                                            <th>Novo Saldo</th>
                                            <th>Juros do Mês</th>
                                            <th>Principal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Pagamento Mínimo</td>
                                            <td>R$ {{ number_format($simulations['minimum_only']['payment_amount'], 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($simulations['minimum_only']['new_balance'], 2, ',', '.') }}</td>
                                            <td class="text-danger">R$ {{ number_format($simulations['minimum_only']['interest_amount'], 2, ',', '.') }}</td>
                                            <td class="text-success">R$ {{ number_format($simulations['minimum_only']['principal_amount'], 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dobrar Mínimo</td>
                                            <td>R$ {{ number_format($simulations['double_minimum']['payment_amount'], 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($simulations['double_minimum']['new_balance'], 2, ',', '.') }}</td>
                                            <td class="text-danger">R$ {{ number_format($simulations['double_minimum']['interest_amount'], 2, ',', '.') }}</td>
                                            <td class="text-success">R$ {{ number_format($simulations['double_minimum']['principal_amount'], 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>+ R$ 100 Extra</td>
                                            <td>R$ {{ number_format($simulations['extra_100']['payment_amount'], 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($simulations['extra_100']['new_balance'], 2, ',', '.') }}</td>
                                            <td class="text-danger">R$ {{ number_format($simulations['extra_100']['interest_amount'], 2, ',', '.') }}</td>
                                            <td class="text-success">R$ {{ number_format($simulations['extra_100']['principal_amount'], 2, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('debts.simulate', $debt) }}" class="btn btn-info">
                                    <i class="fas fa-calculator"></i> Ver Mais Simulações
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com Ações -->
                <div class="col-md-4">
                    <!-- Registrar Pagamento -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Registrar Pagamento</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('debts.record-payment', $debt) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Valor do Pagamento</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="0.01" placeholder="0,00" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Data do Pagamento</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Observações</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" 
                                              placeholder="Observações sobre o pagamento..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Registrar Pagamento
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Resumo Mensal -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-chart-line"></i> Resumo Mensal</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <small class="text-muted">Juros Mensal</small>
                                    <div class="text-danger h5">
                                        R$ {{ number_format($projections['monthly_interest'], 2, ',', '.') }}
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <small class="text-muted">Se Pagar Mínimo</small>
                                    <div class="text-warning">
                                        {{ $projections['months_to_payoff'] > 100 ? '99+' : $projections['months_to_payoff'] }} meses
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Histórico de Pagamentos -->
                    @if($debt->paymentSchedules->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-history"></i> Últimos Pagamentos</h6>
                        </div>
                        <div class="card-body">
                            @foreach($debt->paymentSchedules->take(5) as $schedule)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <small class="text-muted">{{ $schedule->due_date->format('d/m/Y') }}</small>
                                    <div class="small">
                                        @if($schedule->status == 'paid')
                                            <span class="badge bg-success">Pago</span>
                                        @elseif($schedule->status == 'overdue')
                                            <span class="badge bg-danger">Atrasado</span>
                                        @else
                                            <span class="badge bg-warning">Pendente</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">R$ {{ number_format($schedule->payment_amount, 2, ',', '.') }}</div>
                                    @if($schedule->paid_amount)
                                        <small class="text-muted">Pago: R$ {{ number_format($schedule->paid_amount, 2, ',', '.') }}</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Planos de Pagamento -->
                    @if($debt->paymentPlans->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Planos de Pagamento</h6>
                        </div>
                        <div class="card-body">
                            @foreach($debt->paymentPlans as $plan)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">{{ $plan->name }}</span>
                                    <span class="badge bg-{{ $plan->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ ucfirst($plan->strategy) }}</small>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar" style="width: {{ $plan->progress_percentage }}%"></div>
                                </div>
                            </div>
                            @endforeach
                            <a href="{{ route('payment-plans.index') }}" class="btn btn-outline-primary btn-sm w-100">
                                Ver Todos os Planos
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill common payment amounts
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const minimumPayment = {{ $debt->minimum_payment }};
    
    // Add quick buttons for common amounts
    const quickAmounts = [minimumPayment, minimumPayment * 2, minimumPayment + 100];
    const container = amountInput.parentNode.parentNode;
    
    const quickButtonsDiv = document.createElement('div');
    quickButtonsDiv.className = 'mt-2';
    quickButtonsDiv.innerHTML = '<small class="text-muted">Valores rápidos:</small><br>';
    
    quickAmounts.forEach(amount => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-secondary btn-sm me-1 mt-1';
        btn.textContent = 'R$ ' + amount.toFixed(2);
        btn.onclick = () => amountInput.value = amount.toFixed(2);
        quickButtonsDiv.appendChild(btn);
    });
    
    container.appendChild(quickButtonsDiv);
});
</script>
@endsection