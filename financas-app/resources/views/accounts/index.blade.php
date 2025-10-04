@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Minhas Contas') }}</h4>
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Conta
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($accounts->count() > 0)
                        <div class="row">
                            @foreach($accounts as $account)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-start border-5" style="border-color: {{ $account->color }} !important;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="card-title mb-1">{{ $account->name }}</h5>
                                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</small>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('accounts.show', $account) }}">
                                                            <i class="fas fa-eye"></i> Visualizar
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="{{ route('accounts.edit', $account) }}">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('accounts.toggle-status', $account) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="dropdown-item">
                                                                    @if($account->is_active)
                                                                        <i class="fas fa-pause"></i> Desativar
                                                                    @else
                                                                        <i class="fas fa-play"></i> Ativar
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash"></i> Excluir
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <h3 class="mb-0 {{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ $account->formatted_balance }}
                                                </h3>
                                            </div>

                                            @if($account->description)
                                                <p class="card-text text-muted small">{{ $account->description }}</p>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    @if($account->is_active)
                                                        <span class="badge bg-success">Ativa</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inativa</span>
                                                    @endif
                                                </small>
                                                @if($account->icon)
                                                    <i class="{{ $account->icon }} text-muted"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5>Total Geral</h5>
                                            <h3 class="text-primary">
                                                R$ {{ number_format($accounts->sum('balance'), 2, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5>Contas Ativas</h5>
                                            <h3 class="text-success">{{ $accounts->where('is_active', true)->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5>Total de Contas</h5>
                                            <h3 class="text-info">{{ $accounts->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-wallet fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma conta cadastrada</h5>
                            <p class="text-muted">Comece criando sua primeira conta financeira.</p>
                            <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Primeira Conta
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