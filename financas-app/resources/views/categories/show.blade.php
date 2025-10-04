@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Header da Categoria -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="category-icon" style="background-color: {{ $category->color }}20; border: 2px solid {{ $category->color }};">
                                @if($category->icon)
                                    <i class="{{ $category->icon }}" style="color: {{ $category->color }};"></i>
                                @else
                                    <i class="fas fa-tag" style="color: {{ $category->color }};"></i>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $category->name }}</h4>
                            <small class="text-muted">
                                @if($category->type === 'income')
                                    <i class="fas fa-arrow-up text-success"></i> Categoria de Receita
                                @else
                                    <i class="fas fa-arrow-down text-danger"></i> Categoria de Despesa
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted">Total do Mês</h6>
                                <h2 class="{{ $category->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($monthlyTotal, 2, ',', '.') }}
                                </h2>
                                <small class="text-muted">{{ $monthlyTransactions->count() }} transação(ões)</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h6 class="text-muted">Status</h6>
                                @if($category->is_active)
                                    <span class="badge bg-success fs-6">Ativa</span>
                                @else
                                    <span class="badge bg-secondary fs-6">Inativa</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <h6 class="text-muted">Total de Transações</h6>
                                <h4>{{ $recentTransactions->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-5">
                            @if($category->description)
                                <h6 class="text-muted">Descrição</h6>
                                <p class="mb-0">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transações da Categoria -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Transações Recentes
                    </h5>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Nova Transação
                    </button>
                </div>

                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th>Conta</th>
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
                                                <span class="badge" style="background-color: {{ $transaction->account->color }}">
                                                    {{ $transaction->account->name }}
                                                </span>
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
                            <button class="btn btn-outline-primary">
                                Ver Todas as Transações desta Categoria
                            </button>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exchange-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma transação encontrada</h5>
                            <p class="text-muted">Esta categoria ainda não possui transações.</p>
                            <button class="btn btn-primary">
                                <i class="fas fa-plus"></i> Adicionar Primeira Transação
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-icon {
    height: 3rem;
    width: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
</style>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
@endpush