<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncedTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_integration_id',
        'bank_transaction_id',
        'account_id',
        'account_number',
        'account_type',
        'amount',
        'type',
        'description',
        'full_description',
        'transaction_date',
        'processed_at',
        'bank_category',
        'mcc_code',
        'category_id',
        'counterpart_name',
        'counterpart_document',
        'counterpart_account',
        'is_processed',
        'transaction_id',
        'raw_data',
        'processing_notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'processed_at' => 'datetime',
        'is_processed' => 'boolean',
        'raw_data' => 'array'
    ];

    /**
     * Relacionamento com o usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com a integração bancária
     */
    public function bankIntegration(): BelongsTo
    {
        return $this->belongsTo(BankIntegration::class);
    }

    /**
     * Relacionamento com a categoria
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relacionamento com a transação processada
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Verifica se a transação já foi processada
     */
    public function isProcessed(): bool
    {
        return $this->is_processed && $this->transaction_id !== null;
    }

    /**
     * Marca como processada e vincula à transação
     */
    public function markAsProcessed(?Transaction $transaction = null, string $notes = null): void
    {
        $this->update([
            'is_processed' => true,
            'transaction_id' => $transaction?->id,
            'processing_notes' => $notes
        ]);
    }

    /**
     * Converte para transação do sistema
     */
    public function toTransaction(): array
    {
        return [
            'user_id' => $this->user_id,
            'account_id' => $this->getSystemAccountId(),
            'category_id' => $this->category_id,
            'amount' => $this->type === 'debit' ? -abs($this->amount) : abs($this->amount),
            'description' => $this->description,
            'notes' => $this->full_description,
            'transaction_date' => $this->transaction_date,
            'type' => $this->type === 'debit' ? 'expense' : 'income',
            'is_recurring' => false,
            'bank_sync_id' => $this->id
        ];
    }

    /**
     * Obtém ID da conta no sistema (precisa ser mapeado)
     */
    private function getSystemAccountId(): ?int
    {
        // Busca conta do usuário que corresponde à conta bancária
        $account = Account::where('user_id', $this->user_id)
            ->where('bank_code', $this->bankIntegration->bank_code)
            ->where('account_number', $this->account_number)
            ->first();

        return $account?->id;
    }

    /**
     * Sugere categoria baseada na descrição e MCC
     */
    public function suggestCategory(): ?Category
    {
        // Busca por MCC primeiro
        if ($this->mcc_code) {
            $category = Category::where('user_id', $this->user_id)
                ->where('mcc_codes', 'like', '%' . $this->mcc_code . '%')
                ->first();
            
            if ($category) {
                return $category;
            }
        }

        // Busca por palavras-chave na descrição
        $keywords = [
            'supermercado' => 'Alimentação',
            'posto' => 'Combustível',
            'farmacia' => 'Saúde',
            'restaurante' => 'Alimentação',
            'uber' => 'Transporte',
            'netflix' => 'Entretenimento',
            'salario' => 'Salário',
        ];

        $description = strtolower($this->description);
        
        foreach ($keywords as $keyword => $categoryName) {
            if (str_contains($description, $keyword)) {
                $category = Category::where('user_id', $this->user_id)
                    ->where('name', 'like', '%' . $categoryName . '%')
                    ->first();
                
                if ($category) {
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * Scope para transações não processadas
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Scope para transações processadas
     */
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    /**
     * Scope por período
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope por tipo
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}