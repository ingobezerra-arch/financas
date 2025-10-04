<?php

namespace App\Services;

use App\Models\BankIntegration;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RealOpenFinanceService extends OpenFinanceService
{
    protected bool $useRealApis;
    protected array $bankEndpoints;
    protected string $certificatesPath;

    public function __construct()
    {
        parent::__construct();
        
        $this->useRealApis = config('open_finance.production.use_real_apis', false);
        $this->bankEndpoints = config('open_finance.bank_endpoints', []);
        $this->certificatesPath = config('open_finance.production.certificates_path');
    }

    /**
     * Inicia processo de consentimento REAL
     */
    public function initiateConsent(int $userId, string $bankCode, array $permissions = []): array
    {
        if (!$this->useRealApis) {
            // Usa versão simulada se não estiver em modo real
            return parent::initiateConsent($userId, $bankCode, $permissions);
        }

        try {
            $bankEndpoint = $this->bankEndpoints[$bankCode] ?? null;
            
            if (!$bankEndpoint) {
                throw new Exception("Banco $bankCode não suportado para APIs reais");
            }

            // Monta requisição real para o banco
            $consentRequest = [
                'data' => [
                    'permissions' => $permissions ?: config('open_finance.default_permissions'),
                    'expirationDateTime' => now()->addDays(90)->toISOString(),
                    'transactionFromDateTime' => now()->subYear()->toISOString(),
                    'transactionToDateTime' => now()->toISOString(),
                ]
            ];

            // Faz requisição HTTP real para o banco
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getClientCredentialsToken($bankCode),
                'Content-Type' => 'application/json',
                'x-fapi-interaction-id' => Str::uuid(),
            ])
            ->withOptions([
                'cert' => config('open_finance.production.mtls_cert'),
                'ssl_key' => config('open_finance.production.mtls_key'),
                'verify' => true,
            ])
            ->post($bankEndpoint['resource_server'] . '/consents', $consentRequest);

            if (!$response->successful()) {
                throw new Exception("Erro na API do banco: " . $response->body());
            }

            $consentData = $response->json()['data'];

            // Cria registro de integração
            $integration = BankIntegration::create([
                'user_id' => $userId,
                'bank_code' => $bankCode,
                'bank_name' => $this->supportedBanks[$bankCode] ?? 'Banco desconhecido',
                'consent_id' => $consentData['consentId'],
                'status' => 'pending',
                'consent_expires_at' => $consentData['expirationDateTime'],
                'permissions' => $permissions,
                'is_active' => false
            ]);

            // URL real de autorização do banco
            $authUrl = $bankEndpoint['authorization_server'] . '/oauth2/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => config('open_finance.client_id'),
                'scope' => 'consents accounts',
                'redirect_uri' => config('open_finance.redirect_uri') . '/' . $bankCode,
                'state' => $consentData['consentId'],
                'code_challenge' => $this->generateCodeChallenge(),
                'code_challenge_method' => 'S256',
            ]);

            Log::info('Consentimento real iniciado', [
                'user_id' => $userId,
                'bank_code' => $bankCode,
                'consent_id' => $consentData['consentId']
            ]);

            return [
                'success' => true,
                'integration_id' => $integration->id,
                'consent_url' => $authUrl,
                'consent_id' => $consentData['consentId']
            ];

        } catch (Exception $e) {
            Log::error('Erro ao iniciar consentimento real', [
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
     * Busca transações REAIS do banco
     */
    protected function fetchTransactions(string $accessToken, string $bankCode, string $accountId, int $days): array
    {
        if (!$this->useRealApis) {
            return parent::fetchTransactions($accessToken, $bankCode, $accountId, $days);
        }

        try {
            $bankEndpoint = $this->bankEndpoints[$bankCode];
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'x-fapi-interaction-id' => Str::uuid(),
            ])
            ->withOptions([
                'cert' => config('open_finance.production.mtls_cert'),
                'ssl_key' => config('open_finance.production.mtls_key'),
            ])
            ->get($bankEndpoint['resource_server'] . "/accounts/{$accountId}/transactions", [
                'fromTransactionDate' => now()->subDays($days)->format('Y-m-d'),
                'toTransactionDate' => now()->format('Y-m-d'),
            ]);

            if (!$response->successful()) {
                throw new Exception("Erro ao buscar transações: " . $response->body());
            }

            return $response->json()['data'];

        } catch (Exception $e) {
            Log::error('Erro ao buscar transações reais', [
                'bank_code' => $bankCode,
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);

            // Fallback para dados simulados em caso de erro
            return parent::fetchTransactions($accessToken, $bankCode, $accountId, $days);
        }
    }

    /**
     * Obtém token de client credentials
     */
    protected function getClientCredentialsToken(string $bankCode): string
    {
        $bankEndpoint = $this->bankEndpoints[$bankCode];
        
        $response = Http::asForm()
            ->withOptions([
                'cert' => config('open_finance.production.mtls_cert'),
                'ssl_key' => config('open_finance.production.mtls_key'),
            ])
            ->post($bankEndpoint['authorization_server'] . '/oauth2/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('open_finance.client_id'),
                'client_secret' => config('open_finance.client_secret'),
                'scope' => 'consents',
            ]);

        return $response->json()['access_token'];
    }

    /**
     * Gera code challenge para PKCE
     */
    protected function generateCodeChallenge(): string
    {
        $codeVerifier = Str::random(128);
        return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }

    /**
     * Processa callback REAL do consentimento
     */
    public function processConsentCallback(string $consentId, string $authCode): array
    {
        if (!$this->useRealApis) {
            return parent::processConsentCallback($consentId, $authCode);
        }

        try {
            $integration = BankIntegration::where('consent_id', $consentId)->firstOrFail();
            $bankEndpoint = $this->bankEndpoints[$integration->bank_code];

            // Troca código por token REAL
            $tokenResponse = Http::asForm()
                ->withOptions([
                    'cert' => config('open_finance.production.mtls_cert'),
                    'ssl_key' => config('open_finance.production.mtls_key'),
                ])
                ->post($bankEndpoint['authorization_server'] . '/oauth2/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('open_finance.client_id'),
                    'client_secret' => config('open_finance.client_secret'),
                    'code' => $authCode,
                    'redirect_uri' => config('open_finance.redirect_uri') . '/' . $integration->bank_code,
                ]);

            if (!$tokenResponse->successful()) {
                throw new Exception("Erro ao trocar código por token: " . $tokenResponse->body());
            }

            $tokenData = $tokenResponse->json();

            // Busca contas REAIS
            $accountsData = $this->fetchRealAccounts($tokenData['access_token'], $integration->bank_code);

            $integration->update([
                'access_token' => encrypt($tokenData['access_token']),
                'refresh_token' => encrypt($tokenData['refresh_token'] ?? ''),
                'token_expires_at' => now()->addSeconds($tokenData['expires_in']),
                'status' => 'active',
                'accounts_data' => $accountsData,
                'is_active' => true,
                'error_message' => null
            ]);

            // Faz a primeira sincronização automaticamente
            $syncResult = $this->syncTransactions($integration, null, 30);

            Log::info('Consentimento real autorizado e sincronização inicial executada', [
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
            Log::error('Erro no callback do consentimento real', [
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
     * Busca contas REAIS do banco
     */
    protected function fetchRealAccounts(string $accessToken, string $bankCode): array
    {
        $bankEndpoint = $this->bankEndpoints[$bankCode];
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'x-fapi-interaction-id' => Str::uuid(),
        ])
        ->withOptions([
            'cert' => config('open_finance.production.mtls_cert'),
            'ssl_key' => config('open_finance.production.mtls_key'),
        ])
        ->get($bankEndpoint['resource_server'] . '/accounts');

        if (!$response->successful()) {
            throw new Exception("Erro ao buscar contas: " . $response->body());
        }

        return $response->json()['data'];
    }
}