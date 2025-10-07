@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-calculator"></i> Simulador de Pagamentos
                </h1>
                <div>
                    <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> Ver Dívida
                    </a>
                    <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Informações da Dívida -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $debt->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Saldo Atual</h6>
                                <h4 class="text-danger">R$ {{ number_format($debt->current_balance, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Taxa de Juros</h6>
                                <h4 class="text-warning">{{ number_format($debt->interest_rate, 2, ',', '.') }}% a.m.</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Pagamento Mínimo</h6>
                                <h4 class="text-info">R$ {{ number_format($debt->minimum_payment, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Tipo</h6>
                                <h4 class="text-secondary">
                                    @switch($debt->debt_type)
                                        @case('credit_card')
                                            <i class="fas fa-credit-card"></i> Cartão
                                            @break
                                        @case('loan')
                                            <i class="fas fa-handshake"></i> Empréstimo
                                            @break
                                        @case('financing')
                                            <i class="fas fa-car"></i> Financiamento
                                            @break
                                        @case('invoice')
                                            <i class="fas fa-file-invoice"></i> Fatura
                                            @break
                                        @default
                                            <i class="fas fa-question-circle"></i> Outro
                                    @endswitch
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Simulações de Pagamento -->
            <div class="row">
                @foreach($scenarios as $key => $scenario)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 {{ $key === 'current' ? 'border-primary' : ($key === 'extra_100' ? 'border-success' : '') }}">
                        <div class="card-header {{ $key === 'current' ? 'bg-primary text-white' : ($key === 'extra_100' ? 'bg-success text-white' : 'bg-light') }}">
                            <h6 class="mb-0">
                                @if($key === 'current')
                                    <i class="fas fa-clock me-2"></i>
                                @else
                                    <i class="fas fa-rocket me-2"></i>
                                @endif
                                {{ $scenario['name'] }}
                                @if($key === 'extra_100')
                                    <span class="badge bg-warning text-dark ms-2">Recomendado</span>
                                @endif
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h3 class="text-primary">R$ {{ number_format($scenario['payment'], 2, ',', '.') }}</h3>
                                <small class="text-muted">por mês</small>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h5 class="text-info">{{ $scenario['months'] }}</h5>
                                        <small class="text-muted">meses</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-success">R$ {{ number_format($scenario['total_paid'], 2, ',', '.') }}</h5>
                                    <small class="text-muted">total pago</small>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <small class="text-muted">Total de Juros:</small>
                                <h6 class="text-danger">R$ {{ number_format($scenario['total_interest'], 2, ',', '.') }}</h6>
                            </div>
                            
                            @if(isset($scenario['savings']) && $scenario['savings'] > 0)
                                <div class="alert alert-success py-2">
                                    <small>
                                        <i class="fas fa-piggy-bank me-1"></i>
                                        <strong>Economia:</strong> R$ {{ number_format($scenario['savings'], 2, ',', '.') }}
                                        <br>
                                        <i class="fas fa-calendar me-1"></i>
                                        <strong>Tempo:</strong> {{ $scenario['months_saved'] }} meses a menos
                                    </small>
                                </div>
                            @endif
                            
                            @if($key === 'current')
                                <div class="alert alert-warning py-2">
                                    <small>
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Cenário atual - apenas pagamento mínimo
                                    </small>
                                </div>
                            @endif
                        </div>
                        
                        @if($key !== 'current')
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-outline-primary btn-sm w-100" 
                                    data-scenario="{{ $key }}" 
                                    data-payment="{{ $scenario['payment'] }}" 
                                    data-months="{{ $scenario['months'] }}" 
                                    onclick="showPaymentPlan(this.dataset.scenario, this.dataset.payment, this.dataset.months)">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Ver Cronograma
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Simulador Personalizado -->
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-sliders-h me-2"></i>
                        Simulador Personalizado
                    </h5>
                </div>
                <div class="card-body">
                    <form id="customSimulation">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="customPayment" class="form-label">Valor do Pagamento Mensal</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control" id="customPayment" 
                                           min="{{ $debt->minimum_payment }}" step="0.01" 
                                           value="{{ $debt->minimum_payment }}"
                                           placeholder="{{ number_format($debt->minimum_payment, 2, ',', '.') }}">
                                </div>
                                <small class="text-muted">Mínimo: R$ {{ number_format($debt->minimum_payment, 2, ',', '.') }}</small>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" onclick="calculateCustom()">
                                    <i class="fas fa-calculator me-1"></i>
                                    Calcular
                                </button>
                            </div>
                            <div class="col-md-5">
                                <div id="customResult" class="alert alert-info d-none">
                                    <!-- Resultado será exibido aqui -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dicas e Recomendações -->
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Dicas para Quitar Mais Rápido
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-line text-success me-2"></i>Estratégias Eficientes:</h6>
                            <ul>
                                <li>Pague sempre mais que o mínimo</li>
                                <li>Considere quitar dívidas com maior taxa primeiro</li>
                                <li>Use dinheiro extra (13º, férias) para amortizar</li>
                                <li>Renegocie a taxa de juros se possível</li>
                                <li>Evite fazer novas dívidas</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-calculator text-info me-2"></i>Como Interpretar:</h6>
                            <ul>
                                <li><strong>Meses:</strong> Tempo para quitar completamente</li>
                                <li><strong>Total Pago:</strong> Soma de todos os pagamentos</li>
                                <li><strong>Juros:</strong> Diferença entre total pago e saldo atual</li>
                                <li><strong>Economia:</strong> Quanto você economiza vs. mínimo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cronograma Detalhado -->
<div class="modal fade" id="paymentPlanModal" tabindex="-1" aria-labelledby="paymentPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentPlanModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Cronograma de Pagamentos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="paymentSchedule">
                    <!-- Cronograma será gerado dinamicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Dados da dívida para cálculos JavaScript
const debtData = {
    balance: {{ $debt->current_balance }},
    rate: {{ $debt->interest_rate / 100 }},
    minimumPayment: {{ $debt->minimum_payment }}
};

function calculateCustom() {
    const paymentInput = document.getElementById('customPayment');
    const resultDiv = document.getElementById('customResult');
    
    const monthlyPayment = parseFloat(paymentInput.value);
    
    if (monthlyPayment < debtData.minimumPayment) {
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> O pagamento deve ser pelo menos R$ ${debtData.minimumPayment.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
        resultDiv.classList.remove('d-none');
        return;
    }
    
    const result = simulatePayment(debtData.balance, debtData.rate, monthlyPayment);
    
    if (result.months > 360) {
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Com este valor, a dívida nunca será quitada!`;
    } else {
        resultDiv.className = 'alert alert-success';
        resultDiv.innerHTML = `
            <strong><i class="fas fa-check-circle me-1"></i> Resultado:</strong><br>
            <small>
                <strong>${result.months} meses</strong> para quitar<br>
                <strong>R$ ${result.totalPaid.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</strong> total pago<br>
                <strong>R$ ${result.totalInterest.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</strong> em juros
            </small>
        `;
    }
    
    resultDiv.classList.remove('d-none');
}

function simulatePayment(balance, monthlyRate, payment) {
    let currentBalance = balance;
    let months = 0;
    let totalPaid = 0;
    
    while (currentBalance > 0.01 && months < 360) {
        const interestAmount = currentBalance * monthlyRate;
        const principalAmount = payment - interestAmount;
        
        if (principalAmount <= 0) {
            return { months: 999, totalPaid: 0, totalInterest: 0 };
        }
        
        currentBalance -= principalAmount;
        totalPaid += payment;
        months++;
        
        if (currentBalance < 0) {
            totalPaid += currentBalance; // Ajusta o último pagamento
            currentBalance = 0;
        }
    }
    
    return {
        months: months,
        totalPaid: totalPaid,
        totalInterest: totalPaid - balance
    };
}

function showPaymentPlan(scenario, monthlyPayment, totalMonths) {
    const modalLabel = document.getElementById('paymentPlanModalLabel');
    const scheduleDiv = document.getElementById('paymentSchedule');
    
    modalLabel.innerHTML = `<i class="fas fa-calendar-alt me-2"></i>Cronograma - R$ ${monthlyPayment.toLocaleString('pt-BR', {minimumFractionDigits: 2})}/mês`;
    
    // Gerar cronograma detalhado (primeiros 12 meses)
    let html = `
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Mês</th>
                        <th>Pagamento</th>
                        <th>Juros</th>
                        <th>Principal</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    let balance = debtData.balance;
    const monthsToShow = Math.min(12, totalMonths);
    
    for (let month = 1; month <= monthsToShow; month++) {
        const interestAmount = balance * debtData.rate;
        const principalAmount = monthlyPayment - interestAmount;
        balance -= principalAmount;
        
        if (balance < 0) balance = 0;
        
        html += `
            <tr>
                <td>${month}</td>
                <td>R$ ${monthlyPayment.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                <td>R$ ${interestAmount.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                <td>R$ ${principalAmount.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                <td>R$ ${balance.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
            </tr>
        `;
        
        if (balance <= 0) break;
    }
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    if (totalMonths > 12) {
        html += `<p class="text-muted mt-3"><small>Exibindo apenas os primeiros ${monthsToShow} meses de ${totalMonths} totais.</small></p>`;
    }
    
    scheduleDiv.innerHTML = html;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('paymentPlanModal'));
    modal.show();
}
</script>
@endsection