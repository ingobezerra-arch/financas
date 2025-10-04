<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'target_date',
        'start_date',
        'status',
        'color',
        'icon',
        'monthly_contribution'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
        'start_date' => 'date',
        'monthly_contribution' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getPercentageCompleteAttribute(): float
    {
        return $this->target_amount > 0 ? ($this->current_amount / $this->target_amount) * 100 : 0;
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->target_amount - $this->current_amount;
    }

    public function getDaysRemainingAttribute(): int
    {
        return Carbon::now()->diffInDays($this->target_date, false);
    }

    public function getMonthsRemainingAttribute(): int
    {
        return Carbon::now()->diffInMonths($this->target_date, false);
    }

    public function getFormattedTargetAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->target_amount, 2, ',', '.');
    }

    public function getFormattedCurrentAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->current_amount, 2, ',', '.');
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->remaining_amount, 2, ',', '.');
    }

    public function isCompleted(): bool
    {
        return $this->current_amount >= $this->target_amount;
    }

    public function isOverdue(): bool
    {
        return Carbon::now()->gt($this->target_date) && !$this->isCompleted();
    }

    public function calculateMonthlyContributionNeeded(): float
    {
        $monthsRemaining = max(1, $this->months_remaining);
        return $this->remaining_amount / $monthsRemaining;
    }
}
