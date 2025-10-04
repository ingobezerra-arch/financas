@extends('layouts.app')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Editar Conta: ') . $account->name }}</h4>
                    <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('accounts.update', $account) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('Nome da Conta') }} <span class="text-danger">*</span></label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" value="{{ old('name', $account->name) }}" required autocomplete="name" autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">{{ __('Tipo de Conta') }} <span class="text-danger">*</span></label>
                                    <select id="type" class="form-select @error('type') is-invalid @enderror" name="type" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="checking" {{ old('type', $account->type) == 'checking' ? 'selected' : '' }}>Conta Corrente</option>
                                        <option value="savings" {{ old('type', $account->type) == 'savings' ? 'selected' : '' }}>Poupança</option>
                                        <option value="credit_card" {{ old('type', $account->type) == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                        <option value="investment" {{ old('type', $account->type) == 'investment' ? 'selected' : '' }}>Investimento</option>
                                        <option value="cash" {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                                        <option value="digital_wallet" {{ old('type', $account->type) == 'digital_wallet' ? 'selected' : '' }}>Carteira Digital</option>
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
                                    <label for="balance" class="form-label">{{ __('Saldo Atual') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input id="balance" type="number" step="0.01" 
                                               class="form-control @error('balance') is-invalid @enderror" 
                                               name="balance" value="{{ old('balance', $account->balance) }}" required>
                                        @error('balance')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Alterar o saldo não criará uma transação automática.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="currency" class="form-label">{{ __('Moeda') }}</label>
                                    <select id="currency" class="form-select @error('currency') is-invalid @enderror" name="currency">
                                        <option value="BRL" {{ old('currency', $account->currency) == 'BRL' ? 'selected' : '' }}>Real (BRL)</option>
                                        <option value="USD" {{ old('currency', $account->currency) == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
                                        <option value="EUR" {{ old('currency', $account->currency) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                    </select>
                                    @error('currency')
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
                                    <label for="color" class="form-label">{{ __('Cor da Conta') }}</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input id="color" type="color" 
                                               class="form-control form-control-color @error('color') is-invalid @enderror" 
                                               name="color" value="{{ old('color', $account->color) }}" style="width: 60px;">
                                        <input type="text" class="form-control" 
                                               value="{{ old('color', $account->color) }}" 
                                               onchange="document.getElementById('color').value = this.value"
                                               oninput="document.getElementById('color').value = this.value">
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
                                        <option value="fas fa-university" {{ old('icon', $account->icon) == 'fas fa-university' ? 'selected' : '' }}>🏛️ Banco</option>
                                        <option value="fas fa-credit-card" {{ old('icon', $account->icon) == 'fas fa-credit-card' ? 'selected' : '' }}>💳 Cartão</option>
                                        <option value="fas fa-piggy-bank" {{ old('icon', $account->icon) == 'fas fa-piggy-bank' ? 'selected' : '' }}>🐷 Poupança</option>
                                        <option value="fas fa-chart-line" {{ old('icon', $account->icon) == 'fas fa-chart-line' ? 'selected' : '' }}>📈 Investimento</option>
                                        <option value="fas fa-wallet" {{ old('icon', $account->icon) == 'fas fa-wallet' ? 'selected' : '' }}>👛 Carteira</option>
                                        <option value="fas fa-money-bill" {{ old('icon', $account->icon) == 'fas fa-money-bill' ? 'selected' : '' }}>💵 Dinheiro</option>
                                        <option value="fas fa-mobile-alt" {{ old('icon', $account->icon) == 'fas fa-mobile-alt' ? 'selected' : '' }}>📱 Carteira Digital</option>
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
                                              name="description" rows="3" placeholder="Descrição opcional da conta">{{ old('description', $account->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Status da Conta') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Conta Ativa
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Contas inativas não aparecem em relatórios.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
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
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
document.getElementById('color').addEventListener('change', function() {
    this.nextElementSibling.value = this.value;
});
</script>
@endpush