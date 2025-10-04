<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buscar usuários
        $adminUser = User::where('email', 'admin@financas.com')->first();
        $regularUser = User::where('email', 'usuario@financas.com')->first();
        
        if ($adminUser) {
            // Contas para o Admin
            Account::create([
                'user_id' => $adminUser->id,
                'name' => 'Conta Corrente Banco do Brasil',
                'type' => 'checking',
                'balance' => 5000.00,
                'currency' => 'BRL',
                'color' => '#1e40af',
                'icon' => 'fas fa-university',
                'description' => 'Conta corrente principal para movimentação diária',
                'is_active' => true
            ]);
            
            Account::create([
                'user_id' => $adminUser->id,
                'name' => 'Poupança Caixa',
                'type' => 'savings',
                'balance' => 15000.00,
                'currency' => 'BRL',
                'color' => '#059669',
                'icon' => 'fas fa-piggy-bank',
                'description' => 'Conta poupança para reserva de emergência',
                'is_active' => true
            ]);
            
            Account::create([
                'user_id' => $adminUser->id,
                'name' => 'Cartão de Crédito Nubank',
                'type' => 'credit_card',
                'balance' => -850.00,
                'currency' => 'BRL',
                'color' => '#7c3aed',
                'icon' => 'fas fa-credit-card',
                'description' => 'Cartão de crédito para compras e emergências',
                'is_active' => true
            ]);
        }
        
        if ($regularUser) {
            // Contas para o Usuário Regular
            Account::create([
                'user_id' => $regularUser->id,
                'name' => 'Conta Corrente Santander',
                'type' => 'checking',
                'balance' => 2500.00,
                'currency' => 'BRL',
                'color' => '#dc2626',
                'icon' => 'fas fa-university',
                'description' => 'Conta corrente para uso pessoal',
                'is_active' => true
            ]);
            
            Account::create([
                'user_id' => $regularUser->id,
                'name' => 'Carteira Digital PicPay',
                'type' => 'digital_wallet',
                'balance' => 350.00,
                'currency' => 'BRL',
                'color' => '#10b981',
                'icon' => 'fas fa-mobile-alt',
                'description' => 'Carteira digital para pagamentos rápidos',
                'is_active' => true
            ]);
        }
        
        echo "Contas de exemplo criadas com sucesso!\n";
        echo "Admin: 3 contas criadas\n";
        echo "Usuário: 2 contas criadas\n";
    }
}
