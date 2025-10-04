@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1920px;">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-clock"></i> Próximos Pagamentos
                </h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('payment-schedules.overdue') }}" class="btn btn-outline-danger">
                        <i class="fas fa-exclamation-triangle"></i> Em Atraso
                    </a>
                    <a href="{{ route('payment-plans.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt"></i> Meus Planos
                    </a>
                </div>
            </div>

            <!-- Resumo da Semana -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar-week fa-2x text-primary mb-2"></i>
                            <h5 class="card-title">{{ $weekPayments->count() }}</h5>
                            <p class="card-text small text-muted">Próximos 7 dias</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                            <h5 class="card-title">R$ {{ number_format($totalUpcoming, 2, ',', '.') }}</h5>
                            <p class="card-text small text-muted">Total da Semana</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar-day fa-2x text-warning mb-2"></i>
                            <h5 class="card-title">{{ $upcomingPayments->count() }}</h5>
                            <p class="card-text small text-muted">Próximos {{ $days }} dias</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                            <h5 class="card-title">R$ {{ number_format($upcomingPayments->flatten()->sum('payment_amount'), 2, ',', '.') }}</h5>
                            <p class="card-text small text-muted">Total {{ $days }} dias</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtro de Dias -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('payment-schedules.upcoming') }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="days" class="form-label">Período de Visualização</label>
                                <select class="form-select" id="days" name="days" onchange="this.form.submit()">
                                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Próximos 7 dias</option>
                                    <option value="15" {{ $days == 15 ? 'selected' : '' }}>Próximos 15 dias</option>
                                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Próximos 30 dias</option>
                                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>Próximos 60 dias</option>
                                </select>
                            </div>
                            <div class="col-md-9 text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#bulkPaymentModal">
                                        <i class="fas fa-check-double"></i> Marcar Múltiplos como Pagos
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="window.print()">
                                        <i class="fas fa-print"></i> Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($upcomingPayments->count() > 0)
                <!-- Pagamentos por Data -->
                @foreach($upcomingPayments as $date => $payments)
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar"></i> 
                                {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} 
                                <span class="badge bg-secondary">{{ $payments->count() }} pagamento(s)</span>
                                @if(\Carbon\Carbon::parse($date)->isToday())
                                    <span class="badge bg-warning">Hoje</span>
                                @elseif(\Carbon\Carbon::parse($date)->isTomorrow())
                                    <span class="badge bg-info">Amanhã</span>
                                @endif
                            </h6>
                            <div class="text-muted">
                                Total: <strong>R$ {{ number_format($payments->sum('payment_amount'), 2, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="date-select-all" data-date="{{ $date }}">
                                        </th>
                                        <th>Dívida</th>
                                        <th>Plano</th>
                                        <th>Valor</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th width="200">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="payment-checkbox" 
                                                   value="{{ $payment->id }}" data-date="{{ $date }}">
                                        </td>
                                        <td>
                                            <strong>{{ $payment->debt->name }}</strong>
                                            @if($payment->debt->creditor)
                                                <br><small class="text-muted">{{ $payment->debt->creditor }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $payment->paymentPlan->name }}</span>
                                            <br><small class="text-muted">
                                                @switch($payment->paymentPlan->strategy)
                                                    @case('snowball')
                                                        Bola de Neve
                                                        @break
                                                    @case('avalanche')
                                                        Avalanche
                                                        @break
                                                    @case('custom')
                                                        Personalizada
                                                        @break
                                                @endswitch
                                            </small>
                                        </td>
                                        <td>
                                            <strong class="text-primary">R$ {{ number_format($payment->payment_amount, 2, ',', '.') }}</strong>
                                            @if($payment->extra_payment > 0)
                                                <br><small class="text-success">+ R$ {{ number_format($payment->extra_payment, 2, ',', '.') }} extra</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->is_final_payment)
                                                <span class="badge bg-success">Quitação</span>
                                            @elseif($payment->extra_payment > 0)
                                                <span class="badge bg-info">Extra</span>
                                            @else
                                                <span class="badge bg-secondary">Regular</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status_class }}">
                                                {{ $payment->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if($payment->status == 'pending')
                                                    <button type="button" class="btn btn-outline-success" 
                                                            onclick="markAsPaid({{ $payment->id }}, '{{ $payment->payment_amount }}')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning" 
                                                            onclick="reschedulePayment({{ $payment->id }})">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            onclick="skipPayment({{ $payment->id }})">
                                                        <i class="fas fa-step-forward"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <h5>Nenhum pagamento programado</h5>
                        <p class="text-muted">Você não possui pagamentos programados para os próximos {{ $days }} dias.</p>
                        <a href="{{ route('payment-plans.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Criar Plano de Pagamento
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Pagamento Individual -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Valor do Pagamento</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="payment_amount" name="amount" 
                                   step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Data do Pagamento</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="3" 
                                  placeholder="Observações sobre o pagamento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirmar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Reagendamento -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reagendar Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rescheduleForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_due_date" class="form-label">Nova Data de Vencimento</label>
                        <input type="date" class="form-control" id="new_due_date" name="new_due_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="reschedule_reason" class="form-label">Motivo do Reagendamento</label>
                        <textarea class="form-control" id="reschedule_reason" name="reason" rows="3" 
                                  placeholder="Explique o motivo do reagendamento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-calendar-alt"></i> Reagendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Pagamento em Lote -->
<div class="modal fade" id="bulkPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Marcar Múltiplos Pagamentos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('payment-schedules.bulk-mark-paid') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_payment_date" class="form-label">Data dos Pagamentos</label>
                        <input type="date" class="form-control" id="bulk_payment_date" name="payment_date" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="bulk_notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="bulk_notes" name="notes" rows="3" 
                                  placeholder="Observações gerais sobre os pagamentos..."></textarea>
                    </div>
                    <div id="selectedPaymentsInfo"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="bulkConfirmBtn" disabled>
                        <i class="fas fa-check-double"></i> Confirmar Pagamentos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function markAsPaid(scheduleId, amount) {
    document.getElementById('payment_amount').value = amount;
    document.getElementById('paymentForm').action = `/payment-schedules/${scheduleId}/record-payment`;
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function reschedulePayment(scheduleId) {
    document.getElementById('rescheduleForm').action = `/payment-schedules/${scheduleId}/reschedule`;
    
    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    modal.show();
}

function skipPayment(scheduleId) {
    if (confirm('Tem certeza que deseja pular este pagamento?')) {
        fetch(`/payment-schedules/${scheduleId}/skip`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

// Checkboxes functionality
document.addEventListener('DOMContentLoaded', function() {
    // Select all by date
    document.querySelectorAll('.date-select-all').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const date = this.dataset.date;
            const paymentCheckboxes = document.querySelectorAll(`.payment-checkbox[data-date="${date}"]`);
            paymentCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkPaymentInfo();
        });
    });
    
    // Individual checkboxes
    document.querySelectorAll('.payment-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkPaymentInfo);
    });
    
    function updateBulkPaymentInfo() {
        const selected = document.querySelectorAll('.payment-checkbox:checked');
        const bulkBtn = document.getElementById('bulkConfirmBtn');
        const infoDiv = document.getElementById('selectedPaymentsInfo');
        
        if (selected.length > 0) {
            bulkBtn.disabled = false;
            infoDiv.innerHTML = `<div class="alert alert-info">
                <strong>${selected.length}</strong> pagamento(s) selecionado(s) para marcação.
            </div>`;
            
            // Add hidden inputs for selected payments
            const form = bulkBtn.closest('form');
            // Remove existing hidden inputs
            form.querySelectorAll('input[name="schedule_ids[]"]').forEach(input => input.remove());
            
            // Add new hidden inputs
            selected.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'schedule_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
        } else {
            bulkBtn.disabled = true;
            infoDiv.innerHTML = '<div class="alert alert-warning">Nenhum pagamento selecionado.</div>';
        }
    }
});
</script>

<style>
@media print {
    .btn, .modal, .card-header .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        page-break-inside: avoid;
    }
}
</style>
@endsection