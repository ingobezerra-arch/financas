@extends('layouts.app')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Editar Categoria: ') . $category->name }}</h4>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('Nome da Categoria') }} <span class="text-danger">*</span></label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" value="{{ old('name', $category->name) }}" required autocomplete="name" autofocus
                                           placeholder="Ex: Alimentação, Salário, etc.">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">{{ __('Tipo') }} <span class="text-danger">*</span></label>
                                    <select id="type" class="form-select @error('type') is-invalid @enderror" name="type" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>
                                            💰 Receita
                                        </option>
                                        <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>
                                            💸 Despesa
                                        </option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @if($category->transactions()->count() > 0)
                                        <small class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Esta categoria possui {{ $category->transactions()->count() }} transação(ões). Alterar o tipo pode afetar relatórios.
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">{{ __('Cor da Categoria') }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input id="color" type="color" 
                                               class="form-control form-control-color @error('color') is-invalid @enderror" 
                                               name="color" value="{{ old('color', $category->color) }}" style="width: 60px;">
                                        <input type="text" class="form-control" 
                                               value="{{ old('color', $category->color) }}" 
                                               onchange="document.getElementById('color').value = this.value"
                                               oninput="document.getElementById('color').value = this.value"
                                               placeholder="#007bff">
                                    </div>
                                    @error('color')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">{{ __('Ícone') }}</label>
                                    <select id="icon" class="form-select @error('icon') is-invalid @enderror" name="icon">
                                        <option value="">Selecione um ícone</option>
                                        
                                        <optgroup label="💰 Receitas">
                                            <option value="fas fa-briefcase" {{ old('icon', $category->icon) == 'fas fa-briefcase' ? 'selected' : '' }}>💼 Trabalho</option>
                                            <option value="fas fa-laptop" {{ old('icon', $category->icon) == 'fas fa-laptop' ? 'selected' : '' }}>💻 Freelance</option>
                                            <option value="fas fa-chart-line" {{ old('icon', $category->icon) == 'fas fa-chart-line' ? 'selected' : '' }}>📈 Investimentos</option>
                                            <option value="fas fa-shopping-cart" {{ old('icon', $category->icon) == 'fas fa-shopping-cart' ? 'selected' : '' }}>🛒 Vendas</option>
                                            <option value="fas fa-gift" {{ old('icon', $category->icon) == 'fas fa-gift' ? 'selected' : '' }}>🎁 Presentes</option>
                                            <option value="fas fa-home" {{ old('icon', $category->icon) == 'fas fa-home' ? 'selected' : '' }}>🏠 Aluguel</option>
                                        </optgroup>
                                        
                                        <optgroup label="💸 Despesas">
                                            <option value="fas fa-utensils" {{ old('icon', $category->icon) == 'fas fa-utensils' ? 'selected' : '' }}>🍽️ Alimentação</option>
                                            <option value="fas fa-car" {{ old('icon', $category->icon) == 'fas fa-car' ? 'selected' : '' }}>🚗 Transporte</option>
                                            <option value="fas fa-home" {{ old('icon', $category->icon) == 'fas fa-home' ? 'selected' : '' }}>🏠 Moradia</option>
                                            <option value="fas fa-heartbeat" {{ old('icon', $category->icon) == 'fas fa-heartbeat' ? 'selected' : '' }}>❤️ Saúde</option>
                                            <option value="fas fa-graduation-cap" {{ old('icon', $category->icon) == 'fas fa-graduation-cap' ? 'selected' : '' }}>🎓 Educação</option>
                                            <option value="fas fa-film" {{ old('icon', $category->icon) == 'fas fa-film' ? 'selected' : '' }}>🎬 Entretenimento</option>
                                            <option value="fas fa-tshirt" {{ old('icon', $category->icon) == 'fas fa-tshirt' ? 'selected' : '' }}>👕 Roupas</option>
                                            <option value="fas fa-receipt" {{ old('icon', $category->icon) == 'fas fa-receipt' ? 'selected' : '' }}>🧾 Impostos</option>
                                            <option value="fas fa-shield-alt" {{ old('icon', $category->icon) == 'fas fa-shield-alt' ? 'selected' : '' }}>🛡️ Seguros</option>
                                            <option value="fas fa-plane" {{ old('icon', $category->icon) == 'fas fa-plane' ? 'selected' : '' }}>✈️ Viagens</option>
                                            <option value="fas fa-paw" {{ old('icon', $category->icon) == 'fas fa-paw' ? 'selected' : '' }}>🐾 Pets</option>
                                            <option value="fas fa-gamepad" {{ old('icon', $category->icon) == 'fas fa-gamepad' ? 'selected' : '' }}>🎮 Games</option>
                                        </optgroup>
                                        
                                        <optgroup label="⚡ Outros">
                                            <option value="fas fa-tag" {{ old('icon', $category->icon) == 'fas fa-tag' ? 'selected' : '' }}>🏷️ Geral</option>
                                            <option value="fas fa-star" {{ old('icon', $category->icon) == 'fas fa-star' ? 'selected' : '' }}>⭐ Favorito</option>
                                            <option value="fas fa-plus-circle" {{ old('icon', $category->icon) == 'fas fa-plus-circle' ? 'selected' : '' }}>➕ Outros</option>
                                            <option value="fas fa-minus-circle" {{ old('icon', $category->icon) == 'fas fa-minus-circle' ? 'selected' : '' }}>➖ Diversos</option>
                                        </optgroup>
                                    </select>
                                    @error('icon')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('Descrição') }}</label>
                                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                                              name="description" rows="3" 
                                              placeholder="Descrição opcional para a categoria">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Status da Categoria') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Categoria Ativa
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Categorias inativas não aparecem na criação de transações.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview da Categoria -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('Preview') }}</label>
                            <div class="card border-light bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div id="category-preview-icon" class="category-icon" style="background-color: {{ $category->color }}20; border: 2px solid {{ $category->color }};">
                                                <i id="preview-icon" class="{{ $category->icon ?: 'fas fa-tag' }}" style="color: {{ $category->color }};"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0" id="preview-name">{{ $category->name }}</h6>
                                            <small class="text-muted" id="preview-type">{{ $category->type === 'income' ? 'Receita' : 'Despesa' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        @if($category->transactions()->count() > 0 || $category->budgets()->count() > 0)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Informações da Categoria</h6>
                                <ul class="mb-0">
                                    @if($category->transactions()->count() > 0)
                                        <li>{{ $category->transactions()->count() }} transação(ões) associada(s)</li>
                                    @endif
                                    @if($category->budgets()->count() > 0)
                                        <li>{{ $category->budgets()->count() }} orçamento(s) associado(s)</li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-icon {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const typeSelect = document.getElementById('type');
    const colorInput = document.getElementById('color');
    const iconSelect = document.getElementById('icon');
    
    const previewName = document.getElementById('preview-name');
    const previewType = document.getElementById('preview-type');
    const previewIcon = document.getElementById('preview-icon');
    const previewIconContainer = document.getElementById('category-preview-icon');

    function updatePreview() {
        // Atualizar nome
        previewName.textContent = nameInput.value || 'Nome da Categoria';
        
        // Atualizar tipo
        const typeText = typeSelect.value === 'income' ? 'Receita' : 
                        typeSelect.value === 'expense' ? 'Despesa' : 'Tipo';
        previewType.textContent = typeText;
        
        // Atualizar cor
        const color = colorInput.value;
        previewIconContainer.style.backgroundColor = color + '20';
        previewIconContainer.style.borderColor = color;
        previewIcon.style.color = color;
        
        // Atualizar ícone
        if (iconSelect.value) {
            previewIcon.className = iconSelect.value;
        } else {
            previewIcon.className = 'fas fa-tag';
        }
    }

    // Sincronizar inputs de cor
    colorInput.addEventListener('change', function() {
        this.nextElementSibling.value = this.value;
        updatePreview();
    });

    colorInput.nextElementSibling.addEventListener('input', function() {
        colorInput.value = this.value;
        updatePreview();
    });

    // Eventos de atualização
    nameInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', updatePreview);
    iconSelect.addEventListener('change', updatePreview);
    
    // Preview inicial
    updatePreview();
});
</script>
@endpush