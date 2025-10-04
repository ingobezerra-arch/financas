<?php

namespace App\Jobs;

use App\Models\BankIntegration;
use App\Services\OpenFinanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncBankTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected BankIntegration $bankIntegration;
    protected int $daysBack;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BankIntegration $bankIntegration, int $daysBack = 30)
    {
        $this->bankIntegration = $bankIntegration;
        $this->daysBack = $daysBack;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OpenFinanceService $openFinanceService)
    {
        try {
            Log::info('Iniciando sincronização bancária automática', [
                'integration_id' => $this->bankIntegration->id,
                'bank_name' => $this->bankIntegration->bank_name,
                'days_back' => $this->daysBack
            ]);

            // Verifica se a integração está operacional
            if (!$this->bankIntegration->isOperational()) {
                Log::warning('Integração não operacional, pulando sincronização', [
                    'integration_id' => $this->bankIntegration->id,
                    'is_active' => $this->bankIntegration->is_active,
                    'consent_status' => $this->bankIntegration->consent_status,
                    'error_count' => $this->bankIntegration->error_count
                ]);
                return;
            }

            // Executa sincronização
            $result = $openFinanceService->syncTransactions(
                $this->bankIntegration,
                null, // todas as contas
                $this->daysBack
            );

            if ($result['success']) {
                Log::info('Sincronização bancária concluída com sucesso', [
                    'integration_id' => $this->bankIntegration->id,
                    'new_transactions' => $result['new_transactions'],
                    'updated_transactions' => $result['updated_transactions']
                ]);
            } else {
                Log::error('Erro na sincronização bancária', [
                    'integration_id' => $this->bankIntegration->id,
                    'error' => $result['error']
                ]);
                
                // Incrementa contador de erro
                $this->bankIntegration->incrementError($result['error']);
            }

        } catch (\Exception $e) {
            Log::error('Exceção durante sincronização bancária', [
                'integration_id' => $this->bankIntegration->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Incrementa contador de erro
            $this->bankIntegration->incrementError($e->getMessage());
            
            // Re-lança a exceção para que o job seja marcado como falhado
            throw $e;
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de sincronização bancária falhou', [
            'integration_id' => $this->bankIntegration->id,
            'exception' => $exception->getMessage()
        ]);
    }
}
