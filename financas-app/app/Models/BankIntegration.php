<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_code',
        'bank_name',
        'institution_id',
        'consent_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'consent_expires_at',
        'status',
        'permissions',
        'accounts_data',
        'last_sync_at',
        'sync_frequency',
        'auto_sync',
        'error_count',
        'last_error',
        'error_message',
        'metadata',
        'configuration',
        'is_active'
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'consent_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'permissions' => 'array',
        'accounts_data' => 'array',
        'metadata' => 'array',
        'configuration' => 'array',
        'is_active' => 'boolean',
        'auto_sync' => 'boolean',
        'error_count' => 'integer'
    ];

    protected $hidden = [
        'access_token',
        'refresh_token'
    ];

    /**
     * Relacionamento com o usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com transações sincronizadas
     */
    public function syncedTransactions(): HasMany
    {
        return $this->hasMany(SyncedTransaction::class);
    }

    /**
     * Verifica se o token está válido
     */
    public function isTokenValid(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isFuture();
    }

    /**
     * Verifica se o consentimento está válido
     */
    public function isConsentValid(): bool
    {
        return $this->status === 'active' && 
               $this->consent_expires_at && 
               $this->consent_expires_at->isFuture();
    }

    /**
     * Verifica se a integração está ativa e funcional (compatível)
     */
    public function isOperational(): bool
    {
        $basicCheck = $this->is_active && $this->isTokenValid() && $this->isConsentValid();
        
        if ($this->hasColumn('error_count')) {
            return $basicCheck && $this->error_count < 5;
        } else {
            return $basicCheck && $this->status !== 'error';
        }
    }

    /**
     * Incrementa contador de erros (compatível com nova e antiga estrutura)
     */
    public function incrementError(string $error = null): void
    {
        if ($this->hasColumn('error_count')) {
            $this->increment('error_count');
            if ($error) {
                $this->update(['last_error' => $error]);
            }
            // Desativa se muitos erros
            if ($this->error_count >= 5) {
                $this->update(['is_active' => false]);
            }
        } else {
            $this->update([
                'status' => 'error',
                'error_message' => $error
            ]);
        }
    }
    
    // Alias para compatibilidade
    public function incrementErrorCount(string $error = null): void
    {
        $this->incrementError($error);
    }

    /**
     * Reseta contador de erros (compatível com nova e antiga estrutura)
     */
    public function resetErrors(): void
    {
        if ($this->hasColumn('error_count')) {
            $this->update([
                'error_count' => 0,
                'last_error' => null
            ]);
        } else {
            $this->update([
                'status' => 'active',
                'error_message' => null
            ]);
        }
    }
    
    // Alias para compatibilidade
    public function resetErrorCount(): void
    {
        $this->resetErrors();
    }

    /**
     * Atualiza dados de sincronização
     */
    public function updateSyncData(): void
    {
        $this->update(['last_sync_at' => now()]);
        $this->resetErrors();
    }

    /**
     * Obtém lista de contas disponíveis
     */
    public function getAvailableAccounts(): array
    {
        return $this->accounts_data ?? [];
    }

    /**
     * Verifica se tem permissão específica
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Scope para integrações ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para integrações operacionais (compatível)
     */
    public function scopeOperational($query)
    {
        $query = $query->where('is_active', true)
                      ->where('status', 'active')
                      ->where('consent_expires_at', '>', now());
        
        // Se tem coluna error_count, adiciona filtro
        if ($this->hasColumn('error_count')) {
            $query->where('error_count', '<', 5);
        }
        
        return $query;
    }
    
    /**
     * Verifica se uma coluna existe na tabela
     */
    private function hasColumn(string $column): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), $column);
    }
}