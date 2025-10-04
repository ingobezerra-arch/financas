<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\Category;
use Carbon\Carbon;

class BudgetGoalSeeder extends Seeder
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
            $expenseCategories = $adminUser->categories()->where('type', 'expense')->get();
            
            if ($expenseCategories->count() > 0) {
                // Orçamentos para o Admin
                $budgets = [
                    [
                        'user_id' => $adminUser->id,
                        'category_id' => $expenseCategories->where('name', 'Alimentação')->first()->id ?? $expenseCategories->first()->id,
                        'name' => 'Orçamento Alimentação - ' . Carbon::now()->format('M/Y'),
                        'amount' => 800.00,
                        'period' => 'monthly',
                        'start_date' => Carbon::now()->startOfMonth(),
                        'end_date' => Carbon::now()->endOfMonth(),
                        'is_active' => true
                    ],
                    [
                        'user_id' => $adminUser->id,
                        'category_id' => $expenseCategories->where('name', 'Transporte')->first()->id ?? $expenseCategories->skip(1)->first()->id,
                        'name' => 'Orçamento Transporte - ' . Carbon::now()->format('M/Y'),
                        'amount' => 400.00,
                        'period' => 'monthly',
                        'start_date' => Carbon::now()->startOfMonth(),
                        'end_date' => Carbon::now()->endOfMonth(),
                        'is_active' => true
                    ],
                    [
                        'user_id' => $adminUser->id,
                        'category_id' => $expenseCategories->where('name', 'Lazer')->first()->id ?? $expenseCategories->skip(2)->first()->id,
                        'name' => 'Orçamento Lazer - ' . Carbon::now()->format('M/Y'),
                        'amount' => 300.00,
                        'period' => 'monthly',
                        'start_date' => Carbon::now()->startOfMonth(),
                        'end_date' => Carbon::now()->endOfMonth(),
                        'is_active' => true
                    ]
                ];
                
                foreach ($budgets as $budgetData) {
                    Budget::create($budgetData);
                }
            }
            
            // Metas para o Admin
            $goals = [
                [
                    'user_id' => $adminUser->id,
                    'name' => 'Reserva de Emergência',
                    'description' => 'Formar uma reserva de emergência equivalente a 6 meses de gastos',
                    'target_amount' => 30000.00,
                    'current_amount' => 8500.00,
                    'target_date' => Carbon::now()->addMonths(12),
                    'start_date' => Carbon::now()->subMonths(2),
                    'status' => 'active',
                    'color' => '#28a745',
                    'icon' => 'fas fa-umbrella',
                    'monthly_contribution' => 1800.00
                ],
                [
                    'user_id' => $adminUser->id,
                    'name' => 'Viagem Europa',
                    'description' => 'Juntar dinheiro para uma viagem de 15 dias pela Europa',
                    'target_amount' => 15000.00,
                    'current_amount' => 4200.00,
                    'target_date' => Carbon::now()->addMonths(8),
                    'start_date' => Carbon::now()->subMonth(),
                    'status' => 'active',
                    'color' => '#007bff',
                    'icon' => 'fas fa-plane',
                    'monthly_contribution' => 1350.00
                ],
                [
                    'user_id' => $adminUser->id,
                    'name' => 'Notebook Novo',
                    'description' => 'Comprar um notebook para trabalho',
                    'target_amount' => 5000.00,
                    'current_amount' => 3800.00,
                    'target_date' => Carbon::now()->addMonths(2),
                    'start_date' => Carbon::now()->subMonths(3),
                    'status' => 'active',
                    'color' => '#6f42c1',
                    'icon' => 'fas fa-laptop',
                    'monthly_contribution' => 600.00
                ]
            ];
            
            foreach ($goals as $goalData) {
                Goal::create($goalData);
            }
        }
        
        if ($regularUser) {
            $expenseCategories = $regularUser->categories()->where('type', 'expense')->get();
            
            if ($expenseCategories->count() > 0) {
                // Orçamento para o Usuário Regular
                Budget::create([
                    'user_id' => $regularUser->id,
                    'category_id' => $expenseCategories->first()->id,
                    'name' => 'Orçamento Mensal - ' . Carbon::now()->format('M/Y'),
                    'amount' => 1500.00,
                    'period' => 'monthly',
                    'start_date' => Carbon::now()->startOfMonth(),
                    'end_date' => Carbon::now()->endOfMonth(),
                    'is_active' => true
                ]);
            }
            
            // Meta para o Usuário Regular
            Goal::create([
                'user_id' => $regularUser->id,
                'name' => 'Carro Usado',
                'description' => 'Juntar dinheiro para comprar um carro usado',
                'target_amount' => 25000.00,
                'current_amount' => 5200.00,
                'target_date' => Carbon::now()->addMonths(15),
                'start_date' => Carbon::now()->subMonths(4),
                'status' => 'active',
                'color' => '#dc3545',
                'icon' => 'fas fa-car',
                'monthly_contribution' => 1300.00
            ]);
        }
        
        echo "Orçamentos e metas de exemplo criados com sucesso!\n";
        echo "Admin: 3 orçamentos e 3 metas criados\n";
        echo "Usuário: 1 orçamento e 1 meta criados\n";
    }
}
