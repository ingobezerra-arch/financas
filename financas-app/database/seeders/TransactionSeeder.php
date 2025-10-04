<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = User::where('email', 'admin@financas.com')->first();
        $regularUser = User::where('email', 'usuario@financas.com')->first();
        
        if ($adminUser) {
            $adminAccounts = $adminUser->accounts;
            $categories = $adminUser->categories;
            
            if ($adminAccounts->count() > 0 && $categories->count() > 0) {
                $checkingAccount = $adminAccounts->where('type', 'checking')->first();
                $savingsAccount = $adminAccounts->where('type', 'savings')->first();
                $creditCard = $adminAccounts->where('type', 'credit_card')->first();
                
                $incomeCategories = $categories->where('type', 'income');
                $expenseCategories = $categories->where('type', 'expense');
                
                // Transações dos últimos 30 dias
                $transactions = [
                    // Receitas
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $incomeCategories->first()->id,
                        'type' => 'income',
                        'amount' => 5000.00,
                        'description' => 'Salário mensal',
                        'transaction_date' => Carbon::now()->startOfMonth(),
                        'status' => 'completed'
                    ],
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $incomeCategories->first()->id,
                        'type' => 'income',
                        'amount' => 800.00,
                        'description' => 'Freelance projeto website',
                        'transaction_date' => Carbon::now()->subDays(5),
                        'status' => 'completed'
                    ],
                    
                    // Despesas
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $expenseCategories->where('name', 'Moradia')->first()->id ?? $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 1200.00,
                        'description' => 'Aluguel apartamento',
                        'transaction_date' => Carbon::now()->subDays(3),
                        'status' => 'completed'
                    ],
                    [
                        'account_id' => $creditCard->id,
                        'category_id' => $expenseCategories->where('name', 'Alimentação')->first()->id ?? $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 450.00,
                        'description' => 'Supermercado do mês',
                        'transaction_date' => Carbon::now()->subDays(2),
                        'status' => 'completed'
                    ],
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $expenseCategories->where('name', 'Transporte')->first()->id ?? $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 89.50,
                        'description' => 'Combustível posto',
                        'transaction_date' => Carbon::now()->subDays(1),
                        'status' => 'completed'
                    ],
                    [
                        'account_id' => $creditCard->id,
                        'category_id' => $expenseCategories->where('name', 'Lazer')->first()->id ?? $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 120.00,
                        'description' => 'Cinema com amigos',
                        'transaction_date' => Carbon::now()->subDays(1),
                        'status' => 'completed'
                    ],
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $expenseCategories->where('name', 'Saúde')->first()->id ?? $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 180.00,
                        'description' => 'Consulta médica',
                        'transaction_date' => Carbon::now()->subDays(7),
                        'status' => 'completed'
                    ],
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $expenseCategories->where('name', 'Educação')->first()->id ?? $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 350.00,
                        'description' => 'Curso online programação',
                        'transaction_date' => Carbon::now()->subDays(10),
                        'status' => 'completed'
                    ]
                ];
                
                foreach ($transactions as $transactionData) {
                    $transactionData['user_id'] = $adminUser->id;
                    Transaction::create($transactionData);
                }
            }
        }
        
        if ($regularUser) {
            $userAccounts = $regularUser->accounts;
            $categories = $regularUser->categories;
            
            if ($userAccounts->count() > 0 && $categories->count() > 0) {
                $checkingAccount = $userAccounts->where('type', 'checking')->first();
                $digitalWallet = $userAccounts->where('type', 'digital_wallet')->first();
                
                $incomeCategories = $categories->where('type', 'income');
                $expenseCategories = $categories->where('type', 'expense');
                
                $transactions = [
                    // Receitas
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $incomeCategories->first()->id,
                        'type' => 'income',
                        'amount' => 3200.00,
                        'description' => 'Salário CLT',
                        'transaction_date' => Carbon::now()->startOfMonth(),
                        'status' => 'completed'
                    ],
                    
                    // Despesas
                    [
                        'account_id' => $checkingAccount->id,
                        'category_id' => $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 800.00,
                        'description' => 'Aluguel quitinete',
                        'transaction_date' => Carbon::now()->subDays(2),
                        'status' => 'completed'
                    ],
                    [
                        'account_id' => $digitalWallet->id,
                        'category_id' => $expenseCategories->first()->id,
                        'type' => 'expense',
                        'amount' => 25.90,
                        'description' => 'Lanche delivery',
                        'transaction_date' => Carbon::now(),
                        'status' => 'completed'
                    ]
                ];
                
                foreach ($transactions as $transactionData) {
                    $transactionData['user_id'] = $regularUser->id;
                    Transaction::create($transactionData);
                }
            }
        }
        
        echo "Transações de exemplo criadas com sucesso!\n";
        echo "Admin: 8 transações criadas\n";
        echo "Usuário: 3 transações criadas\n";
    }
}
