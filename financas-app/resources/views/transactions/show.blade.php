@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Detalhes da Transação') }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Valor</h6>
                            <h2 class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                            </h2>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Data</h6>
                            <p class="h5">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                            <small class="text-muted">{{ $transaction->transaction_date->diffForHumans() }}</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted">Descrição</h6>
                            <p class="h5">{{ $transaction->description }}</p>
                        </div>
                    </div>

                    @if($transaction->notes)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="text-muted">Observações</h6>
                                <p>{{ $transaction->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted">Conta</h6>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <div class="account-icon" style="background-color: {{ $transaction->account->color }}20; border: 2px solid {{ $transaction->account->color }};">
                                        @if($transaction->account->icon)
                                            <i class="{{ $transaction->account->icon }}" style="color: {{ $transaction->account->color }};"></i>
                                        @else
                                            <i class="fas fa-wallet" style="color: {{ $transaction->account->color }};"></i>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0">{{ $transaction->account->name }}</p>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $transaction->account->type)) }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <h6 class="text-muted">Categoria</h6>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <div class="category-icon" style="background-color: {{ $transaction->category->color }}20; border: 2px solid {{ $transaction->category->color }};">
                                        @if($transaction->category->icon)
                                            <i class="{{ $transaction->category->icon }}" style="color: {{ $transaction->category->color }};"></i>
                                        @else
                                            <i class="fas fa-tag" style="color: {{ $transaction->category->color }};"></i>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0">{{ $transaction->category->name }}</p>
                                    <small class="text-muted">{{ $transaction->category->type === 'income' ? 'Receita' : 'Despesa' }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <h6 class="text-muted">Status</h6>
                            @if($transaction->status === 'completed')
                                <span class="badge bg-success fs-6">Concluída</span>
                            @elseif($transaction->status === 'pending')
                                <span class="badge bg-warning fs-6">Pendente</span>
                            @else
                                <span class="badge bg-secondary fs-6">Cancelada</span>
                            @endif
                        </div>
                    </div>

                    @if($transaction->tags && count($transaction->tags) > 0)
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted">Tags</h6>
                                @foreach($transaction->tags as $tag)
                                    <span class="badge bg-light text-dark me-1">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($transaction->receipt_url)
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted">Comprovante</h6>
                                <a href="{{ $transaction->receipt_url }}" target="_blank" class="btn btn-outline-info">
                                    <i class="fas fa-external-link-alt"></i> Ver Comprovante
                                </a>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Criado em</h6>
                            <p>{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($transaction->updated_at != $transaction->created_at)
                            <div class="col-md-6">
                                <h6 class="text-muted">Última atualização</h6>
                                <p>{{ $transaction->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta transação?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Excluir Transação
                            </button>
                        </form>
                        
                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Transação
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.account-icon, .category-icon {
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
@endpush