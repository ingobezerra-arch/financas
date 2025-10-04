@extends('layouts.app')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="{{ $goal->icon }} me-2" style="color: {{ $goal->color }};"></i>Editar Meta: {{ $goal->name }}</h4>
                        <div>
                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-1"></i>Ver
                            </a>
                            <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('goals.update', $goal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome da Meta</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $goal->name) }}" required>
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
                                               id="target_amount" name="target_amount" value="{{ old('target_amount', $goal->target_amount) }}" required>
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
                                      id="description" name="description" rows="3">{{ old('description', $goal->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_amount" class="form-label">Valor Atual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('current_amount') is-invalid @enderror" 
                                               id="current_amount" name="current_amount" value="{{ old('current_amount', $goal->current_amount) }}">
                                        @error('current_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_date" class="form-label">Data da Meta</label>
                                    <input type="date" class="form-control @error('target_date') is-invalid @enderror" 
                                           id="target_date" name="target_date" value="{{ old('target_date', $goal->target_date->format('Y-m-d')) }}" required>
                                    @error('target_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="monthly_contribution" class="form-label">Contribuição Mensal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('monthly_contribution') is-invalid @enderror" 
                                               id="monthly_contribution" name="monthly_contribution" value="{{ old('monthly_contribution', $goal->monthly_contribution) }}">
                                        @error('monthly_contribution')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Valor que você planeja contribuir mensalmente</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $goal->status) === 'active' ? 'selected' : '' }}>Ativa</option>
                                        <option value="completed" {{ old('status', $goal->status) === 'completed' ? 'selected' : '' }}>Concluída</option>
                                        <option value="cancelled" {{ old('status', $goal->status) === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Cor</label>
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color', $goal->color) }}">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Ícone</label>
                                    <select class="form-select @error('icon') is-invalid @enderror" id="icon" name="icon">
                                        <option value="fas fa-bullseye" {{ old('icon', $goal->icon) === 'fas fa-bullseye' ? 'selected' : '' }}>🎯 Meta</option>
                                        <option value="fas fa-home" {{ old('icon', $goal->icon) === 'fas fa-home' ? 'selected' : '' }}>🏠 Casa</option>
                                        <option value="fas fa-car" {{ old('icon', $goal->icon) === 'fas fa-car' ? 'selected' : '' }}>🚗 Carro</option>
                                        <option value="fas fa-plane" {{ old('icon', $goal->icon) === 'fas fa-plane' ? 'selected' : '' }}>✈️ Viagem</option>
                                        <option value="fas fa-graduation-cap" {{ old('icon', $goal->icon) === 'fas fa-graduation-cap' ? 'selected' : '' }}>🎓 Educação</option>
                                        <option value="fas fa-ring" {{ old('icon', $goal->icon) === 'fas fa-ring' ? 'selected' : '' }}>💍 Casamento</option>
                                        <option value="fas fa-baby" {{ old('icon', $goal->icon) === 'fas fa-baby' ? 'selected' : '' }}>👶 Filho</option>
                                        <option value="fas fa-piggy-bank" {{ old('icon', $goal->icon) === 'fas fa-piggy-bank' ? 'selected' : '' }}>🐷 Poupança</option>
                                        <option value="fas fa-chart-line" {{ old('icon', $goal->icon) === 'fas fa-chart-line' ? 'selected' : '' }}>📈 Investimento</option>
                                        <option value="fas fa-umbrella" {{ old('icon', $goal->icon) === 'fas fa-umbrella' ? 'selected' : '' }}>☂️ Emergência</option>
                                    </select>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Progresso Atual -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Progresso Atual</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>R$ {{ number_format($goal->current_amount, 2, ',', '.') }} de R$ {{ number_format($goal->target_amount, 2, ',', '.') }}</span>
                                    <span>{{ number_format(($goal->current_amount / $goal->target_amount) * 100, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ min(($goal->current_amount / $goal->target_amount) * 100, 100) }}%; background-color: {{ $goal->color }};"
                                         role="progressbar"></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Salvar Alterações
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
// Atualizar progresso ao alterar valores
function updateProgress() {
    const currentAmount = parseFloat(document.getElementById('current_amount').value) || 0;
    const targetAmount = parseFloat(document.getElementById('target_amount').value) || 1;
    const percentage = Math.min((currentAmount / targetAmount) * 100, 100);
    
    // Atualizar barra de progresso (se existir na página)
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = percentage + '%';
    }
}

document.getElementById('current_amount').addEventListener('input', updateProgress);
document.getElementById('target_amount').addEventListener('input', updateProgress);
</script>
@endpush