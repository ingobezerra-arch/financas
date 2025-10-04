@extends('layouts.app')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Nova Transação') }}</h4>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('transactions.store') }}">
                        @csrf

                        <div class="row">
                            <!-- Tipo de Transação -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('Tipo de Transação') }} <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="type" id="type_income" value="income" {{ old('type') == 'income' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-success" for="type_income">
                                        <i class="fas fa-arrow-up"></i> Receita
                                    </label>

                                    <input type="radio" class="btn-check" name="type" id="type_expense" value="expense" {{ old('type') == 'expense' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-danger" for="type_expense">
                                        <i class="fas fa-arrow-down"></i> Despesa
                                    </label>
                                </div>
                                @error('type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Valor -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">{{ __('Valor') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input id="amount" type="number" step="0.01" min="0.01" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               name="amount" value="{{ old('amount') }}" required autofocus>
                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Data -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="transaction_date" class="form-label">{{ __('Data') }} <span class="text-danger">*</span></label>
                                    <input id="transaction_date" type="date" 
                                           class="form-control @error('transaction_date') is-invalid @enderror" 
                                           name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                    @error('transaction_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Conta -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="account_id" class="form-label">{{ __('Conta') }} <span class="text-danger">*</span></label>
                                    <select id="account_id" class="form-select @error('account_id') is-invalid @enderror" name="account_id" required>
                                        <option value="">Selecione uma conta</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}
                                                    data-color="{{ $account->color }}" data-icon="{{ $account->icon }}">
                                                {{ $account->name }} ({{ $account->formatted_balance }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Descrição -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('Descrição') }} <span class="text-danger">*</span></label>
                                    <input id="description" type="text" 
                                           class="form-control @error('description') is-invalid @enderror" 
                                           name="description" value="{{ old('description') }}" required
                                           placeholder="Ex: Compra no supermercado, Salário mensal">
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Categoria -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">{{ __('Categoria') }} <span class="text-danger">*</span></label>
                                    <select id="category_id" class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                        <option value="">Selecione uma categoria</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}
                                                    data-type="{{ $category->type }}" data-color="{{ $category->color }}" data-icon="{{ $category->icon }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Observações -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ __('Observações') }}</label>
                                    <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" rows="3" 
                                              placeholder="Observações adicionais sobre a transação">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tags_input" class="form-label">{{ __('Tags') }}</label>
                                    <input id="tags_input" type="text" class="form-control" 
                                           placeholder="Ex: mercado, combustível">
                                    <small class="form-text text-muted">Pressione Enter para adicionar tags</small>
                                    <input type="hidden" name="tags" id="tags_hidden" value="{{ json_encode(old('tags', [])) }}">
                                    <div id="tags_container" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- URL do Comprovante -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="receipt_url" class="form-label">{{ __('URL do Comprovante') }}</label>
                                    <input id="receipt_url" type="url" 
                                           class="form-control @error('receipt_url') is-invalid @enderror" 
                                           name="receipt_url" value="{{ old('receipt_url') }}"
                                           placeholder="https://exemplo.com/comprovante.jpg">
                                    @error('receipt_url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Link para foto ou arquivo do comprovante</small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview da Transação -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('Preview da Transação') }}</label>
                            <div class="card border-light bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div id="preview-icon" class="transaction-icon bg-secondary">
                                                    <i class="fas fa-exchange-alt text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0" id="preview-description">Descrição da transação</h6>
                                                <small class="text-muted">
                                                    <span id="preview-account">Conta</span> • 
                                                    <span id="preview-category">Categoria</span> • 
                                                    <span id="preview-date">{{ date('d/m/Y') }}</span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-0" id="preview-amount">R$ 0,00</h5>
                                            <small class="text-muted" id="preview-type">Tipo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Transação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.transaction-icon {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.tag-item {
    display: inline-block;
    background: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: 15px;
    padding: 2px 8px;
    margin: 2px;
    font-size: 0.875rem;
}
.tag-remove {
    cursor: pointer;
    margin-left: 5px;
    color: #dc3545;
}
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let tags = JSON.parse(document.getElementById('tags_hidden').value || '[]');
    
    // Elementos do preview
    const previewIcon = document.getElementById('preview-icon');
    const previewDescription = document.getElementById('preview-description');
    const previewAccount = document.getElementById('preview-account');
    const previewCategory = document.getElementById('preview-category');
    const previewDate = document.getElementById('preview-date');
    const previewAmount = document.getElementById('preview-amount');
    const previewType = document.getElementById('preview-type');
    
    // Filtrar categorias por tipo
    function filterCategories() {
        const type = document.querySelector('input[name="type"]:checked')?.value;
        const categorySelect = document.getElementById('category_id');
        const options = categorySelect.querySelectorAll('option[data-type]');
        
        options.forEach(option => {
            if (option.dataset.type === type) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Reset selection if current doesn't match type
        if (categorySelect.value) {
            const selectedOption = categorySelect.querySelector(`option[value="${categorySelect.value}"]`);
            if (selectedOption && selectedOption.dataset.type !== type) {
                categorySelect.value = '';
            }
        }
        
        updatePreview();
    }
    
    // Atualizar preview
    function updatePreview() {
        const type = document.querySelector('input[name="type"]:checked')?.value;
        const amount = document.getElementById('amount').value;
        const description = document.getElementById('description').value;
        const accountSelect = document.getElementById('account_id');
        const categorySelect = document.getElementById('category_id');
        const date = document.getElementById('transaction_date').value;
        
        // Atualizar descrição
        previewDescription.textContent = description || 'Descrição da transação';
        
        // Atualizar conta
        if (accountSelect.value) {
            const selectedAccount = accountSelect.options[accountSelect.selectedIndex];
            previewAccount.textContent = selectedAccount.text.split(' (')[0];
        } else {
            previewAccount.textContent = 'Conta';
        }
        
        // Atualizar categoria
        if (categorySelect.value) {
            previewCategory.textContent = categorySelect.options[categorySelect.selectedIndex].text;
        } else {
            previewCategory.textContent = 'Categoria';
        }
        
        // Atualizar data
        if (date) {
            const dateObj = new Date(date + 'T00:00:00');
            previewDate.textContent = dateObj.toLocaleDateString('pt-BR');
        }
        
        // Atualizar valor
        if (amount) {
            const formattedAmount = 'R$ ' + parseFloat(amount).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            previewAmount.textContent = (type === 'expense' ? '-' : '+') + formattedAmount;
            previewAmount.className = 'mb-0 ' + (type === 'expense' ? 'text-danger' : 'text-success');
        } else {
            previewAmount.textContent = 'R$ 0,00';
            previewAmount.className = 'mb-0';
        }
        
        // Atualizar tipo
        previewType.textContent = type === 'income' ? 'Receita' : type === 'expense' ? 'Despesa' : 'Tipo';
        
        // Atualizar ícone
        previewIcon.className = 'transaction-icon ' + (type === 'expense' ? 'bg-danger' : type === 'income' ? 'bg-success' : 'bg-secondary');
    }
    
    // Sistema de tags
    function renderTags() {
        const container = document.getElementById('tags_container');
        container.innerHTML = '';
        
        tags.forEach((tag, index) => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag-item';
            tagElement.innerHTML = `#${tag} <span class="tag-remove" onclick="removeTag(${index})">×</span>`;
            container.appendChild(tagElement);
        });
        
        document.getElementById('tags_hidden').value = JSON.stringify(tags);
    }
    
    window.removeTag = function(index) {
        tags.splice(index, 1);
        renderTags();
    };
    
    // Adicionar tag ao pressionar Enter
    document.getElementById('tags_input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = this.value.trim();
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                renderTags();
                this.value = '';
            }
        }
    });
    
    // Event listeners
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', filterCategories);
    });
    
    document.getElementById('amount').addEventListener('input', updatePreview);
    document.getElementById('description').addEventListener('input', updatePreview);
    document.getElementById('account_id').addEventListener('change', updatePreview);
    document.getElementById('category_id').addEventListener('change', updatePreview);
    document.getElementById('transaction_date').addEventListener('change', updatePreview);
    
    // Inicializar
    filterCategories();
    renderTags();
    updatePreview();
});
</script>
@endpush