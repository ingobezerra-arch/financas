@extends('layouts.app')

@section('title', 'Perfil do Usuário')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-circle text-primary"></i>
                        Meu Perfil
                    </h1>
                    <p class="text-muted">Gerencie suas informações pessoais e preferências</p>
                </div>
            </div>

            <!-- Status Messages -->
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    Perfil atualizado com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    Senha atualizada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('status') === 'theme-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    Tema atualizado com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Informações do Perfil -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user"></i>
                                Informações Pessoais
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nome Completo</label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        
                                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                            <div class="mt-2">
                                                <div class="alert alert-warning" role="alert">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Seu e-mail não foi verificado.
                                                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-link p-0 align-baseline">
                                                            Clique aqui para reenviar o e-mail de verificação.
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Alterar Senha -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-lock"></i>
                                Alterar Senha
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="current_password" class="form-label">Senha Atual</label>
                                        <input type="password" 
                                               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                               id="current_password" 
                                               name="current_password" 
                                               required>
                                        @error('current_password', 'updatePassword')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Nova Senha</label>
                                        <input type="password" 
                                               class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required>
                                        @error('password', 'updatePassword')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key"></i>
                                        Alterar Senha
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Configurações e Preferências -->
                <div class="col-lg-4">
                    <!-- Preferências de Tema -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-palette"></i>
                                Tema da Interface
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update-theme') }}" id="themeForm">
                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <p class="text-muted mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        Escolha o tema de sua preferência para a interface do sistema.
                                    </p>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card theme-option {{ (old('theme', $user->theme ?? 'light') === 'light') ? 'border-primary' : '' }}" 
                                                 data-theme="light" 
                                                 style="cursor: pointer;">
                                                <div class="card-body text-center p-3">
                                                    <i class="fas fa-sun fa-2x text-warning mb-2"></i>
                                                    <h6 class="mb-1">Tema Claro</h6>
                                                    <small class="text-muted">Interface clara e limpa</small>
                                                    @if((old('theme', $user->theme ?? 'light') === 'light'))
                                                        <div class="mt-2">
                                                            <i class="fas fa-check-circle text-primary"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card theme-option {{ (old('theme', $user->theme ?? 'light') === 'dark') ? 'border-primary' : '' }}" 
                                                 data-theme="dark" 
                                                 style="cursor: pointer;">
                                                <div class="card-body text-center p-3">
                                                    <i class="fas fa-moon fa-2x text-info mb-2"></i>
                                                    <h6 class="mb-1">Tema Escuro</h6>
                                                    <small class="text-muted">Interface escura e elegante</small>
                                                    @if((old('theme', $user->theme ?? 'light') === 'dark'))
                                                        <div class="mt-2">
                                                            <i class="fas fa-check-circle text-primary"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="theme" id="themeInput" value="{{ old('theme', $user->theme ?? 'light') }}">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary" id="saveThemeBtn">
                                        <i class="fas fa-save"></i>
                                        Salvar Tema
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Informações da Conta -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i>
                                Informações da Conta
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted">Data de Cadastro</label>
                                    <div class="fw-bold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted">Última Atualização</label>
                                    <div class="fw-bold">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                                @if($user->email_verified_at)
                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted">E-mail Verificado</label>
                                        <div class="fw-bold text-success">
                                            <i class="fas fa-check-circle"></i>
                                            {{ $user->email_verified_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas Rápidas -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar"></i>
                                Estatísticas Rápidas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-0">{{ $user->accounts()->count() }}</h4>
                                        <small class="text-muted">Contas</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-success mb-0">{{ $user->transactions()->count() }}</h4>
                                    <small class="text-muted">Transações</small>
                                </div>
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-warning mb-0">{{ $user->budgets()->count() }}</h4>
                                        <small class="text-muted">Orçamentos</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info mb-0">{{ $user->goals()->count() }}</h4>
                                    <small class="text-muted">Metas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zona de Perigo -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                Zona de Perigo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="text-danger">Excluir Conta</h6>
                                    <p class="text-muted mb-0">
                                        Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente apagados. 
                                        Antes de excluir sua conta, faça o download de qualquer informação que deseja manter.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                        <i class="fas fa-trash"></i>
                                        Excluir Conta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Exclusão da Conta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                    </div>
                    
                    <p>Tem certeza de que deseja excluir sua conta? Todos os seus recursos e dados serão removidos permanentemente.</p>
                    
                    <div class="mb-3">
                        <label for="password_delete" class="form-label">Digite sua senha para confirmar:</label>
                        <input type="password" 
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                               id="password_delete" 
                               name="password" 
                               required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Sim, Excluir Conta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gerenciar seleção de tema
    const themeOptions = document.querySelectorAll('.theme-option');
    const themeInput = document.getElementById('themeInput');
    
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const selectedTheme = this.dataset.theme;
            
            // Remover seleção anterior
            themeOptions.forEach(opt => {
                opt.classList.remove('border-primary');
                const checkIcon = opt.querySelector('.fa-check-circle');
                if (checkIcon) {
                    checkIcon.remove();
                }
            });
            
            // Adicionar seleção atual
            this.classList.add('border-primary');
            const cardBody = this.querySelector('.card-body');
            cardBody.insertAdjacentHTML('beforeend', '<div class="mt-2"><i class="fas fa-check-circle text-primary"></i></div>');
            
            // Atualizar input hidden
            themeInput.value = selectedTheme;
        });
    });
    
    // Auto-dismiss alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        });
    }, 5000);
    
    // Show delete modal if there are errors
    @if ($errors->userDeletion->any())
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        deleteModal.show();
    @endif
});
</script>
@endpush