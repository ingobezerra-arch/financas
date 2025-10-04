@extends('layouts.app')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Nova Categoria') }}</h4>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('Nome da Categoria') }} <span class="text-danger">*</span></label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
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
                                        <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>
                                            💰 Receita
                                        </option>
                                        <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>
                                            💸 Despesa
                                        </option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
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
                                               name="color" value="{{ old('color', '#007bff') }}" style="width: 60px;">
                                        <input type="text" class="form-control" 
                                               value="{{ old('color', '#007bff') }}" 
                                               onchange="document.getElementById('color').value = this.value"
                                               oninput="document.getElementById('color').value = this.value"
                                               placeholder="#007bff">
                                    </div>
                                    @error('color')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">A cor será automaticamente definida baseada no tipo se não for especificada.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">{{ __('Ícone') }}</label>
                                    <select id="icon" class="form-select @error('icon') is-invalid @enderror" name="icon">
                                        <option value="">Selecione um ícone</option>
                                        
                                        <optgroup label="💰 Receitas">
                                            <option value="fas fa-briefcase" {{ old('icon') == 'fas fa-briefcase' ? 'selected' : '' }}>💼 Trabalho</option>
                                            <option value="fas fa-laptop" {{ old('icon') == 'fas fa-laptop' ? 'selected' : '' }}>💻 Freelance</option>
                                            <option value="fas fa-chart-line" {{ old('icon') == 'fas fa-chart-line' ? 'selected' : '' }}>📈 Investimentos</option>
                                            <option value="fas fa-shopping-cart" {{ old('icon') == 'fas fa-shopping-cart' ? 'selected' : '' }}>🛒 Vendas</option>
                                            <option value="fas fa-gift" {{ old('icon') == 'fas fa-gift' ? 'selected' : '' }}>🎁 Presentes</option>
                                            <option value="fas fa-home" {{ old('icon') == 'fas fa-home' ? 'selected' : '' }}>🏠 Aluguel</option>
                                        </optgroup>
                                        
                                        <optgroup label="💸 Despesas">
                                            <option value="fas fa-utensils" {{ old('icon') == 'fas fa-utensils' ? 'selected' : '' }}>🍽️ Alimentação</option>
                                            <option value="fas fa-car" {{ old('icon') == 'fas fa-car' ? 'selected' : '' }}>🚗 Transporte</option>
                                            <option value="fas fa-home" {{ old('icon') == 'fas fa-home' ? 'selected' : '' }}>🏠 Moradia</option>
                                            <option value="fas fa-heartbeat" {{ old('icon') == 'fas fa-heartbeat' ? 'selected' : '' }}>❤️ Saúde</option>
                                            <option value="fas fa-graduation-cap" {{ old('icon') == 'fas fa-graduation-cap' ? 'selected' : '' }}>🎓 Educação</option>
                                            <option value="fas fa-film" {{ old('icon') == 'fas fa-film' ? 'selected' : '' }}>🎬 Entretenimento</option>
                                            <option value="fas fa-tshirt" {{ old('icon') == 'fas fa-tshirt' ? 'selected' : '' }}>👕 Roupas</option>
                                            <option value="fas fa-receipt" {{ old('icon') == 'fas fa-receipt' ? 'selected' : '' }}>🧾 Impostos</option>
                                            <option value="fas fa-shield-alt" {{ old('icon') == 'fas fa-shield-alt' ? 'selected' : '' }}>🛡️ Seguros</option>
                                            <option value="fas fa-plane" {{ old('icon') == 'fas fa-plane' ? 'selected' : '' }}>✈️ Viagens</option>
                                            <option value="fas fa-paw" {{ old('icon') == 'fas fa-paw' ? 'selected' : '' }}>🐾 Pets</option>
                                            <option value="fas fa-gamepad" {{ old('icon') == 'fas fa-gamepad' ? 'selected' : '' }}>🎮 Games</option>
                                        </optgroup>
                                        
                                        <optgroup label="⚡ Outros">
                                            <option value="fas fa-tag" {{ old('icon') == 'fas fa-tag' ? 'selected' : '' }}>🏷️ Geral</option>
                                            <option value="fas fa-star" {{ old('icon') == 'fas fa-star' ? 'selected' : '' }}>⭐ Favorito</option>
                                            <option value="fas fa-plus-circle" {{ old('icon') == 'fas fa-plus-circle' ? 'selected' : '' }}>➕ Outros</option>
                                            <option value="fas fa-minus-circle" {{ old('icon') == 'fas fa-minus-circle' ? 'selected' : '' }}>➖ Diversos</option>
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

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Descrição') }}</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                                      name="description" rows="3" 
                                      placeholder="Descrição opcional para a categoria">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Preview da Categoria -->
                        <div class="mb-4">
                            <label class="form-label">{{ __('Preview') }}</label>
                            <div class="card border-light bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div id="category-preview-icon" class="category-icon" style="background-color: #007bff20; border: 2px solid #007bff;">
                                                <i id="preview-icon" class="fas fa-tag" style="color: #007bff;"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0" id="preview-name">Nome da Categoria</h6>
                                            <small class="text-muted" id="preview-type">Tipo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Categoria
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

    // Auto-definir cor baseada no tipo
    typeSelect.addEventListener('change', function() {
        if (this.value === 'income' && !colorInput.dataset.userChanged) {
            colorInput.value = '#28a745';
            colorInput.nextElementSibling.value = '#28a745';
        } else if (this.value === 'expense' && !colorInput.dataset.userChanged) {
            colorInput.value = '#dc3545';
            colorInput.nextElementSibling.value = '#dc3545';
        }
        updatePreview();
    });

    // Marcar quando usuário alterar cor manualmente
    colorInput.addEventListener('change', function() {
        this.dataset.userChanged = 'true';
        this.nextElementSibling.value = this.value;
        updatePreview();
    });

    // Sincronizar inputs de cor
    colorInput.nextElementSibling.addEventListener('input', function() {
        colorInput.value = this.value;
        colorInput.dataset.userChanged = 'true';
        updatePreview();
    });

    // Eventos de atualização
    nameInput.addEventListener('input', updatePreview);
    iconSelect.addEventListener('change', updatePreview);
    
    // Preview inicial
    updatePreview();
});
</script>
@endpush