@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-plus"></i> Nova Dívida
                </h1>
                <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informações da Dívida</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('debts.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Nome da Dívida <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Ex: Cartão Visa, Financiamento Carro..." required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="debt_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select @error('debt_type') is-invalid @enderror" id="debt_type" name="debt_type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="credit_card" {{ old('debt_type') == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                    <option value="loan" {{ old('debt_type') == 'loan' ? 'selected' : '' }}>Empréstimo</option>
                                    <option value="financing" {{ old('debt_type') == 'financing' ? 'selected' : '' }}>Financiamento</option>
                                    <option value="invoice" {{ old('debt_type') == 'invoice' ? 'selected' : '' }}>Fatura</option>
                                    <option value="other" {{ old('debt_type') == 'other' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('debt_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="creditor" class="form-label">Credor/Instituição</label>
                                <input type="text" class="form-control @error('creditor') is-invalid @enderror" 
                                       id="creditor" name="creditor" value="{{ old('creditor') }}" 
                                       placeholder="Ex: Banco do Brasil, Loja XYZ...">
                                @error('creditor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contract_date" class="form-label">Data do Contrato</label>
                                <input type="date" class="form-control @error('contract_date') is-invalid @enderror" 
                                       id="contract_date" name="contract_date" value="{{ old('contract_date') }}">
                                @error('contract_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Detalhes adicionais sobre a dívida...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3">Valores Financeiros</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="original_amount" class="form-label">Valor Original <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('original_amount') is-invalid @enderror" 
                                           id="original_amount" name="original_amount" value="{{ old('original_amount') }}" 
                                           step="0.01" placeholder="0,00" required>
                                </div>
                                @error('original_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="current_balance" class="form-label">Saldo Atual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('current_balance') is-invalid @enderror" 
                                           id="current_balance" name="current_balance" value="{{ old('current_balance') }}" 
                                           step="0.01" placeholder="0,00" required>
                                </div>
                                @error('current_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="interest_rate" class="form-label">Taxa de Juros (% ao mês) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" 
                                           id="interest_rate" name="interest_rate" value="{{ old('interest_rate') }}" 
                                           step="0.01" placeholder="0,00" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('interest_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Taxa mensal de juros aplicada</small>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_payment" class="form-label">Pagamento Mínimo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('minimum_payment') is-invalid @enderror" 
                                           id="minimum_payment" name="minimum_payment" value="{{ old('minimum_payment') }}" 
                                           step="0.01" placeholder="0,00" required>
                                </div>
                                @error('minimum_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Valor mínimo mensal</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="due_date" class="form-label">Data de Vencimento</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="installments_total" class="form-label">Total de Parcelas</label>
                                <input type="number" class="form-control @error('installments_total') is-invalid @enderror" 
                                       id="installments_total" name="installments_total" value="{{ old('installments_total') }}" 
                                       min="1" placeholder="Ex: 24">
                                @error('installments_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Se aplicável</small>
                            </div>
                            <div class="col-md-4">
                                <label for="installments_paid" class="form-label">Parcelas Pagas</label>
                                <input type="number" class="form-control @error('installments_paid') is-invalid @enderror" 
                                       id="installments_paid" name="installments_paid" value="{{ old('installments_paid', 0) }}" 
                                       min="0" placeholder="Ex: 8">
                                @error('installments_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Dívida
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card com Dicas -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Dicas Importantes</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Taxa de Juros:</strong> Informe a taxa mensal. Se só souber a anual, divida por 12.</li>
                        <li><strong>Saldo Atual:</strong> Pode ser menor que o valor original se já fez pagamentos.</li>
                        <li><strong>Pagamento Mínimo:</strong> Valor mínimo exigido mensalmente pelo credor.</li>
                        <li><strong>Tipo de Dívida:</strong> Ajuda a categorizar e analisar seus gastos.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill current balance with original amount if not filled
document.getElementById('original_amount').addEventListener('blur', function() {
    const currentBalance = document.getElementById('current_balance');
    if (!currentBalance.value && this.value) {
        currentBalance.value = this.value;
    }
});

// Validate that current balance is not greater than original amount
document.getElementById('current_balance').addEventListener('blur', function() {
    const originalAmount = parseFloat(document.getElementById('original_amount').value) || 0;
    const currentBalance = parseFloat(this.value) || 0;
    
    if (currentBalance > originalAmount && originalAmount > 0) {
        alert('O saldo atual não pode ser maior que o valor original da dívida.');
        this.focus();
    }
});

// Update installments paid limit when total changes
document.getElementById('installments_total').addEventListener('input', function() {
    const installmentsPaid = document.getElementById('installments_paid');
    if (this.value) {
        installmentsPaid.max = this.value;
        if (parseInt(installmentsPaid.value) > parseInt(this.value)) {
            installmentsPaid.value = this.value;
        }
    }
});
</script>
@endsection