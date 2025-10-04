@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-redo-alt me-2"></i>{{ $recurringTransaction->description }}</h1>
                <div>
                    <a href="{{ route('recurring-transactions.edit', $recurringTransaction) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="{{ route('recurring-transactions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações Principais -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informações da Transação Recorrente</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Tipo:</strong> 
                                        <span class="badge {{ $recurringTransaction->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $recurringTransaction->type === 'income' ? 'Receita' : 'Despesa' }}
                                        </span>
                                    </p>
                                    <p><strong>Valor:</strong> 
                                        <span class="h5 {{ $recurringTransaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            R$ {{ number_format($recurringTransaction->amount, 2, ',', '.') }}
                                        </span>
                                    </p>
                                    <p><strong>Conta:</strong> {{ $recurringTransaction->account->name }}</p>
                                    <p><strong>Categoria:</strong> {{ $recurringTransaction->category->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge {{ $recurringTransaction->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $recurringTransaction->is_active ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </p>
                                    <p><strong>Frequência:</strong> 
                                        @switch($recurringTransaction->frequency)
                                            @case('daily') Diário @break
                                            @case('weekly') Semanal @break
                                            @case('monthly') Mensal @break
                                            @case('yearly') Anual @break
                                        @endswitch
                                        (a cada {{ $recurringTransaction->interval }} 
                                        @switch($recurringTransaction->frequency)
                                            @case('daily') {{ $recurringTransaction->interval > 1 ? 'dias' : 'dia' }} @break
                                            @case('weekly') {{ $recurringTransaction->interval > 1 ? 'semanas' : 'semana' }} @break
                                            @case('monthly') {{ $recurringTransaction->interval > 1 ? 'meses' : 'mês' }} @break
                                            @case('yearly') {{ $recurringTransaction->interval > 1 ? 'anos' : 'ano' }} @break
                                        @endswitch)
                                    </p>
                                    <p><strong>Próxima Execução:</strong> {{ $recurringTransaction->next_due_date->format('d/m/Y') }}</p>
                                    <p><strong>Execuções:</strong> {{ $recurringTransaction->occurrences_count }}
                                        @if($recurringTransaction->occurrences)
                                            / {{ $recurringTransaction->occurrences }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($recurringTransaction->notes)
                            <div class="mt-3">
                                <strong>Observações:</strong>
                                <p class="text-muted">{{ $recurringTransaction->notes }}</p>
                            </div>
                            @endif

                            @if($recurringTransaction->tags)
                            <div class="mt-3">
                                <strong>Tags:</strong>
                                @foreach($recurringTransaction->tags as $tag)
                                    <span class="badge bg-light text-dark me-1">#{{ $tag }}</span>
                                @endforeach
                            </div>
                            @endif

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <p><strong>Data de Início:</strong> {{ $recurringTransaction->start_date->format('d/m/Y') }}</p>
                                    @if($recurringTransaction->end_date)
                                    <p><strong>Data de Fim:</strong> {{ $recurringTransaction->end_date->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Criada em:</strong> {{ $recurringTransaction->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Última Atualização:</strong> {{ $recurringTransaction->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Próximas Execuções -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Próximas 5 Execuções</h6>
                        </div>
                        <div class="card-body">
                            @if(count($nextExecutions) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Dia da Semana</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nextExecutions as $index => $execution)
                                        <tr class="{{ $index === 0 ? 'table-primary' : '' }}">
                                            <td>{{ $execution->format('d/m/Y') }}</td>
                                            <td>
                                                @switch($execution->dayOfWeek)
                                                    @case(0) Domingo @break
                                                    @case(1) Segunda-feira @break
                                                    @case(2) Terça-feira @break
                                                    @case(3) Quarta-feira @break
                                                    @case(4) Quinta-feira @break
                                                    @case(5) Sexta-feira @break
                                                    @case(6) Sábado @break
                                                @endswitch
                                            </td>
                                            <td class="{{ $recurringTransaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                R$ {{ number_format($recurringTransaction->amount, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted mb-0">Nenhuma execução futura programada.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Histórico de Transações -->
                    @if($recurringTransaction->transactions && $recurringTransaction->transactions->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Histórico de Transações ({{ $recurringTransaction->transactions->count() }})</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recurringTransaction->transactions->sortByDesc('transaction_date') as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                            <td class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ ucfirst($transaction->status) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('transactions.show', $transaction) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Ações -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Ações</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($recurringTransaction->is_active && $recurringTransaction->next_due_date <= now())
                                <form action="{{ route('recurring-transactions.execute', $recurringTransaction) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-play me-1"></i>Executar Agora
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('recurring-transactions.toggle', $recurringTransaction) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        @if($recurringTransaction->is_active)
                                            <i class="fas fa-pause me-1"></i>Desativar
                                        @else
                                            <i class="fas fa-play me-1"></i>Ativar
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('recurring-transactions.destroy', $recurringTransaction) }}" method="POST" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta transação recorrente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-trash me-1"></i>Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo Financeiro -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Impacto Financeiro</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $monthlyImpact = 0;
                                $yearlyImpact = 0;
                                
                                switch($recurringTransaction->frequency) {
                                    case 'daily':
                                        $monthlyImpact = ($recurringTransaction->amount * 30) / $recurringTransaction->interval;
                                        $yearlyImpact = ($recurringTransaction->amount * 365) / $recurringTransaction->interval;
                                        break;
                                    case 'weekly':
                                        $monthlyImpact = ($recurringTransaction->amount * 4.33) / $recurringTransaction->interval;
                                        $yearlyImpact = ($recurringTransaction->amount * 52) / $recurringTransaction->interval;
                                        break;
                                    case 'monthly':
                                        $monthlyImpact = $recurringTransaction->amount / $recurringTransaction->interval;
                                        $yearlyImpact = ($recurringTransaction->amount * 12) / $recurringTransaction->interval;
                                        break;
                                    case 'yearly':
                                        $monthlyImpact = ($recurringTransaction->amount / 12) / $recurringTransaction->interval;
                                        $yearlyImpact = $recurringTransaction->amount / $recurringTransaction->interval;
                                        break;
                                }
                                
                                if ($recurringTransaction->type === 'expense') {
                                    $monthlyImpact = -$monthlyImpact;
                                    $yearlyImpact = -$yearlyImpact;
                                }
                            @endphp

                            <p class="mb-2">
                                <strong>Impacto Mensal:</strong><br>
                                <span class="{{ $monthlyImpact >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format(abs($monthlyImpact), 2, ',', '.') }}
                                </span>
                            </p>
                            <p class="mb-2">
                                <strong>Impacto Anual:</strong><br>
                                <span class="{{ $yearlyImpact >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format(abs($yearlyImpact), 2, ',', '.') }}
                                </span>
                            </p>
                            <p class="mb-0">
                                <strong>Total Executado:</strong><br>
                                <span class="{{ $recurringTransaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($recurringTransaction->amount * $recurringTransaction->occurrences_count, 2, ',', '.') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection