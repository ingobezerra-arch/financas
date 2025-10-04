<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@financas.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'usuario@financas.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('password'),
            ]
        );

        $this->command->info('Usuários de teste criados com sucesso!');
        $this->command->info('Login: admin@financas.com | Senha: password');
        $this->command->info('Login: usuario@financas.com | Senha: password');
    }
}
