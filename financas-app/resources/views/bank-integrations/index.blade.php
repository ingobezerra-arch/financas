@extends('layouts.app')

@section('title', 'Integrações Bancárias')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Integrações Bancárias</h1>
                <div>
                    @php
                        $isRealMode = !config('open_finance.sandbox_mode', true) && config('open_finance.production.use_real_apis', false);
                    @endphp
                    
                    @if(!$isRealMode)
                        <a href="{{ route('bank-integrations.setup') }}" class="btn btn-outline-info me-2" title="Configurar bancos reais">
                            <i class="fas fa-cog me-1"></i>Bancos Reais
                        </a>
                    @endif
                    
                    <a href="{{ route('bank-integrations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nova Integração
                    </a>
                </div>
            </div>

            @if($integrations->count() > 0)
                <div class="row">
                    @foreach($integrations as $integration)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fas fa-university text-white"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="card-title mb-1">{{ $integration->bank_name }}</h5>
                                            <span class="badge {{ $integration->isOperational() ? 'bg-success' : 'bg-warning' }}">
                                                {{ $integration->isOperational() ? 'Ativa' : 'Inativa' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="mb-1">{{ $integration->syncedTransactions->count() }}</h4>
                                                <p class="text-muted mb-0 small">Transações</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="mb-1">{{ count($integration->getAvailableAccounts()) }}</h4>
                                            <p class="text-muted mb-0 small">Contas</p>
                                        </div>
                                    </div>

                                    @if($integration->last_sync_at)
                                        <p class="text-muted small mb-3">
                                            <i class="fas fa-sync-alt me-1"></i>
                                            Última sincronização: {{ $integration->last_sync_at->diffForHumans() }}
                                        </p>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('bank-integrations.show', $integration) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-eye me-1"></i>Detalhes
                                        </a>
                                        
                                        @if($integration->isOperational())
                                            <button type="button" 
                                                    class="btn btn-outline-success btn-sm sync-btn"
                                                    data-integration-id="{{ $integration->id }}">
                                                <i class="fas fa-sync-alt me-1"></i>Sincronizar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Lista de transações recentes -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Transações Sincronizadas Recentes</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $recentTransactions = \App\Models\SyncedTransaction::where('user_id', auth()->id())
                                ->with(['bankIntegration', 'category'])
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();
                        @endphp

                        @if($recentTransactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Banco</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                                <td>{{ $transaction->bankIntegration->bank_name }}</td>
                                                <td>{{ $transaction->description }}</td>
                                                <td>
                                                    <span class="text-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                                        {{ $transaction->type === 'credit' ? '+' : '-' }}
                                                        R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $transaction->is_processed ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $transaction->is_processed ? 'Processada' : 'Pendente' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(!$transaction->is_processed)
                                                        <a href="{{ route('bank-integrations.transactions') }}?unprocessed=1" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            Processar
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('bank-integrations.transactions') }}" class="btn btn-outline-primary">
                                    Ver Todas as Transações
                                </a>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Nenhuma transação sincronizada ainda.</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-university fa-3x text-muted"></i>
                        </div>
                        <h4 class="mb-3">Nenhuma integração bancária</h4>
                        <p class="text-muted mb-4">
                            Conecte suas contas bancárias para importar transações automaticamente.
                        </p>
                        <a href="{{ route('bank-integrations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Adicionar Primeira Integração
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmação para sincronização -->
<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sincronizar Transações</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Deseja sincronizar as transações desta integração bancária?</p>
                <p class="text-muted small">Isso pode levar alguns minutos dependendo da quantidade de transações.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="syncForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-1"></i>Sincronizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('Scripts de integração bancária carregados');

// Função para abrir modal de sincronização
function syncIntegration(integrationId) {
    console.log('Função syncIntegration chamada com ID:', integrationId);
    
    const modal = document.getElementById('syncModal');
    if (!modal) {
        console.error('Modal syncModal não encontrado!');
        alert('Erro: Modal não encontrado. Verifique a página.');
        return;
    }
    
    const form = document.getElementById('syncForm');
    if (!form) {
        console.error('Formulário syncForm não encontrado!');
        alert('Erro: Formulário não encontrado. Verifique a página.');
        return;
    }
    
    const modalInstance = new bootstrap.Modal(modal);
    form.action = `/bank-integrations/${integrationId}/sync`;
    
    console.log('Abrindo modal para sincronização, action:', form.action);
    modalInstance.show();
}

// Inicializar eventos quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado. Bootstrap disponível:', typeof bootstrap !== 'undefined');
    
    // Adicionar eventos aos botões de sincronização
    const syncButtons = document.querySelectorAll('.sync-btn');
    console.log('Botões de sincronização encontrados:', syncButtons.length);
    
    syncButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const integrationId = this.getAttribute('data-integration-id');
            console.log('Botão clicado, ID da integração:', integrationId);
            syncIntegration(integrationId);
        });
    });
    
    // Teste dos botões de detalhes
    const detailButtons = document.querySelectorAll('a[href*="bank-integrations/"]');
    console.log('Botões de detalhes encontrados:', detailButtons.length);
    
    detailButtons.forEach((button, index) => {
        button.addEventListener('click', function(e) {
            console.log(`Clique no botão de detalhes ${index + 1}:`, this.href);
            
            // Verificar se o href está válido
            if (!this.href || this.href === '') {
                e.preventDefault();
                console.error('URL inválida para o botão de detalhes');
                alert('Erro: URL inválida. Tente recarregar a página.');
            }
        });
    });
    
    // Debug: verificar se os elementos estão na página
    const modal = document.getElementById('syncModal');
    const form = document.getElementById('syncForm');
    console.log('Modal encontrado:', !!modal);
    console.log('Form encontrado:', !!form);
});
</script>
@endpush