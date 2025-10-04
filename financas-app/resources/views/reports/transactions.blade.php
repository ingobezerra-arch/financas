@extends('layouts.app')

@section('title', 'Relatório de Transações')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-exchange-alt text-primary"></i>
                        Relatório de Transações
                    </h1>
                    <p class="text-muted">Análise detalhada das suas transações financeiras</p>
                </div>
                <div>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar aos Relatórios
                    </a>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.transactions') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="period" class="form-label">Período</label>
                                <select name="period" id="period" class="form-select">
                                    <option value="current_month" {{ $period === 'current_month' ? 'selected' : '' }}>Mês Atual</option>
                                    <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Mês Passado</option>
                                    <option value="current_year" {{ $period === 'current_year' ? 'selected' : '' }}>Ano Atual</option>
                                    <option value="last_year" {{ $period === 'last_year' ? 'selected' : '' }}>Ano Passado</option>
                                    <option value="last_7_days" {{ $period === 'last_7_days' ? 'selected' : '' }}>Últimos 7 dias</option>
                                    <option value="last_30_days" {{ $period === 'last_30_days' ? 'selected' : '' }}>Últimos 30 dias</option>
                                    <option value="last_90_days" {{ $period === 'last_90_days' ? 'selected' : '' }}>Últimos 90 dias</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label">Tipo</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Receitas</option>
                                    <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Despesas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="category_id" class="form-label">Categoria</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">Todas as categorias</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="account_id" class="form-label">Conta</label>
                                <select name="account_id" id="account_id" class="form-select">
                                    <option value="">Todas as contas</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Resumo da Pesquisa -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up fa-2x mb-2"></i>
                            <h6 class="mb-1">Total de Receitas</h6>
                            <h4 class="mb-0">R$ {{ number_format($searchSummary['total_income'], 2, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down fa-2x mb-2"></i>
                            <h6 class="mb-1">Total de Despesas</h6>
                            <h4 class="mb-0">R$ {{ number_format($searchSummary['total_expense'], 2, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-balance-scale fa-2x mb-2"></i>
                            <h6 class="mb-1">Saldo Líquido</h6>
                            <h4 class="mb-0">R$ {{ number_format($searchSummary['total_income'] - $searchSummary['total_expense'], 2, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-list fa-2x mb-2"></i>
                            <h6 class="mb-1">Total de Transações</h6>
                            <h4 class="mb-0">{{ $searchSummary['count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Transações -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> 
                        Transações no período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th>Categoria</th>
                                        <th>Conta</th>
                                        <th>Tipo</th>
                                        <th class="text-end">Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <strong>{{ $transaction->transaction_date->format('d/m/Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $transaction->transaction_date->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $transaction->description }}</strong>
                                                @if($transaction->notes)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($transaction->notes, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->category)
                                                    <span class="badge rounded-pill" style="background-color: {{ $transaction->category->color }}; color: white;">
                                                        <i class="{{ $transaction->category->icon }}"></i>
                                                        {{ $transaction->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sem categoria</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->account)
                                                    <span class="badge bg-secondary">
                                                        <i class="{{ $transaction->account->icon }}"></i>
                                                        {{ $transaction->account->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sem conta</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type === 'income')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-arrow-up"></i> Receita
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-arrow-down"></i> Despesa
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'income' ? '+' : '-' }}R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                                </strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pendente'],
                                                        'completed' => ['class' => 'success', 'icon' => 'check', 'text' => 'Concluída'],
                                                        'cancelled' => ['class' => 'danger', 'icon' => 'times', 'text' => 'Cancelada'],
                                                    ];
                                                    $status = $statusConfig[$transaction->status] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => 'Indefinido'];
                                                @endphp
                                                <span class="badge bg-{{ $status['class'] }}">
                                                    <i class="fas fa-{{ $status['icon'] }}"></i>
                                                    {{ $status['text'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma transação encontrada</h5>
                            <p class="text-muted">Tente ajustar os filtros para encontrar transações.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit do formulário quando filtros mudarem
    const filters = document.querySelectorAll('#period, #type, #category_id, #account_id');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush