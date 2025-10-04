@extends('layouts.app')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-redo-alt me-2"></i>Editar Transação Recorrente</h4>
                        <div>
                            <a href="{{ route('recurring-transactions.show', $recurringTransaction) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-1"></i>Ver
                            </a>
                            <a href="{{ route('recurring-transactions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('recurring-transactions.update', $recurringTransaction) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipo</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="income" {{ old('type', $recurringTransaction->type) === 'income' ? 'selected' : '' }}>Receita</option>
                                        <option value="expense" {{ old('type', $recurringTransaction->type) === 'expense' ? 'selected' : '' }}>Despesa</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Valor</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0.01" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" value="{{ old('amount', $recurringTransaction->amount) }}" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_id" class="form-label">Conta</label>
                                    <select class="form-select @error('account_id') is-invalid @enderror" 
                                            id="account_id" name="account_id" required>
                                        <option value="">Selecione uma conta</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id', $recurringTransaction->account_id) == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }} ({{ $account->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoria</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Selecione uma categoria</option>
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                   id="description" name="description" value="{{ old('description', $recurringTransaction->description) }}" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $recurringTransaction->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Configurações de Recorrência -->
                        <div class="card bg-light mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-sync-alt me-2"></i>Configurações de Recorrência</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="frequency" class="form-label">Frequência</label>
                                            <select class="form-select @error('frequency') is-invalid @enderror" 
                                                    id="frequency" name="frequency" required>
                                                <option value="">Selecione</option>
                                                <option value="daily" {{ old('frequency', $recurringTransaction->frequency) === 'daily' ? 'selected' : '' }}>Diário</option>
                                                <option value="weekly" {{ old('frequency', $recurringTransaction->frequency) === 'weekly' ? 'selected' : '' }}>Semanal</option>
                                                <option value="monthly" {{ old('frequency', $recurringTransaction->frequency) === 'monthly' ? 'selected' : '' }}>Mensal</option>
                                                <option value="yearly" {{ old('frequency', $recurringTransaction->frequency) === 'yearly' ? 'selected' : '' }}>Anual</option>
                                            </select>
                                            @error('frequency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="interval" class="form-label">Intervalo</label>
                                            <input type="number" min="1" max="12" 
                                                   class="form-control @error('interval') is-invalid @enderror" 
                                                   id="interval" name="interval" value="{{ old('interval', $recurringTransaction->interval) }}" required>
                                            @error('interval')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">A cada quantos períodos repetir</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Data de Início</label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" value="{{ old('start_date', $recurringTransaction->start_date->format('Y-m-d')) }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">Data de Fim (Opcional)</label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" name="end_date" value="{{ old('end_date', $recurringTransaction->end_date?->format('Y-m-d')) }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Deixe vazio para não ter fim</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="occurrences" class="form-label">Número de Ocorrências (Opcional)</label>
                                            <input type="number" min="1" 
                                                   class="form-control @error('occurrences') is-invalid @enderror" 
                                                   id="occurrences" name="occurrences" value="{{ old('occurrences', $recurringTransaction->occurrences) }}">
                                            @error('occurrences')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Quantas vezes repetir</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                           id="tags" name="tags" value="{{ old('tags', is_array($recurringTransaction->tags) ? implode(', ', $recurringTransaction->tags) : '') }}" 
                                           placeholder="Digite as tags separadas por vírgula">
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Ex: casa, aluguel, financiamento</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                               {{ old('is_active', $recurringTransaction->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Transação Ativa
                                        </label>
                                    </div>
                                    <div class="form-text">Desmarque para pausar a execução automática</div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Atual -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Status Atual</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Execuções:</strong></p>
                                        <span class="h6">{{ $recurringTransaction->occurrences_count }}
                                            @if($recurringTransaction->occurrences)
                                                / {{ $recurringTransaction->occurrences }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Próxima:</strong></p>
                                        <span class="h6">{{ $recurringTransaction->next_due_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Total Movimentado:</strong></p>
                                        <span class="h6 {{ $recurringTransaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            R$ {{ number_format($recurringTransaction->amount * $recurringTransaction->occurrences_count, 2, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Status:</strong></p>
                                        <span class="badge {{ $recurringTransaction->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $recurringTransaction->is_active ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('recurring-transactions.show', $recurringTransaction) }}" class="btn btn-outline-secondary me-md-2">
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
// Categorias organizadas por tipo
const categoriesByType = {
    income: @json($categories->where('type', 'income')->values()),
    expense: @json($categories->where('type', 'expense')->values())
};

// Atualizar categorias quando o tipo mudar
function updateCategories() {
    const type = document.getElementById('type').value;
    const categorySelect = document.getElementById('category_id');
    const currentCategoryId = '{{ old("category_id", $recurringTransaction->category_id) }}';
    
    // Limpar categorias
    categorySelect.innerHTML = '<option value="">Selecione uma categoria</option>';
    
    if (type && categoriesByType[type]) {
        categoriesByType[type].forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            if (currentCategoryId == category.id) {
                option.selected = true;
            }
            categorySelect.appendChild(option);
        });
    }
}

document.getElementById('type').addEventListener('change', updateCategories);

// Trigger inicial
updateCategories();

// Validações de data
document.getElementById('end_date').addEventListener('change', function() {
    const startDate = document.getElementById('start_date').value;
    const endDate = this.value;
    
    if (startDate && endDate && endDate <= startDate) {
        alert('A data de fim deve ser posterior à data de início.');
        this.value = '';
    }
});

// Desabilitar ocorrências quando data de fim estiver preenchida
document.getElementById('end_date').addEventListener('input', function() {
    const occurrences = document.getElementById('occurrences');
    if (this.value) {
        occurrences.disabled = true;
        if (!occurrences.dataset.originalValue) {
            occurrences.dataset.originalValue = occurrences.value;
        }
        occurrences.value = '';
    } else {
        occurrences.disabled = false;
        if (occurrences.dataset.originalValue) {
            occurrences.value = occurrences.dataset.originalValue;
        }
    }
});

document.getElementById('occurrences').addEventListener('input', function() {
    const endDate = document.getElementById('end_date');
    if (this.value) {
        endDate.disabled = true;
        if (!endDate.dataset.originalValue) {
            endDate.dataset.originalValue = endDate.value;
        }
        endDate.value = '';
    } else {
        endDate.disabled = false;
        if (endDate.dataset.originalValue) {
            endDate.value = endDate.dataset.originalValue;
        }
    }
});

// Verificar estado inicial
if (document.getElementById('end_date').value) {
    document.getElementById('occurrences').disabled = true;
}
if (document.getElementById('occurrences').value) {
    document.getElementById('end_date').disabled = true;
}
</script>
@endpush