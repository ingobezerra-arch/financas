<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_plan_id',
        'debt_id',
        'month',
        'due_date',
        'payment_amount',
        'minimum_payment',
        'extra_payment',
        'interest_amount',
        'principal_amount',
        'remaining_balance',
        'status',
        'paid_date',
        'paid_amount',
        'notes',
        'priority_order',
        'is_final_payment'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'payment_amount' => 'decimal:2',
        'minimum_payment' => 'decimal:2',
        'extra_payment' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'is_final_payment' => 'boolean'
    ];

    /**
     * Relacionamento com plano de pagamento
     */
    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    /**
     * Relacionamento com dívida
     */
    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }

    /**
     * Scope para pagamentos pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para pagamentos vencidos
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', 'pending');
    }

    /**
     * Scope para próximos pagamentos
     */
    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays($days))
            ->where('status', 'pending');
    }

    /**
     * Scope por mês
     */
    public function scopeForMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Verifica se o pagamento está em atraso
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() && $this->status === 'pending';
    }

    /**
     * Obtém a diferença entre valor pago e programado
     */
    public function getPaymentDifferenceAttribute(): float
    {
        if (!$this->paid_amount) {
            return 0;
        }
        
        return $this->paid_amount - $this->payment_amount;
    }

    /**
     * Verifica se foi pago integralmente
     */
    public function getIsPaidInFullAttribute(): bool
    {
        return $this->status === 'paid' && $this->paid_amount >= $this->payment_amount;
    }

    /**
     * Calcula dias até o vencimento
     */
    public function getDaysUntilDueAttribute(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Marca pagamento como realizado
     */
    public function markAsPaid(float $amount, Carbon $date = null, string $notes = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_amount' => $amount,
            'paid_date' => $date ?? now(),
            'notes' => $notes
        ]);
    }

    /**
     * Marca como atrasado
     */
    public function markAsOverdue(): void
    {
        if ($this->status === 'pending' && $this->is_overdue) {
            $this->update(['status' => 'overdue']);
        }
    }

    /**
     * Obtém status formatado
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'overdue' => 'Em Atraso',
            'skipped' => 'Pulado',
            default => 'Desconhecido'
        };
    }

    /**
     * Obtém classe CSS do status
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'skipped' => 'secondary',
            default => 'secondary'
        };
    }
}
