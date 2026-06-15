<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cria o usuário Administrador para o Backpack (se não existir)
        User::firstOrCreate(
            ['email' => 'admin@barbearia.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('admin123'),
                'telefone' => '(00) 00000-0000',
            ]
        );

        User::firstOrCreate(
            ['email' => 'loacirr@gmail.com'],
            [
                'name'     => 'usuario teste',
                'password' => Hash::make('admin123'),
                'telefone' => '(00) 00000-0000',
            ]
        );

        // 2. Executa todos os seeders em sequência
        $this->call([
            BarbeiroSeeder::class,
            HorariosIniciaisSeeder::class,
            ServicoSeeder::class, 
        ]);
    }
}