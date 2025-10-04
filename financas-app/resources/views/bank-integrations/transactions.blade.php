@extends('layouts.app')

@section('title', 'Transações Sincronizadas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Transações Sincronizadas</h1>
                <a href="{{ route('bank-integrations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('bank-integrations.transactions') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Banco</label>
                                <select name="bank_integration_id" class="form-select">
                                    <option value="">Todos os bancos</option>
                                    @foreach($integrations as $id => $name)
                                        <option value="{{ $id }}" {{ request('bank_integration_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipo</label>
                                <select name="type" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Entrada</option>
                                    <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Saída</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="is_processed" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="0" {{ request('is_processed') === '0' ? 'selected' : '' }}>Pendente</option>
                                    <option value="1" {{ request('is_processed') === '1' ? 'selected' : '' }}>Processada</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Data Inicial</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Data Final</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de transações -->
            @if($transactions->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Banco</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-university text-white small"></i>
                                                    </div>
                                                    {{ $transaction->bankIntegration->bank_name }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $transaction->description }}
                                                    @if($transaction->counterpart_name)
                                                        <br><small class="text-muted">{{ $transaction->counterpart_name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($transaction->category)
                                                    <span class="badge bg-secondary">{{ $transaction->category->name }}</span>
                                                @else
                                                    <span class="text-muted">Sem categoria</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}
                                                    R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->is_processed)
                                                    <span class="badge bg-success">Processada</span>
                                                    @if($transaction->transaction)
                                                        <br><small class="text-muted">
                                                            <a href="{{ route('transactions.show', $transaction->transaction) }}">
                                                                Ver transação
                                                            </a>
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning">Pendente</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$transaction->is_processed)
                                                    <div class="btn-group" role="group">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-success"
                                                                onclick="processTransaction({{ $transaction->id }}, 'create')">
                                                            <i class="fas fa-plus me-1"></i>Criar
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-secondary"
                                                                onclick="processTransaction({{ $transaction->id }}, 'ignore')">
                                                            <i class="fas fa-eye-slash me-1"></i>Ignorar
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-exchange-alt fa-3x text-muted"></i>
                        </div>
                        <h4 class="mb-3">Nenhuma transação encontrada</h4>
                        <p class="text-muted mb-4">
                            Não há transações sincronizadas com os filtros aplicados.
                        </p>
                        <a href="{{ route('bank-integrations.index') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-2"></i>Sincronizar Transações
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para processar transação -->
<div class="modal fade" id="processModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processModalTitle">Processar Transação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="processForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="createOptions" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select name="category_id" class="form-select">
                                <option value="">Selecione uma categoria (opcional)</option>
                                @foreach(auth()->user()->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-muted small">
                            Esta transação será adicionada ao seu sistema de finanças pessoais.
                        </p>
                    </div>
                    <div id="ignoreOptions" style="display: none;">
                        <p>Tem certeza que deseja ignorar esta transação?</p>
                        <p class="text-muted small">
                            A transação será marcada como processada, mas não será adicionada ao sistema.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="processSubmitBtn">Processar</button>
                    <input type="hidden" name="action" id="processAction">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function processTransaction(transactionId, action) {
    const modal = new bootstrap.Modal(document.getElementById('processModal'));
    const form = document.getElementById('processForm');
    const title = document.getElementById('processModalTitle');
    const submitBtn = document.getElementById('processSubmitBtn');
    const actionInput = document.getElementById('processAction');
    const createOptions = document.getElementById('createOptions');
    const ignoreOptions = document.getElementById('ignoreOptions');
    
    form.action = `/bank-integrations/transactions/${transactionId}/process`;
    actionInput.value = action;
    
    if (action === 'create') {
        title.textContent = 'Criar Transação';
        submitBtn.textContent = 'Criar Transação';
        submitBtn.className = 'btn btn-success';
        createOptions.style.display = 'block';
        ignoreOptions.style.display = 'none';
    } else {
        title.textContent = 'Ignorar Transação';
        submitBtn.textContent = 'Ignorar';
        submitBtn.className = 'btn btn-secondary';
        createOptions.style.display = 'none';
        ignoreOptions.style.display = 'block';
    }
    
    modal.show();
}
</script>
@endpush