<?php

namespace App\Console\Commands;

use App\Models\BankIntegration;
use App\Jobs\SyncBankTransactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncAllBankIntegrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:sync-all {--force : Forçar sincronização mesmo com erros}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza transações de todas as integrações bancárias ativas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando sincronização de todas as integrações bancárias...');
        
        $query = BankIntegration::query()
            ->where('is_active', true)
            ->where('auto_sync', true);
        
        // Se não for forçado, filtra apenas integrações operacionais
        if (!$this->option('force')) {
            $query->operational();
        }
        
        $integrations = $query->get();
        
        if ($integrations->isEmpty()) {
            $this->warn('Nenhuma integração bancária disponível para sincronização.');
            return Command::SUCCESS;
        }
        
        $this->info("Encontradas {$integrations->count()} integrações para sincronização.");
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($integrations as $integration) {
            try {
                $this->info("Sincronizando {$integration->bank_name} (ID: {$integration->id})...");
                
                // Determina quantos dias sincronizar baseado na última sincronização
                $daysBack = $this->calculateDaysBack($integration);
                
                // Despacha job de sincronização
                SyncBankTransactions::dispatch($integration, $daysBack);
                
                $this->line("  ✓ Job de sincronização despachado para {$integration->bank_name}");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao processar {$integration->bank_name}: {$e->getMessage()}");
                
                Log::error('Erro ao despachar job de sincronização', [
                    'integration_id' => $integration->id,
                    'error' => $e->getMessage()
                ]);
                
                $errorCount++;
            }
        }
        
        $this->newLine();
        $this->info("Sincronização concluída:");
        $this->line("  ✓ Sucessos: {$successCount}");
        $this->line("  ✗ Erros: {$errorCount}");
        
        Log::info('Comando de sincronização bancária executado', [
            'total_integrations' => $integrations->count(),
            'success_count' => $successCount,
            'error_count' => $errorCount
        ]);
        
        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
    
    /**
     * Calcula quantos dias sincronizar baseado na última sincronização
     */
    private function calculateDaysBack(BankIntegration $integration): int
    {
        if (!$integration->last_sync_at) {
            // Primeira sincronização - pega 30 dias
            return 30;
        }
        
        $daysSinceLastSync = now()->diffInDays($integration->last_sync_at);
        
        // Mínimo 1 dia, máximo 30 dias
        return min(max($daysSinceLastSync + 1, 1), 30);
    }
}
