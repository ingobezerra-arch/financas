@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-bullseye me-2"></i>Nova Meta Financeira</h4>
                        <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('goals.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome da Meta</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_amount" class="form-label">Valor da Meta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0.01" 
                                               class="form-control @error('target_amount') is-invalid @enderror" 
                                               id="target_amount" name="target_amount" value="{{ old('target_amount') }}" required>
                                        @error('target_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_amount" class="form-label">Valor Inicial</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('current_amount') is-invalid @enderror" 
                                               id="current_amount" name="current_amount" value="{{ old('current_amount', 0) }}">
                                        @error('current_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Valor que você já possui para esta meta</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_date" class="form-label">Data da Meta</label>
                                    <input type="date" class="form-control @error('target_date') is-invalid @enderror" 
                                           id="target_date" name="target_date" value="{{ old('target_date') }}" required>
                                    @error('target_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="monthly_contribution" class="form-label">Contribuição Mensal (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('monthly_contribution') is-invalid @enderror" 
                                       id="monthly_contribution" name="monthly_contribution" value="{{ old('monthly_contribution') }}">
                                @error('monthly_contribution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Valor que você planeja contribuir mensalmente</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Cor</label>
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color', '#007bff') }}">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Ícone</label>
                                    <select class="form-select @error('icon') is-invalid @enderror" id="icon" name="icon">
                                        <option value="fas fa-bullseye" {{ old('icon') === 'fas fa-bullseye' ? 'selected' : '' }}>🎯 Meta</option>
                                        <option value="fas fa-home" {{ old('icon') === 'fas fa-home' ? 'selected' : '' }}>🏠 Casa</option>
                                        <option value="fas fa-car" {{ old('icon') === 'fas fa-car' ? 'selected' : '' }}>🚗 Carro</option>
                                        <option value="fas fa-plane" {{ old('icon') === 'fas fa-plane' ? 'selected' : '' }}>✈️ Viagem</option>
                                        <option value="fas fa-graduation-cap" {{ old('icon') === 'fas fa-graduation-cap' ? 'selected' : '' }}>🎓 Educação</option>
                                        <option value="fas fa-ring" {{ old('icon') === 'fas fa-ring' ? 'selected' : '' }}>💍 Casamento</option>
                                        <option value="fas fa-baby" {{ old('icon') === 'fas fa-baby' ? 'selected' : '' }}>👶 Filho</option>
                                        <option value="fas fa-piggy-bank" {{ old('icon') === 'fas fa-piggy-bank' ? 'selected' : '' }}>🐷 Poupança</option>
                                        <option value="fas fa-chart-line" {{ old('icon') === 'fas fa-chart-line' ? 'selected' : '' }}>📈 Investimento</option>
                                        <option value="fas fa-umbrella" {{ old('icon') === 'fas fa-umbrella' ? 'selected' : '' }}>☂️ Emergência</option>
                                    </select>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Criar Meta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Calcular e mostrar previsão de conclusão
function calculateEndDate() {
    const targetAmount = parseFloat(document.getElementById('target_amount').value) || 0;
    const currentAmount = parseFloat(document.getElementById('current_amount').value) || 0;
    const monthlyContribution = parseFloat(document.getElementById('monthly_contribution').value) || 0;
    
    if (monthlyContribution > 0 && targetAmount > currentAmount) {
        const remainingAmount = targetAmount - currentAmount;
        const monthsNeeded = Math.ceil(remainingAmount / monthlyContribution);
        const endDate = new Date();
        endDate.setMonth(endDate.getMonth() + monthsNeeded);
        
        console.log(`Com R$ ${monthlyContribution}/mês, você alcançará a meta em ${monthsNeeded} meses (${endDate.toLocaleDateString()})`);
    }
}

document.getElementById('monthly_contribution').addEventListener('input', calculateEndDate);
document.getElementById('target_amount').addEventListener('input', calculateEndDate);
document.getElementById('current_amount').addEventListener('input', calculateEndDate);
</script>
@endpush