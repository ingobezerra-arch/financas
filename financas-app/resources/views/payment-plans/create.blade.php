@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-magic"></i> Criar Plano de Pagamento
                </h1>
                <a href="{{ route('payment-plans.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            @if($debts->isEmpty())
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Nenhuma dívida ativa encontrada!</strong>
                    Você precisa ter pelo menos uma dívida ativa para criar um plano de pagamento.
                    <a href="{{ route('debts.create') }}" class="btn btn-primary btn-sm ms-2">
                        <i class="fas fa-plus"></i> Cadastrar Dívida
                    </a>
                </div>
            @else
                <!-- Comparação de Estratégias -->
                @if($comparison)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-balance-scale mr-2"></i> Comparação de Estratégias</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($comparison as $strategy => $data)
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            @if($strategy == 'snowball')
                                                <i class="fas fa-snowflake text-info"></i> Bola de Neve
                                            @else
                                                <i class="fas fa-mountain text-danger"></i> Avalanche
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            {{ $strategy == 'snowball' ? 'Menor saldo primeiro' : 'Maior juros primeiro' }}
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="text-primary h5">{{ $data['months_to_payoff'] }}</div>
                                                <small class="text-muted">Meses</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-danger h5">R$ {{ number_format($data['total_interest'], 2, ',', '.') }}</div>
                                                <small class="text-muted">Total Juros</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <small class="text-muted">Ordem de pagamento:</small>
                                        <ul class="list-unstyled small mt-2">
                                            @foreach($data['debt_order'] as $debtId => $debtName)
                                                <li>{{ $loop->iteration }}. {{ $debtName }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        @php
                            $snowball = $comparison['snowball'];
                            $avalanche = $comparison['avalanche'];
                            $betterStrategy = $avalanche['total_interest'] < $snowball['total_interest'] ? 'avalanche' : 'snowball';
                            $savings = abs($avalanche['total_interest'] - $snowball['total_interest']);
                            $monthsSavings = abs($avalanche['months_to_payoff'] - $snowball['months_to_payoff']);
                        @endphp
                        
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Recomendação:</strong> 
                            A estratégia <strong>{{ $betterStrategy == 'avalanche' ? 'Avalanche' : 'Bola de Neve' }}</strong> 
                            pode economizar <strong>R$ {{ number_format($savings, 2, ',', '.') }}</strong> 
                            @if($monthsSavings > 0)
                                e <strong>{{ $monthsSavings }} meses</strong>
                            @endif
                            em comparação com a outra estratégia.
                        </div>
                    </div>
                </div>
                @endif

                <!-- Formulário de Criação -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Configuração do Plano</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('payment-plans.store') }}" method="POST" id="planForm">
                            @csrf
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="strategy" class="form-label">Estratégia de Pagamento <span class="text-danger">*</span></label>
                                    <select class="form-select @error('strategy') is-invalid @enderror" id="strategy" name="strategy" required onchange="updateStrategyInfo()">
                                        <option value="">Selecione uma estratégia</option>
                                        <option value="snowball" {{ old('strategy') == 'snowball' ? 'selected' : '' }}>
                                            🏔️ Bola de Neve - Menor saldo primeiro
                                        </option>
                                        <option value="avalanche" {{ old('strategy') == 'avalanche' ? 'selected' : '' }}>
                                            ⛰️ Avalanche - Maior juros primeiro
                                        </option>
                                        <option value="custom" {{ old('strategy') == 'custom' ? 'selected' : '' }}>
                                            ⚙️ Personalizada - Ordem manual
                                        </option>
                                    </select>
                                    @error('strategy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <div id="strategyInfo" class="mt-2">
                                        <!-- Info dinâmica da estratégia -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="monthly_budget" class="form-label">Orçamento Mensal <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control @error('monthly_budget') is-invalid @enderror" 
                                                       id="monthly_budget" name="monthly_budget" value="{{ old('monthly_budget') }}" 
                                                       step="0.01" placeholder="0,00" required onchange="validateBudget()">
                                            </div>
                                            @error('monthly_budget')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Valor total disponível por mês</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="extra_payment" class="form-label">Valor Extra</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control @error('extra_payment') is-invalid @enderror" 
                                                       id="extra_payment" name="extra_payment" value="{{ old('extra_payment', 0) }}" 
                                                       step="0.01" placeholder="0,00">
                                            </div>
                                            @error('extra_payment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Valor adicional para acelerar</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-primary mb-3">Selecionar Dívidas</h6>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">
                                                        <input type="checkbox" id="selectAll" onchange="toggleAllDebts()">
                                                    </th>
                                                    <th>Dívida</th>
                                                    <th>Saldo</th>
                                                    <th>Taxa Juros</th>
                                                    <th>Pag. Mínimo</th>
                                                    <th id="customOrderHeader" style="display: none;">Ordem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($debts as $debt)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="debt-checkbox" name="debt_ids[]" 
                                                               value="{{ $debt->id }}" onchange="updateTotals()"
                                                               {{ in_array($debt->id, old('debt_ids', [])) ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $debt->name }}</strong>
                                                        @if($debt->creditor)
                                                            <br><small class="text-muted">{{ $debt->creditor }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="text-danger">R$ {{ number_format($debt->current_balance, 2, ',', '.') }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $debt->interest_rate > 5 ? 'danger' : ($debt->interest_rate > 2 ? 'warning' : 'success') }}">
                                                            {{ number_format($debt->interest_rate, 2) }}%
                                                        </span>
                                                    </td>
                                                    <td>R$ {{ number_format($debt->minimum_payment, 2, ',', '.') }}</td>
                                                    <td class="custom-order" style="display: none;">
                                                        <input type="number" class="form-control form-control-sm" 
                                                               name="custom_priorities[]" value="{{ $loop->iteration }}"
                                                               min="1" max="{{ $debts->count() }}" style="width: 60px;">
                                                        <input type="hidden" name="custom_debt_ids[]" value="{{ $debt->id }}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Resumo</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <small class="text-muted">Dívidas Selecionadas:</small>
                                                <div class="fw-bold" id="selectedCount">0</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Valor Total:</small>
                                                <div class="fw-bold text-danger" id="totalAmount">R$ 0,00</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Pagamentos Mínimos:</small>
                                                <div class="fw-bold" id="totalMinimum">R$ 0,00</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">Orçamento Necessário:</small>
                                                <div class="fw-bold" id="budgetNeeded">R$ 0,00</div>
                                            </div>
                                            <div id="budgetAlert" class="alert alert-warning mt-2" style="display: none;">
                                                <small>Orçamento insuficiente para pagamentos mínimos!</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('payment-plans.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="createPlanBtn" disabled>
                                    <i class="fas fa-magic"></i> Criar Plano de Pagamento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card com Explicações -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i> Como Funcionam as Estratégias</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-info">🏔️ Bola de Neve</h6>
                                <p class="small">Foca na dívida com menor saldo primeiro. Proporciona vitórias rápidas e motivação psicológica para continuar.</p>
                                <strong class="text-success">Vantagem:</strong> <span class="small">Motivação e momentum</span><br>
                                <strong class="text-warning">Desvantagem:</strong> <span class="small">Pode pagar mais juros</span>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-danger">⛰️ Avalanche</h6>
                                <p class="small">Foca na dívida com maior taxa de juros primeiro. Matematicamente a mais eficiente em economia de dinheiro.</p>
                                <strong class="text-success">Vantagem:</strong> <span class="small">Menor custo total</span><br>
                                <strong class="text-warning">Desvantagem:</strong> <span class="small">Pode ser menos motivante</span>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-primary">⚙️ Personalizada</h6>
                                <p class="small">Você define a ordem de prioridade das dívidas conforme sua preferência pessoal.</p>
                                <strong class="text-success">Vantagem:</strong> <span class="small">Controle total</span><br>
                                <strong class="text-warning">Desvantagem:</strong> <span class="small">Requer conhecimento</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
const debts = @json($debts);

function updateStrategyInfo() {
    const strategy = document.getElementById('strategy').value;
    const infoDiv = document.getElementById('strategyInfo');
    const customOrderHeader = document.getElementById('customOrderHeader');
    const customOrderCells = document.querySelectorAll('.custom-order');
    
    let info = '';
    
    switch(strategy) {
        case 'snowball':
            info = '<div class="alert alert-info alert-sm"><small><i class="fas fa-snowflake"></i> <strong>Bola de Neve:</strong> As dívidas serão ordenadas automaticamente do menor para o maior saldo.</small></div>';
            customOrderHeader.style.display = 'none';
            customOrderCells.forEach(cell => cell.style.display = 'none');
            break;
        case 'avalanche':
            info = '<div class="alert alert-danger alert-sm"><small><i class="fas fa-mountain"></i> <strong>Avalanche:</strong> As dívidas serão ordenadas automaticamente da maior para a menor taxa de juros.</small></div>';
            customOrderHeader.style.display = 'none';
            customOrderCells.forEach(cell => cell.style.display = 'none');
            break;
        case 'custom':
            info = '<div class="alert alert-primary alert-sm"><small><i class="fas fa-cog"></i> <strong>Personalizada:</strong> Defina a ordem de pagamento manualmente na coluna "Ordem".</small></div>';
            customOrderHeader.style.display = 'table-cell';
            customOrderCells.forEach(cell => cell.style.display = 'table-cell');
            break;
        default:
            customOrderHeader.style.display = 'none';
            customOrderCells.forEach(cell => cell.style.display = 'none');
    }
    
    infoDiv.innerHTML = info;
}

function toggleAllDebts() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.debt-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateTotals();
}

function updateTotals() {
    const checkboxes = document.querySelectorAll('.debt-checkbox:checked');
    const selectedCount = checkboxes.length;
    
    let totalAmount = 0;
    let totalMinimum = 0;
    
    checkboxes.forEach(checkbox => {
        const debtId = parseInt(checkbox.value);
        const debt = debts.find(d => d.id === debtId);
        if (debt) {
            totalAmount += parseFloat(debt.current_balance);
            totalMinimum += parseFloat(debt.minimum_payment);
        }
    });
    
    document.getElementById('selectedCount').textContent = selectedCount;
    document.getElementById('totalAmount').textContent = 'R$ ' + totalAmount.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    document.getElementById('totalMinimum').textContent = 'R$ ' + totalMinimum.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    document.getElementById('budgetNeeded').textContent = 'R$ ' + totalMinimum.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
    validateBudget();
    
    // Enable/disable create button
    const createBtn = document.getElementById('createPlanBtn');
    const strategy = document.getElementById('strategy').value;
    const budget = parseFloat(document.getElementById('monthly_budget').value) || 0;
    
    createBtn.disabled = !(selectedCount > 0 && strategy && budget >= totalMinimum);
}

function validateBudget() {
    const budget = parseFloat(document.getElementById('monthly_budget').value) || 0;
    const totalMinimum = parseFloat(document.getElementById('budgetNeeded').textContent.replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
    const budgetAlert = document.getElementById('budgetAlert');
    
    if (budget > 0 && totalMinimum > 0 && budget < totalMinimum) {
        budgetAlert.style.display = 'block';
    } else {
        budgetAlert.style.display = 'none';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
    updateStrategyInfo();
});
</script>
@endsection