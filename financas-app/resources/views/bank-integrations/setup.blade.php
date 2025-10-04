@extends('layouts.app')

@section('title', 'Configuração de Bancos Reais')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">🏦 Configuração para Bancos Reais</h4>
                </div>
                <div class="card-body">
                    @php
                        $sandboxMode = config('open_finance.sandbox_mode', true);
                        $useRealApis = config('open_finance.production.use_real_apis', false);
                        $clientId = config('open_finance.client_id');
                        $clientSecret = config('open_finance.client_secret');
                        $certPath = config('open_finance.production.mtls_cert');
                        $keyPath = config('open_finance.production.mtls_key');
                        $isConfigured = !$sandboxMode && $useRealApis && $clientId && $clientSecret && $certPath && $keyPath;
                    @endphp

                    <!-- Status Atual -->
                    <div class="alert {{ $isConfigured ? 'alert-success' : 'alert-warning' }}">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas {{ $isConfigured ? 'fa-check-circle' : 'fa-exclamation-triangle' }} fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1">
                                    @if($isConfigured)
                                        ✅ Sistema Configurado para Bancos Reais
                                    @else
                                        ⚠️ Sistema em Modo de Demonstração
                                    @endif
                                </h5>
                                <p class="mb-0">
                                    @if($isConfigured)
                                        Suas integrações bancárias usarão dados reais dos bancos.
                                    @else
                                        Atualmente usando dados fictícios para demonstração.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Status -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Configuração</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Modo Sandbox</strong></td>
                                    <td>
                                        @if($sandboxMode)
                                            <span class="badge bg-warning">Ativado</span>
                                        @else
                                            <span class="badge bg-success">Desativado</span>
                                        @endif
                                    </td>
                                    <td>{{ $sandboxMode ? 'Simulação' : 'Produção' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>APIs Reais</strong></td>
                                    <td>
                                        @if($useRealApis)
                                            <span class="badge bg-success">Ativadas</span>
                                        @else
                                            <span class="badge bg-warning">Desativadas</span>
                                        @endif
                                    </td>
                                    <td>{{ $useRealApis ? 'Usando APIs dos bancos' : 'Usando dados simulados' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Client ID</strong></td>
                                    <td>
                                        @if($clientId && $clientId !== 'your-client-id')
                                            <span class="badge bg-success">✓ Configurado</span>
                                        @else
                                            <span class="badge bg-danger">✗ Não configurado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($clientId && $clientId !== 'your-client-id')
                                            {{ substr($clientId, 0, 20) }}...
                                        @else
                                            Usar credenciais reais do Open Finance
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Client Secret</strong></td>
                                    <td>
                                        @if($clientSecret && $clientSecret !== 'your-client-secret')
                                            <span class="badge bg-success">✓ Configurado</span>
                                        @else
                                            <span class="badge bg-danger">✗ Não configurado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($clientSecret && $clientSecret !== 'your-client-secret')
                                            ********** (Oculto por segurança)
                                        @else
                                            Necessário para autenticação
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Certificado mTLS</strong></td>
                                    <td>
                                        @if($certPath && file_exists($certPath))
                                            <span class="badge bg-success">✓ Válido</span>
                                        @else
                                            <span class="badge bg-danger">✗ Não encontrado</span>
                                        @endif
                                    </td>
                                    <td>{{ $certPath ?: 'Caminho não configurado' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Chave Privada</strong></td>
                                    <td>
                                        @if($keyPath && file_exists($keyPath))
                                            <span class="badge bg-success">✓ Válida</span>
                                        @else
                                            <span class="badge bg-danger">✗ Não encontrada</span>
                                        @endif
                                    </td>
                                    <td>{{ $keyPath ?: 'Caminho não configurado' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pré-requisitos -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">📋 Pré-requisitos para Bancos Reais</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>🏛️ Regulamentação</h6>
                                    <ul class="list-unstyled">
                                        <li>☐ Registro como TPP (Third Party Provider)</li>
                                        <li>☐ Aprovação do Banco Central</li>
                                        <li>☐ Certificação de segurança</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>🔐 Certificados e Credenciais</h6>
                                    <ul class="list-unstyled">
                                        <li>☐ Certificado ICP-Brasil válido</li>
                                        <li>☐ Certificados mTLS para cada banco</li>
                                        <li>☐ Client ID/Secret por instituição</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comandos -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">⚡ Comandos Úteis</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Verificar Configuração</h6>
                                    <code class="d-block bg-light p-2 rounded">php artisan bank:setup-real --check</code>
                                </div>
                                <div class="col-md-6">
                                    <h6>Configurar Produção</h6>
                                    <code class="d-block bg-light p-2 rounded">php artisan bank:setup-real</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aviso Importante -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Informação Importante</h6>
                        <p class="mb-2">
                            <strong>Para fins de demonstração</strong>, o modo simulado atual é perfeitamente adequado. 
                            Você pode explorar todas as funcionalidades do sistema com dados fictícios.
                        </p>
                        <p class="mb-0">
                            <strong>Para uso em produção</strong> com dados bancários reais, consulte a documentação completa 
                            em <code>BANCOS_REAIS.md</code> ou busque consultoria especializada em Open Finance.
                        </p>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar para Integrações
                        </a>
                        
                        @if(!$isConfigured)
                            <div>
                                <a href="{{ asset('BANCOS_REAIS.md') }}" target="_blank" class="btn btn-info me-2">
                                    <i class="fas fa-book me-2"></i>Documentação Completa
                                </a>
                                <button type="button" class="btn btn-warning" onclick="showSetupInstructions()">
                                    <i class="fas fa-cog me-2"></i>Como Configurar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Instruções -->
<div class="modal fade" id="setupModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🛠️ Como Configurar Bancos Reais</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="steps">
                    <div class="step mb-4">
                        <h6><span class="badge bg-primary me-2">1</span>Execute o comando de configuração</h6>
                        <code class="d-block bg-light p-2 rounded mt-2">php artisan bank:setup-real</code>
                        <small class="text-muted">Este comando irá guiá-lo através do processo de configuração.</small>
                    </div>
                    
                    <div class="step mb-4">
                        <h6><span class="badge bg-primary me-2">2</span>Tenha suas credenciais em mãos</h6>
                        <ul class="small">
                            <li>Client ID do Open Finance</li>
                            <li>Client Secret do Open Finance</li>
                            <li>Caminho para certificado mTLS (.pem)</li>
                            <li>Caminho para chave privada (.pem)</li>
                        </ul>
                    </div>
                    
                    <div class="step mb-4">
                        <h6><span class="badge bg-primary me-2">3</span>Reinicie o servidor</h6>
                        <code class="d-block bg-light p-2 rounded mt-2">php artisan serve</code>
                        <small class="text-muted">Necessário para aplicar as novas configurações.</small>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <strong>⚠️ Atenção:</strong> Certifique-se de ter todas as aprovações regulamentares 
                    antes de tentar configurar integrações reais.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="copyCommand()">
                    <i class="fas fa-copy me-1"></i>Copiar Comando
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showSetupInstructions() {
    const modal = new bootstrap.Modal(document.getElementById('setupModal'));
    modal.show();
}

function copyCommand() {
    const command = 'php artisan bank:setup-real';
    navigator.clipboard.writeText(command).then(function() {
        // Feedback visual
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copiado!';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 2000);
    });
}
</script>
@endpush