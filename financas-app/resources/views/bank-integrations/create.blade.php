@extends('layouts.app')

@section('title', 'Nova Integração Bancária')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Nova Integração Bancária</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Sobre as integrações:</strong> Conecte suas contas bancárias de forma segura 
                        para importar transações automaticamente. Utilizamos o Open Finance para garantir 
                        a segurança dos seus dados.
                    </div>

                    <form method="POST" action="{{ route('bank-integrations.store') }}" id="bankForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Selecione seu banco</label>
                            <div class="row">
                                @foreach($supportedBanks as $code => $name)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="bank_code" 
                                                   id="bank_{{ $code }}" 
                                                   value="{{ $code }}"
                                                   {{ old('bank_code') == $code ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="bank_{{ $code }}">
                                                <div class="d-flex align-items-center p-3 border rounded">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-university text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">{{ $name }}</h6>
                                                        <small class="text-muted">Código: {{ $code }}</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('bank_code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissões solicitadas</label>
                            <div class="alert alert-light">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked disabled>
                                    <label class="form-check-label">
                                        <strong>Leitura de contas</strong> - Visualizar suas contas bancárias
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked disabled>
                                    <label class="form-check-label">
                                        <strong>Saldos das contas</strong> - Visualizar saldos atuais
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked disabled>
                                    <label class="form-check-label">
                                        <strong>Histórico de transações</strong> - Importar transações dos últimos 12 meses
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted">
                                Essas permissões são necessárias para o funcionamento básico da integração.
                            </small>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-shield-alt me-2"></i>Segurança e Privacidade</h6>
                                <ul class="mb-0">
                                    <li>Não armazenamos suas credenciais bancárias</li>
                                    <li>A conexão é feita através do Open Finance (padrão do Banco Central)</li>
                                    <li>Você pode revogar o acesso a qualquer momento</li>
                                    <li>Os dados são criptografados e protegidos</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms_accept" required>
                                <label class="form-check-label" for="terms_accept">
                                    Aceito os termos de uso e autorizo a conexão com minha conta bancária
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-link me-2"></i>Conectar Banco
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Dúvidas Frequentes</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    É seguro conectar minha conta bancária?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim, é completamente seguro. Utilizamos o Open Finance, padrão regulamentado pelo 
                                    Banco Central do Brasil. Não temos acesso às suas credenciais bancárias e todas 
                                    as conexões são criptografadas.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Que tipo de transações são importadas?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Importamos todas as transações de entrada e saída da sua conta, incluindo transferências, 
                                    pagamentos, depósitos e saques dos últimos 12 meses.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Como revogar o acesso?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Você pode revogar o acesso a qualquer momento através da página de integrações 
                                    bancárias ou diretamente no seu banco. O acesso será removido imediatamente.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bankForm');
    const submitBtn = document.getElementById('submitBtn');
    
    console.log('Bank integration form loaded');
    
    // Debug: verificar se o formulário existe
    if (!form) {
        console.error('Formulário não encontrado!');
        return;
    }
    
    // Event listener para o submit
    form.addEventListener('submit', function(e) {
        console.log('Formulário sendo enviado...');
        
        // Verifica se um banco foi selecionado
        const selectedBank = form.querySelector('input[name="bank_code"]:checked');
        if (!selectedBank) {
            e.preventDefault();
            alert('Por favor, selecione um banco.');
            console.log('Nenhum banco selecionado');
            return false;
        }
        
        // Verifica se os termos foram aceitos
        const termsAccepted = document.getElementById('terms_accept').checked;
        if (!termsAccepted) {
            e.preventDefault();
            alert('Por favor, aceite os termos de uso.');
            console.log('Termos não aceitos');
            return false;
        }
        
        console.log('Banco selecionado:', selectedBank.value);
        console.log('Termos aceitos:', termsAccepted);
        
        // Desabilita o botão para evitar duplo clique
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Conectando...';
        
        return true;
    });
    
    // Debug: verificar clique no botão
    submitBtn.addEventListener('click', function(e) {
        console.log('Botão clicado!');
    });
    
    // Debug: verificar seleção de banco
    const bankRadios = form.querySelectorAll('input[name="bank_code"]');
    bankRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            console.log('Banco selecionado:', this.value);
        });
    });
});
</script>
@endpush