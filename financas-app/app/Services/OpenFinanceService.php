<?php

namespace App\Services;

use App\Models\BankIntegration;
use App\Models\SyncedTransaction;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OpenFinanceService
{
    protected string $baseUrl;
    protected array $supportedBanks;

    public function __construct()
    {
        $this->baseUrl = config('open_finance.base_url', 'https://api.openfinancebrasil.org.br');
        $this->supportedBanks = config('open_finance.supported_banks', [
            '001' => 'Banco do Brasil',
            '033' => 'Santander',
            '104' => 'Caixa Econômica Federal',
            '237' => 'Bradesco',
            '341' => 'Itaú',
            '422' => 'Safra',
            '756' => 'Sicoob',
        ]);
    }

    /**
     * Obtém lista de bancos suportados
     */
    public function getSupportedBanks(): array
    {
        return $this->supportedBanks;
    }

    /**
     * Inicia processo de consentimento
     */
    public function initiateConsent(int $userId, string $bankCode, array $permissions = []): array
    {
        try {
            $defaultPermissions = [
                'ACCOUNTS_READ',
                'ACCOUNTS_BALANCES_READ',
                'RESOURCES_READ'
            ];

            $permissions = array_merge($defaultPermissions, $permissions);

            // Simula resposta da API (em produção usaria API real)
            $consentData = [
                'consent_id' => 'consent_' . uniqid(),
                'status' => 'AWAITING_AUTHORISATION',
                'creation_date_time' => now()->toISOString(),
                'expiration_date_time' => now()->addDays(90)->toISOString(),
                'permissions' => $permissions,
                'redirect_url' => route('bank.integration.callback', ['bank' => $bankCode])
            ];

            // Cria registro de integração pendente
            $integration = BankIntegration::create([
                'user_id' => $userId,
                'bank_code' => $bankCode,
                'bank_name' => $this->supportedBanks[$bankCode] ?? 'Banco desconhecido',
                'consent_id' => $consentData['consent_id'],
                'status' => 'pending',
                'consent_expires_at' => $consentData['expiration_date_time'],
                'permissions' => $permissions,
                'is_active' => false
            ]);

            Log::info('Consentimento iniciado', [
                'user_id' => $userId,
                'bank_code' => $bankCode,
                'consent_id' => $consentData['consent_id']
            ]);

            return [
                'success' => true,
                'integration_id' => $integration->id,
                'consent_url' => $this->generateConsentUrl($consentData['consent_id'], $bankCode),
                'consent_id' => $consentData['consent_id']
            ];

        } catch (Exception $e) {
            Log::error('Erro ao iniciar consentimento', [
                'user_id' => $userId,
                'bank_code' => $bankCode,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Processa callback do consentimento
     */
    public function processConsentCallback(string $consentId, string $authCode): array
    {
        try {
            $integration = BankIntegration::where('consent_id', $consentId)->firstOrFail();

            // Simula troca do código por token (em produção usaria API real)
            $tokenData = [
                'access_token' => 'access_' . uniqid(),
                'refresh_token' => 'refresh_' . uniqid(),
                'expires_in' => 3600,
                'token_type' => 'Bearer'
            ];

            // Simula obtenção das contas
            $accountsData = $this->fetchAccounts($tokenData['access_token'], $integration->bank_code);

            $integration->update([
                'access_token' => encrypt($tokenData['access_token']),
                'refresh_token' => encrypt($tokenData['refresh_token']),
                'token_expires_at' => now()->addSeconds($tokenData['expires_in']),
                'status' => 'active',
                'accounts_data' => $accountsData,
                'is_active' => true,
                'error_message' => null
            ]);

            // Faz a primeira sincronização automaticamente
            $syncResult = $this->syncTransactions($integration, null, 30);

            Log::info('Consentimento autorizado e sincronização inicial executada', [
                'integration_id' => $integration->id,
                'consent_id' => $consentId,
                'sync_success' => $syncResult['success'],
                'new_transactions' => $syncResult['new_transactions'] ?? 0
            ]);

            return [
                'success' => true,
                'integration' => $integration,
                'accounts' => $accountsData,
                'sync_result' => $syncResult
            ];

        } catch (Exception $e) {
            Log::error('Erro no callback do consentimento', [
                'consent_id' => $consentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Busca contas do banco
     */
    protected function fetchAccounts(string $accessToken, string $bankCode): array
    {
        // Simula dados das contas (em produção usaria API real)
        return [
            [
                'account_id' => 'acc_' . uniqid(),
                'account_type' => 'CONTA_DEPOSITO_A_VISTA',
                'account_subtype' => 'CONTA_CORRENTE',
                'number' => '12345-6',
                'check_digit' => '6',
                'agency' => '1234',
                'agency_check_digit' => '4',
                'currency' => 'BRL',
                'account_holder_name' => 'João Silva',
                'balance' => [
                    'available' => 5000.00,
                    'blocked' => 0.00,
                    'current' => 5000.00
                ]
            ]
        ];
    }

    /**
     * Sincroniza transações de uma integração
     */
    public function syncTransactions(BankIntegration $integration, array $accountIds = null, int $days = 30): array
    {
        try {
            if (!$integration->isOperational()) {
                throw new Exception('Integração não está operacional');
            }

            $accessToken = decrypt($integration->access_token);
            $accounts = $accountIds ?? array_column($integration->accounts_data, 'account_id');
            $results = [];

            foreach ($accounts as $accountId) {
                $transactions = $this->fetchTransactions($accessToken, $integration->bank_code, $accountId, $days);
                $processed = $this->processTransactions($integration, $accountId, $transactions);
                $results[$accountId] = $processed;
            }

            $integration->updateSyncData();

            $totalNew = array_sum(array_column($results, 'new_count'));
            $totalUpdated = array_sum(array_column($results, 'updated_count'));

            Log::info('Sincronização de transações concluída', [
                'integration_id' => $integration->id,
                'new_transactions' => $totalNew,
                'updated_transactions' => $totalUpdated
            ]);

            return [
                'success' => true,
                'new_transactions' => $totalNew,
                'updated_transactions' => $totalUpdated,
                'details' => $results
            ];

        } catch (Exception $e) {
            $integration->incrementError($e->getMessage());
            
            Log::error('Erro na sincronização de transações', [
                'integration_id' => $integration->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Busca transações do banco
     */
    protected function fetchTransactions(string $accessToken, string $bankCode, string $accountId, int $days): array
    {
        // Simula dados de transações (em produção usaria API real)
        $transactions = [];
        
        for ($i = 0; $i < rand(5, 20); $i++) {
            $isCredit = rand(0, 1);
            $amount = rand(10, 1000);
            
            $transactions[] = [
                'transaction_id' => 'txn_' . uniqid(),
                'amount' => $amount,
                'currency' => 'BRL',
                'type' => $isCredit ? 'CREDITO' : 'DEBITO',
                'credit_debit_type' => $isCredit ? 'CREDITO' : 'DEBITO',
                'transaction_name' => $this->generateRandomTransactionName($isCredit),
                'transaction_date' => now()->subDays(rand(1, $days))->format('Y-m-d'),
                'value_date' => now()->subDays(rand(1, $days))->format('Y-m-d'),
                'counterpart' => [
                    'name' => 'Estabelecimento ' . rand(1, 100),
                    'document' => '12345678000199'
                ],
                'mcc' => rand(5411, 9999)
            ];
        }
        
        return $transactions;
    }

    /**
     * Processa transações e salva no banco
     */
    protected function processTransactions(BankIntegration $integration, string $accountId, array $transactions): array
    {
        $newCount = 0;
        $updatedCount = 0;
        $account = collect($integration->accounts_data)->firstWhere('account_id', $accountId);

        foreach ($transactions as $txnData) {
            $existing = SyncedTransaction::where('bank_transaction_id', $txnData['transaction_id'])->first();

            $syncedData = [
                'user_id' => $integration->user_id,
                'bank_integration_id' => $integration->id,
                'bank_transaction_id' => $txnData['transaction_id'],
                'account_id' => $accountId,
                'account_number' => $account['number'] ?? '',
                'account_type' => $account['account_type'] ?? '',
                'amount' => abs($txnData['amount']),
                'type' => strtolower($txnData['credit_debit_type']) === 'credito' ? 'credit' : 'debit',
                'description' => $txnData['transaction_name'],
                'full_description' => json_encode($txnData),
                'transaction_date' => $txnData['transaction_date'],
                'processed_at' => now(),
                'mcc_code' => $txnData['mcc'] ?? null,
                'counterpart_name' => $txnData['counterpart']['name'] ?? null,
                'counterpart_document' => $txnData['counterpart']['document'] ?? null,
                'raw_data' => $txnData
            ];

            if ($existing) {
                $existing->update($syncedData);
                $updatedCount++;
            } else {
                $syncedTransaction = SyncedTransaction::create($syncedData);
                
                // Sugere categoria automaticamente
                $suggestedCategory = $syncedTransaction->suggestCategory();
                if ($suggestedCategory) {
                    $syncedTransaction->update(['category_id' => $suggestedCategory->id]);
                }
                
                $newCount++;
            }
        }

        return [
            'new_count' => $newCount,
            'updated_count' => $updatedCount
        ];
    }

    /**
     * Gera URL de consentimento (mock)
     */
    protected function generateConsentUrl(string $consentId, string $bankCode): string
    {
        return route('bank.integration.simulate', [
            'consent_id' => $consentId,
            'bank_code' => $bankCode
        ]);
    }

    /**
     * Gera nome aleatório para transação (mock)
     */
    protected function generateRandomTransactionName(bool $isCredit): string
    {
        if ($isCredit) {
            $names = ['Salário', 'Transferência recebida', 'Rendimento poupança', 'Freelance', 'Venda'];
        } else {
            $names = ['Supermercado', 'Posto de gasolina', 'Restaurante', 'Farmácia', 'Netflix', 'Uber', 'Conta de luz'];
        }

        return $names[array_rand($names)] . ' ' . rand(1, 999);
    }

    /**
     * Revoga consentimento
     */
    public function revokeConsent(BankIntegration $integration): array
    {
        try {
            // Em produção, faria chamada para API para revogar
            
            $integration->update([
                'status' => 'revoked',
                'is_active' => false,
                'access_token' => null,
                'refresh_token' => null
            ]);

            Log::info('Consentimento revogado', [
                'integration_id' => $integration->id
            ]);

            return ['success' => true];

        } catch (Exception $e) {
            Log::error('Erro ao revogar consentimento', [
                'integration_id' => $integration->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}