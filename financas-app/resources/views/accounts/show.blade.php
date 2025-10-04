@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Header da Conta -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($account->icon)
                            <i class="{{ $account->icon }} fa-2x me-3" style="color: {{ $account->color }}"></i>
                        @endif
                        <div>
                            <h4 class="mb-0">{{ $account->name }}</h4>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Saldo Atual</h6>
                                <h2 class="{{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $account->formatted_balance }}
                                </h2>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h6 class="text-muted">Moeda</h6>
                                <p class="mb-0">{{ $account->currency }}</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h6 class="text-muted">Status</h6>
                                @if($account->is_active)
                                    <span class="badge bg-success">Ativa</span>
                                @else
                                    <span class="badge bg-secondary">Inativa</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-5">
                            @if($account->description)
                                <h6 class="text-muted">Descrição</h6>
                                <p class="mb-0">{{ $account->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>Receitas do Mês</h5>
                            <h3>R$ 0,00</h3>
                            <small>0 transações</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h5>Despesas do Mês</h5>
                            <h3>R$ 0,00</h3>
                            <small>0 transações</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>Total de Transações</h5>
                            <h3>{{ $recentTransactions->count() }}</h3>
                            <small>últimas 10</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5>Última Movimentação</h5>
                            <h3>
                                @if($recentTransactions->first())
                                    {{ $recentTransactions->first()->transaction_date->format('d/m') }}
                                @else
                                    ---
                                @endif
                            </h3>
                            <small>
                                @if($recentTransactions->first())
                                    {{ $recentTransactions->first()->transaction_date->diffForHumans() }}
                                @else
                                    Nenhuma transação
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transações Recentes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transações Recentes</h5>
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Nova Transação
                    </a>
                </div>

                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Tipo</th>
                                        <th class="text-end">Valor</th>
                                        <th>Status</th>
                                        <th width="80">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                            <td>
                                                <strong>{{ $transaction->description }}</strong>
                                                @if($transaction->notes)
                                                    <br><small class="text-muted">{{ Str::limit($transaction->notes, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $transaction->category->color }}">
                                                    {{ $transaction->category->name }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->type == 'income')
                                                    <span class="badge bg-success">Receita</span>
                                                @elseif($transaction->type == 'expense')
                                                    <span class="badge bg-danger">Despesa</span>
                                                @else
                                                    <span class="badge bg-info">Transferência</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="{{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                                    @if($transaction->type == 'income')
                                                        +{{ $transaction->formatted_amount }}
                                                    @else
                                                        -{{ $transaction->formatted_amount }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->status == 'completed')
                                                    <span class="badge bg-success">Concluída</span>
                                                @elseif($transaction->status == 'pending')
                                                    <span class="badge bg-warning">Pendente</span>
                                                @else
                                                    <span class="badge bg-secondary">Cancelada</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#">
                                                            <i class="fas fa-trash"></i> Excluir
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-outline-primary">
                                Ver Todas as Transações
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma transação encontrada</h5>
                            <p class="text-muted">Comece adicionando algumas transações para esta conta.</p>
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Adicionar Primeira Transação
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
@endpush