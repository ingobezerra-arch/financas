<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'original_amount',
        'current_balance',
        'interest_rate',
        'minimum_payment',
        'due_date',
        'status',
        'debt_type',
        'creditor',
        'installments_total',
        'installments_paid',
        'contract_date',
        'additional_info',
        'is_active'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'minimum_payment' => 'decimal:2',
        'due_date' => 'date',
        'contract_date' => 'date',
        'additional_info' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com planos de pagamento
     */
    public function paymentPlans(): BelongsToMany
    {
        return $this->belongsToMany(PaymentPlan::class, 'debt_payment_plan')
            ->withPivot([
                'initial_balance',
                'priority_order',
                'allocated_extra_payment',
                'added_date',
                'projected_payoff_date',
                'actual_payoff_date',
                'status'
            ])
            ->withTimestamps();
    }

    /**
     * Relacionamento com cronogramas de pagamento
     */
    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    /**
     * Scope para dívidas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope para dívidas em atraso
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', '!=', 'paid');
    }

    /**
     * Calcula o percentual pago da dívida
     */
    public function getPercentagePaidAttribute(): float
    {
        if ($this->original_amount <= 0) {
            return 0;
        }
        
        $paidAmount = $this->original_amount - $this->current_balance;
        return round(($paidAmount / $this->original_amount) * 100, 2);
    }

    /**
     * Verifica se a dívida está em atraso
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'paid';
    }

    /**
     * Calcula juros mensal baseado no saldo atual
     */
    public function calculateMonthlyInterest(): float
    {
        return $this->current_balance * ($this->interest_rate / 100);
    }

    /**
     * Calcula quantos meses restam se pagar apenas o mínimo
     */
    public function calculateMonthsToPayoff(): int
    {
        if ($this->minimum_payment <= 0 || $this->interest_rate <= 0) {
            return 0;
        }

        $balance = $this->current_balance;
        $monthlyRate = $this->interest_rate / 100;
        $payment = $this->minimum_payment;

        if ($payment <= ($balance * $monthlyRate)) {
            return 999; // Nunca será pago apenas com o mínimo
        }

        $months = ceil(log(1 + ($balance * $monthlyRate) / $payment) / log(1 + $monthlyRate));
        return max(0, $months);
    }

    /**
     * Simula pagamento e retorna novo saldo
     */
    public function simulatePayment(float $paymentAmount): array
    {
        $interestAmount = $this->calculateMonthlyInterest();
        $principalAmount = max(0, $paymentAmount - $interestAmount);
        $newBalance = max(0, $this->current_balance - $principalAmount);

        return [
            'payment_amount' => $paymentAmount,
            'interest_amount' => $interestAmount,
            'principal_amount' => $principalAmount,
            'new_balance' => $newBalance,
            'is_paid_off' => $newBalance <= 0.01
        ];
    }
}
