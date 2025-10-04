<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'amount',
        'description',
        'notes',
        'frequency',
        'interval',
        'start_date',
        'end_date',
        'next_due_date',
        'occurrences',
        'occurrences_count',
        'is_active',
        'tags'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
        'is_active' => 'boolean',
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('next_due_date', '<=', Carbon::now()->toDateString());
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getFrequencyTextAttribute(): string
    {
        $frequencies = [
            'daily' => 'Diário',
            'weekly' => 'Semanal',
            'monthly' => 'Mensal',
            'yearly' => 'Anual',
        ];

        return $frequencies[$this->frequency] ?? $this->frequency;
    }

    public function calculateNextDueDate(): Carbon
    {
        $date = Carbon::parse($this->next_due_date);

        switch ($this->frequency) {
            case 'daily':
                return $date->addDays($this->interval);
            case 'weekly':
                return $date->addWeeks($this->interval);
            case 'monthly':
                return $date->addMonths($this->interval);
            case 'yearly':
                return $date->addYears($this->interval);
            default:
                return $date;
        }
    }

    public function isExpired(): bool
    {
        if ($this->end_date && Carbon::now()->gt($this->end_date)) {
            return true;
        }

        if ($this->occurrences && $this->occurrences_count >= $this->occurrences) {
            return true;
        }

        return false;
    }

    public function shouldExecute(): bool
    {
        return $this->is_active && 
               !$this->isExpired() && 
               Carbon::now()->gte($this->next_due_date);
    }
}
