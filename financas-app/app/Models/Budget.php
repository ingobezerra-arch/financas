<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'amount',
        'spent',
        'period',
        'start_date',
        'end_date',
        'description',
        'is_active',
        'alert_percentage'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'alert_percentage' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function getSpentAmountAttribute(): float
    {
        // Calcular valor gasto baseado nas transações da categoria no período
        return $this->user->transactions()
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->spent;
    }

    public function getPercentageUsedAttribute(): float
    {
        return $this->amount > 0 ? ($this->spent / $this->amount) * 100 : 0;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getFormattedSpentAttribute(): string
    {
        return 'R$ ' . number_format($this->spent, 2, ',', '.');
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->remaining_amount, 2, ',', '.');
    }

    public function isOverBudget(): bool
    {
        return $this->spent > $this->amount;
    }

    public function shouldAlert(): bool
    {
        return $this->percentage_used >= $this->alert_percentage;
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->isOverBudget();
    }

    public function getShouldAlertAttribute(): bool
    {
        return $this->shouldAlert();
    }
}
