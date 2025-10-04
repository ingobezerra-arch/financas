@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('Minhas Categorias') }}</h4>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova Categoria
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

                    <div class="row">
                        <!-- Categorias de Receita -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-arrow-up me-2"></i>
                                        Categorias de Receita ({{ $incomeCategories->count() }})
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    @if($incomeCategories->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($incomeCategories as $category)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
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
                                                            <h6 class="mb-0">{{ $category->name }}</h6>
                                                            @if($category->description)
                                                                <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="{{ route('categories.show', $category) }}">
                                                                <i class="fas fa-eye"></i> Visualizar
                                                            </a></li>
                                                            <li><a class="dropdown-item" href="{{ route('categories.edit', $category) }}">
                                                                <i class="fas fa-edit"></i> Editar
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-pause"></i> Desativar
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
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
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhuma categoria de receita</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Categorias de Despesa -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-arrow-down me-2"></i>
                                        Categorias de Despesa ({{ $expenseCategories->count() }})
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    @if($expenseCategories->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($expenseCategories as $category)
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
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
                                                            <h6 class="mb-0">{{ $category->name }}</h6>
                                                            @if($category->description)
                                                                <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="{{ route('categories.show', $category) }}">
                                                                <i class="fas fa-eye"></i> Visualizar
                                                            </a></li>
                                                            <li><a class="dropdown-item" href="{{ route('categories.edit', $category) }}">
                                                                <i class="fas fa-edit"></i> Editar
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-pause"></i> Desativar
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
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
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-arrow-down fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhuma categoria de despesa</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categorias Inativas -->
                    @if($inactiveCategories->count() > 0)
                        <div class="card border-secondary mt-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-pause me-2"></i>
                                    Categorias Inativas ({{ $inactiveCategories->count() }})
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($inactiveCategories as $category)
                                        <div class="list-group-item d-flex justify-content-between align-items-center opacity-75">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="category-icon" style="background-color: #6c757d20; border: 2px solid #6c757d;">
                                                        @if($category->icon)
                                                            <i class="{{ $category->icon }}" style="color: #6c757d;"></i>
                                                        @else
                                                            <i class="fas fa-tag" style="color: #6c757d;"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $category->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $category->type === 'income' ? 'Receita' : 'Despesa' }}
                                                        @if($category->description)
                                                            • {{ Str::limit($category->description, 50) }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form action="{{ route('categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fas fa-play"></i> Ativar
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><a class="dropdown-item" href="{{ route('categories.edit', $category) }}">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
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
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Estatísticas Resumo -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Total de Categorias</h5>
                                    <h3 class="text-primary">{{ $incomeCategories->count() + $expenseCategories->count() + $inactiveCategories->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Receitas</h5>
                                    <h3 class="text-success">{{ $incomeCategories->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Despesas</h5>
                                    <h3 class="text-danger">{{ $expenseCategories->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Inativas</h5>
                                    <h3 class="text-secondary">{{ $inactiveCategories->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-icon {
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