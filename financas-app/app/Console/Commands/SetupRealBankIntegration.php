<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupRealBankIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:setup-real {--check : Apenas verificar configuração}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura integração com bancos reais via Open Finance';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== SETUP INTEGRAÇÃO BANCÁRIA REAL ===');
        $this->newLine();
        
        if ($this->option('check')) {
            return $this->checkConfiguration();
        }
        
        $this->info('Este comando ajudará você a configurar integrações reais com bancos.');
        $this->newLine();
        
        // Verifica status atual
        $currentMode = config('open_finance.sandbox_mode', true);
        $useRealApis = config('open_finance.production.use_real_apis', false);
        
        $this->info('Status atual:');
        $this->line('- Modo Sandbox: ' . ($currentMode ? 'ATIVADO' : 'DESATIVADO'));
        $this->line('- APIs Reais: ' . ($useRealApis ? 'ATIVADAS' : 'DESATIVADAS'));
        $this->newLine();
        
        // Perguntas de configuração
        $setupReal = $this->confirm('Deseja configurar integração com bancos reais?');
        
        if (!$setupReal) {
            $this->info('Operação cancelada.');
            return Command::SUCCESS;
        }
        
        $this->warn('ATENÇÃO: Para usar APIs reais você precisa:');
        $this->line('1. Estar registrado como TPP (Third Party Provider) no Open Finance');
        $this->line('2. Ter certificados digitais válidos (ICP-Brasil)');
        $this->line('3. Ter aprovado no Banco Central');
        $this->line('4. Ter credenciais de cada banco');
        $this->newLine();
        
        $hasCredentials = $this->confirm('Você possui todas as credenciais necessárias?');
        
        if (!$hasCredentials) {
            $this->error('Sem as credenciais não é possível configurar APIs reais.');
            $this->info('Mantenha o modo simulado por enquanto.');
            return Command::FAILURE;
        }
        
        // Coleta informações
        $clientId = $this->ask('Client ID do Open Finance:');
        $clientSecret = $this->secret('Client Secret do Open Finance:');
        $certPath = $this->ask('Caminho para o certificado (.pem):');
        $keyPath = $this->ask('Caminho para a chave privada (.pem):');
        
        // Valida arquivos
        if (!File::exists($certPath)) {
            $this->error("Certificado não encontrado: {$certPath}");
            return Command::FAILURE;
        }
        
        if (!File::exists($keyPath)) {
            $this->error("Chave privada não encontrada: {$keyPath}");
            return Command::FAILURE;
        }
        
        // Cria configuração
        $envContent = File::get(base_path('.env'));
        
        $newEnvVars = [
            'OPEN_FINANCE_SANDBOX=false',
            'OPEN_FINANCE_USE_REAL_APIS=true',
            "OPEN_FINANCE_CLIENT_ID={$clientId}",
            "OPEN_FINANCE_CLIENT_SECRET={$clientSecret}",
            "OPEN_FINANCE_MTLS_CERT={$certPath}",
            "OPEN_FINANCE_MTLS_KEY={$keyPath}",
        ];
        
        foreach ($newEnvVars as $envVar) {
            list($key) = explode('=', $envVar, 2);
            
            if (strpos($envContent, $key) !== false) {
                // Atualiza variável existente
                $envContent = preg_replace("/^{$key}=.*/m", $envVar, $envContent);
            } else {
                // Adiciona nova variável
                $envContent .= PHP_EOL . $envVar;
            }
        }
        
        // Salva .env
        File::put(base_path('.env'), $envContent);
        
        $this->info('Configuração atualizada no arquivo .env');
        $this->newLine();
        
        // Limpa cache
        $this->call('config:clear');
        $this->info('Cache de configuração limpo.');
        
        $this->newLine();
        $this->info('✓ Configuração concluída!');
        $this->warn('IMPORTANTE: Reinicie o servidor web para aplicar as mudanças.');
        
        return Command::SUCCESS;
    }
    
    private function checkConfiguration(): int
    {
        $this->info('=== VERIFICAÇÃO DE CONFIGURAÇÃO ===');
        $this->newLine();
        
        $sandboxMode = config('open_finance.sandbox_mode', true);
        $useRealApis = config('open_finance.production.use_real_apis', false);
        $clientId = config('open_finance.client_id');
        $clientSecret = config('open_finance.client_secret');
        $certPath = config('open_finance.production.mtls_cert');
        $keyPath = config('open_finance.production.mtls_key');
        
        $this->table(['Configuração', 'Valor', 'Status'], [
            ['Modo Sandbox', $sandboxMode ? 'Ativado' : 'Desativado', $sandboxMode ? '⚠️  Simulado' : '✓ Produção'],
            ['APIs Reais', $useRealApis ? 'Ativadas' : 'Desativadas', $useRealApis ? '✓ Configurado' : '⚠️  Simulado'],
            ['Client ID', $clientId ? substr($clientId, 0, 20) . '...' : 'Não configurado', $clientId ? '✓' : '❌'],
            ['Client Secret', $clientSecret ? 'Configurado' : 'Não configurado', $clientSecret ? '✓' : '❌'],
            ['Certificado mTLS', $certPath ?: 'Não configurado', $certPath && File::exists($certPath) ? '✓' : '❌'],
            ['Chave Privada', $keyPath ?: 'Não configurado', $keyPath && File::exists($keyPath) ? '✓' : '❌'],
        ]);
        
        $this->newLine();
        
        if ($sandboxMode && !$useRealApis) {
            $this->info('📊 Sistema em MODO DE DEMONSTRAÇÃO');
            $this->line('- Usando dados fictícios para simulação');
            $this->line('- Para usar dados reais, execute: php artisan bank:setup-real');
        } elseif ($useRealApis) {
            $this->info('🏦 Sistema configurado para BANCOS REAIS');
            
            $errors = 0;
            if (!$clientId) { $this->error('Client ID não configurado'); $errors++; }
            if (!$clientSecret) { $this->error('Client Secret não configurado'); $errors++; }
            if (!$certPath || !File::exists($certPath)) { $this->error('Certificado mTLS inválido'); $errors++; }
            if (!$keyPath || !File::exists($keyPath)) { $this->error('Chave privada inválida'); $errors++; }
            
            if ($errors > 0) {
                $this->error("Encontrados {$errors} erro(s) na configuração.");
                return Command::FAILURE;
            } else {
                $this->info('✓ Todas as configurações estão corretas!');
            }
        }
        
        return Command::SUCCESS;
    }
}
