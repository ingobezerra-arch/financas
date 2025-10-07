@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-edit"></i> Editar Dívida
                </h1>
                <div>
                    <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> Visualizar
                    </a>
                    <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações da Dívida: {{ $debt->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('debts.update', $debt) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Nome da Dívida <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $debt->name) }}" 
                                       placeholder="Ex: Cartão Visa, Financiamento Carro..." required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="debt_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select @error('debt_type') is-invalid @enderror" id="debt_type" name="debt_type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="credit_card" {{ old('debt_type', $debt->debt_type) == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                    <option value="loan" {{ old('debt_type', $debt->debt_type) == 'loan' ? 'selected' : '' }}>Empréstimo</option>
                                    <option value="financing" {{ old('debt_type', $debt->debt_type) == 'financing' ? 'selected' : '' }}>Financiamento</option>
                                    <option value="invoice" {{ old('debt_type', $debt->debt_type) == 'invoice' ? 'selected' : '' }}>Fatura</option>
                                    <option value="other" {{ old('debt_type', $debt->debt_type) == 'other' ? 'selected' : '' }}>Outro</option>
                                </select>
                                @error('debt_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $debt->status) == 'active' ? 'selected' : '' }}>Ativa</option>
                                    <option value="paid" {{ old('status', $debt->status) == 'paid' ? 'selected' : '' }}>Paga</option>
                                    <option value="overdue" {{ old('status', $debt->status) == 'overdue' ? 'selected' : '' }}>Em Atraso</option>
                                    <option value="negotiated" {{ old('status', $debt->status) == 'negotiated' ? 'selected' : '' }}>Negociada</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="creditor" class="form-label">Credor/Instituição</label>
                                <input type="text" class="form-control @error('creditor') is-invalid @enderror" 
                                       id="creditor" name="creditor" value="{{ old('creditor', $debt->creditor) }}" 
                                       placeholder="Ex: Banco do Brasil, Loja XYZ...">
                                @error('creditor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="contract_date" class="form-label">Data do Contrato</label>
                                <input type="date" class="form-control @error('contract_date') is-invalid @enderror" 
                                       id="contract_date" name="contract_date" value="{{ old('contract_date', $debt->contract_date?->format('Y-m-d')) }}">
                                @error('contract_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Detalhes adicionais sobre a dívida...">{{ old('description', $debt->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3">Valores Financeiros</h6>

                        <!-- Informação do Valor Original (somente leitura) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Valor Original</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control" 
                                           value="{{ number_format($debt->original_amount, 2, ',', '.') }}" 
                                           readonly disabled>
                                </div>
                                <small class="text-muted">Valor original não pode ser alterado</small>
                            </div>
                            <div class="col-md-6">
                                <label for="current_balance" class="form-label">Saldo Atual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('current_balance') is-invalid @enderror" 
                                           id="current_balance" name="current_balance" value="{{ old('current_balance', $debt->current_balance) }}" 
                                           step="0.01" placeholder="0,00" required min="0">
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
                                           id="interest_rate" name="interest_rate" value="{{ old('interest_rate', $debt->interest_rate) }}" 
                                           step="0.01" placeholder="0,00" required min="0" max="100">
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
                                           id="minimum_payment" name="minimum_payment" value="{{ old('minimum_payment', $debt->minimum_payment) }}" 
                                           step="0.01" placeholder="0,00" required min="0.01">
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
                                       id="due_date" name="due_date" value="{{ old('due_date', $debt->due_date?->format('Y-m-d')) }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="installments_total" class="form-label">Total de Parcelas</label>
                                <input type="number" class="form-control @error('installments_total') is-invalid @enderror" 
                                       id="installments_total" name="installments_total" value="{{ old('installments_total', $debt->installments_total) }}" 
                                       min="1" placeholder="Ex: 24">
                                @error('installments_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Se aplicável</small>
                            </div>
                            <div class="col-md-4">
                                <label for="installments_paid" class="form-label">Parcelas Pagas</label>
                                <input type="number" class="form-control @error('installments_paid') is-invalid @enderror" 
                                       id="installments_paid" name="installments_paid" value="{{ old('installments_paid', $debt->installments_paid ?? 0) }}" 
                                       min="0" placeholder="Ex: 8">
                                @error('installments_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Atualizar Dívida
                                </button>
                            </div>
                            
                            <!-- Botão de exclusão -->
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Excluir Dívida
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card com Informações Adicionais -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i> Resumo da Dívida</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Valor Original</h6>
                                <h5 class="text-primary">R$ {{ number_format($debt->original_amount, 2, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Saldo Atual</h6>
                                <h5 class="text-danger">R$ {{ number_format($debt->current_balance, 2, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Taxa de Juros</h6>
                                <h5 class="text-warning">{{ number_format($debt->interest_rate, 2, ',', '.') }}%</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Pagamento Mínimo</h6>
                                <h5 class="text-info">R$ {{ number_format($debt->minimum_payment, 2, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                    
                    @if($debt->installments_total)
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Progresso das Parcelas:</small>
                            <div class="progress mt-1">
                                @php
                                    $progress = ($debt->installments_paid / $debt->installments_total) * 100;
                                @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%" 
                                     aria-valuenow="{{ $debt->installments_paid }}" 
                                     aria-valuemin="0" aria-valuemax="{{ $debt->installments_total }}">
                                    {{ $debt->installments_paid }}/{{ $debt->installments_total }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Parcelas Restantes:</small>
                            <h6 class="mt-1">{{ $debt->installments_total - $debt->installments_paid }} parcelas</h6>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card com Alertas -->
            <div class="card mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Atenção</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Valor Original:</strong> Não pode ser alterado para manter o histórico da dívida.</li>
                        <li><strong>Saldo Atual:</strong> Atualize conforme os pagamentos realizados.</li>
                        <li><strong>Status:</strong> "Paga" remove a dívida dos cálculos ativos.</li>
                        <li><strong>Exclusão:</strong> Remove permanentemente todos os dados da dívida.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Atenção!</strong> Esta ação não pode ser desfeita.</p>
                <p>Você está prestes a excluir a dívida:</p>
                <div class="alert alert-warning">
                    <strong>{{ $debt->name }}</strong><br>
                    <small>Saldo atual: R$ {{ number_format($debt->current_balance, 2, ',', '.') }}</small>
                </div>
                <p>Todos os dados relacionados a esta dívida serão perdidos permanentemente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('debts.destroy', $debt) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Excluir Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validate that current balance is not negative
document.getElementById('current_balance').addEventListener('blur', function() {
    const currentBalance = parseFloat(this.value) || 0;
    
    if (currentBalance < 0) {
        alert('O saldo atual não pode ser negativo.');
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

// Auto-update status when current balance is zero
document.getElementById('current_balance').addEventListener('input', function() {
    const statusSelect = document.getElementById('status');
    const currentBalance = parseFloat(this.value) || 0;
    
    if (currentBalance === 0 && statusSelect.value === 'active') {
        if (confirm('O saldo está zerado. Deseja marcar esta dívida como "Paga"?')) {
            statusSelect.value = 'paid';
        }
    }
});

// Show warning when changing to paid status
document.getElementById('status').addEventListener('change', function() {
    const currentBalance = parseFloat(document.getElementById('current_balance').value) || 0;
    
    if (this.value === 'paid' && currentBalance > 0) {
        if (confirm('Você está marcando a dívida como "Paga" mas ainda há saldo devedor. Deseja zerar o saldo atual?')) {
            document.getElementById('current_balance').value = '0.00';
        }
    }
});
</script>
@endsection