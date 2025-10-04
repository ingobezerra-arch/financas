@extends('layouts.app')

@section('title', 'Detalhes da Integração - ' . $bankIntegration->bank_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $bankIntegration->bank_name }}</h1>
                    <p class="text-muted mb-0">
                        Integração criada em {{ $bankIntegration->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                    @if($bankIntegration->isOperational())
                        <button type="button" class="btn btn-success me-2 sync-btn" data-integration-id="{{ $bankIntegration->id }}">
                            <i class="fas fa-sync-alt me-2"></i>Sincronizar
                        </button>
                    @endif
                    <a href="{{ route('bank-integrations.edit', $bankIntegration) }}" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>Configurações
                    </a>
                </div>
            </div>

            <!-- Status da Integração -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Status da Integração</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status Geral</label>
                                        <div>
                                            <span class="badge {{ $bankIntegration->isOperational() ? 'bg-success' : 'bg-warning' }} fs-6">
                                                {{ $bankIntegration->isOperational() ? 'Operacional' : 'Inativa' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status de Consentimento</label>
                                        <div>
                                            <span class="badge {{ $bankIntegration->status === 'active' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($bankIntegration->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Última Sincronização</label>
                                        <div>
                                            @if($bankIntegration->last_sync_at)
                                                {{ $bankIntegration->last_sync_at->format('d/m/Y H:i') }}
                                                <small class="text-muted">({{ $bankIntegration->last_sync_at->diffForHumans() }})</small>
                                            @else
                                                <span class="text-muted">Nunca sincronizada</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Expiração do Consentimento</label>
                                        <div>
                                            @if($bankIntegration->consent_expires_at)
                                                {{ $bankIntegration->consent_expires_at->format('d/m/Y') }}
                                                @if($bankIntegration->consent_expires_at->isPast())
                                                    <span class="badge bg-danger ms-2">Expirado</span>
                                                @elseif($bankIntegration->consent_expires_at->diffInDays() < 7)
                                                    <span class="badge bg-warning ms-2">Expira em breve</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Não definido</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contagem de Erros</label>
                                        <div>
                                            <span class="badge {{ $bankIntegration->error_count > 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $bankIntegration->error_count }} erro(s)
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Sincronização Automática</label>
                                        <div>
                                            <span class="badge {{ $bankIntegration->auto_sync ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $bankIntegration->auto_sync ? 'Ativada' : 'Desativada' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($bankIntegration->last_error)
                                <div class="alert alert-danger mt-3">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Último Erro</h6>
                                    <p class="mb-0">{{ $bankIntegration->last_error }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Estatísticas</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h3 class="text-primary mb-1">{{ $stats['total_transactions'] }}</h3>
                                <p class="text-muted mb-0">Total de Transações</p>
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h5 class="text-success mb-1">{{ $stats['processed_transactions'] }}</h5>
                                        <p class="text-muted mb-0 small">Processadas</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-warning mb-1">{{ $stats['unprocessed_transactions'] }}</h5>
                                    <p class="text-muted mb-0 small">Pendentes</p>
                                </div>
                            </div>
                            <hr>
                            <div class="text-center">
                                <h5 class="mb-1">R$ {{ number_format($stats['total_amount'], 2, ',', '.') }}</h5>
                                <p class="text-muted mb-0 small">Volume Total</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contas Vinculadas -->
            @if($bankIntegration->getAvailableAccounts())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Contas Vinculadas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($bankIntegration->getAvailableAccounts() as $account)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $account['account_type'] ?? 'Conta' }}</h6>
                                                <p class="text-muted mb-0">
                                                    Ag: {{ $account['agency'] ?? 'N/A' }} | 
                                                    C/C: {{ $account['number'] ?? 'N/A' }}
                                                </p>
                                            </div>
                                            @if(isset($account['balance']))
                                                <div class="text-end">
                                                    <h6 class="mb-0">R$ {{ number_format($account['balance']['current'] ?? 0, 2, ',', '.') }}</h6>
                                                    <small class="text-muted">Saldo atual</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Transações Recentes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Transações Sincronizadas</h5>
                    <a href="{{ route('bank-integrations.transactions') }}?bank_integration_id={{ $bankIntegration->id }}" 
                       class="btn btn-outline-primary btn-sm">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if($syncedTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($syncedTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                            <td>
                                                {{ $transaction->description }}
                                                @if($transaction->counterpart_name)
                                                    <br><small class="text-muted">{{ $transaction->counterpart_name }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->category)
                                                    <span class="badge bg-secondary">{{ $transaction->category->name }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $syncedTransactions->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h5>Nenhuma transação sincronizada</h5>
                            <p class="text-muted">Execute uma sincronização para importar transações desta integração.</p>
                            @if($bankIntegration->isOperational())
                                <button type="button" class="btn btn-primary sync-btn" data-integration-id="{{ $bankIntegration->id }}">
                                    <i class="fas fa-sync-alt me-2"></i>Sincronizar Agora
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de sincronização -->
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página de detalhes da integração carregada');
    
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
    
    // Verificar se os elementos do modal existem
    const modal = document.getElementById('syncModal');
    const form = document.getElementById('syncForm');
    console.log('Modal encontrado:', !!modal);
    console.log('Form encontrado:', !!form);
});

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
    form.action = `{{ route('bank-integrations.sync', '') }}/${integrationId}`;
    
    console.log('Abrindo modal para sincronização, action:', form.action);
    modalInstance.show();
}
</script>
@endpush