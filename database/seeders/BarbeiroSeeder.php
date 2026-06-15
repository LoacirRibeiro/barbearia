<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barbeiro;

class BarbeiroSeeder extends Seeder
{
    public function run(): void
    {
        // 💈 Cadastra o primeiro barbeiro (ID 1)
        Barbeiro::updateOrCreate(
            ['id' => 1], 
            [
                'nome' => 'Coxxa',
            ]
        );

        // 💈 Cadastra o segundo barbeiro (ID 2)
        Barbeiro::updateOrCreate(
            ['id' => 2], 
            [
                'nome' => 'Loacir',
            ]
        );

        $this->command->info('✅ Barbeiros "Coxxa" e "Loacir" cadastrados com sucesso!');
    }
}