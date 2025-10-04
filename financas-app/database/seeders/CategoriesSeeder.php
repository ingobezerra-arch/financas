<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca o primeiro usuário (para demo)
        $user = User::first();
        
        if (!$user) {
            $this->command->info('Nenhum usuário encontrado. Execute primeiro o seeder de usuários.');
            return;
        }

        $categories = [
            // Categorias de Receita
            ['name' => 'Salário', 'type' => 'income', 'color' => '#28a745', 'icon' => 'fas fa-briefcase'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#17a2b8', 'icon' => 'fas fa-laptop'],
            ['name' => 'Investimentos', 'type' => 'income', 'color' => '#6f42c1', 'icon' => 'fas fa-chart-line'],
            ['name' => 'Vendas', 'type' => 'income', 'color' => '#fd7e14', 'icon' => 'fas fa-shopping-cart'],
            ['name' => 'Outras Receitas', 'type' => 'income', 'color' => '#20c997', 'icon' => 'fas fa-plus-circle'],
            
            // Categorias de Despesa
            ['name' => 'Alimentação', 'type' => 'expense', 'color' => '#dc3545', 'icon' => 'fas fa-utensils'],
            ['name' => 'Transporte', 'type' => 'expense', 'color' => '#fd7e14', 'icon' => 'fas fa-car'],
            ['name' => 'Moradia', 'type' => 'expense', 'color' => '#6c757d', 'icon' => 'fas fa-home'],
            ['name' => 'Saúde', 'type' => 'expense', 'color' => '#e83e8c', 'icon' => 'fas fa-heartbeat'],
            ['name' => 'Educação', 'type' => 'expense', 'color' => '#6f42c1', 'icon' => 'fas fa-graduation-cap'],
            ['name' => 'Entretenimento', 'type' => 'expense', 'color' => '#ffc107', 'icon' => 'fas fa-film'],
            ['name' => 'Roupas', 'type' => 'expense', 'color' => '#e83e8c', 'icon' => 'fas fa-tshirt'],
            ['name' => 'Impostos', 'type' => 'expense', 'color' => '#6c757d', 'icon' => 'fas fa-receipt'],
            ['name' => 'Seguros', 'type' => 'expense', 'color' => '#17a2b8', 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Tecnologia', 'type' => 'expense', 'color' => '#007bff', 'icon' => 'fas fa-laptop'],
            ['name' => 'Viagens', 'type' => 'expense', 'color' => '#28a745', 'icon' => 'fas fa-plane'],
            ['name' => 'Pets', 'type' => 'expense', 'color' => '#fd7e14', 'icon' => 'fas fa-paw'],
            ['name' => 'Outras Despesas', 'type' => 'expense', 'color' => '#6c757d', 'icon' => 'fas fa-minus-circle'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $categoryData['name'],
                    'type' => $categoryData['type']
                ],
                [
                    'color' => $categoryData['color'],
                    'icon' => $categoryData['icon'],
                    'is_active' => true
                ]
            );
        }

        $this->command->info('Categorias padrão criadas com sucesso!');
    }
}
