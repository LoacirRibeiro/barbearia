<?php

namespace Database\Seeders; // <-- Corrigido para iniciais maiúsculas

use Illuminate\Database\Seeder;
use App\Models\Servico;

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        $servicos = [
            // Cabelo
            ['nome' => 'Corte Degrade', 'categoria' => 'Cabelo', 'preco' => 30.00],
            ['nome' => 'Corte na Tesoura', 'categoria' => 'Cabelo', 'preco' => 30.00],
            ['nome' => 'Corte Social', 'categoria' => 'Cabelo', 'preco' => 28.00],
            ['nome' => 'Raspa na Máquina', 'categoria' => 'Cabelo', 'preco' => 20.00],
            ['nome' => 'Pezinho', 'categoria' => 'Cabelo', 'preco' => 10.00],
            ['nome' => 'Risquinho / Desenho', 'categoria' => 'Cabelo', 'preco' => 5.00],

            // Barba
            ['nome' => 'Barba Completa', 'categoria' => 'Barba', 'preco' => 30.00],
            ['nome' => 'Barba Simples', 'categoria' => 'Barba', 'preco' => 15.00],
            ['nome' => 'Bigode', 'categoria' => 'Barba', 'preco' => 5.00],
            ['nome' => 'Sobrancelha', 'categoria' => 'Barba', 'preco' => 10.00],

            // Combo
            ['nome' => 'Cabelo e Barba', 'categoria' => 'Combo', 'preco' => 55.00],
            ['nome' => 'Cabelo, Barba e Sobrancelha', 'categoria' => 'Combo', 'preco' => 60.00],
            ['nome' => 'Corte Social, Risquinho / Desenho', 'categoria' => 'Combo', 'preco' => 33.00],

            // Facial & Cuidados
            ['nome' => 'Limpeza Ozônio & Lanbena', 'categoria' => 'Facial & Cuidados', 'preco' => 35.00],
            ['nome' => 'Limpeza Máscara Black', 'categoria' => 'Facial & Cuidados', 'preco' => 15.00],
            ['nome' => 'Hidratação', 'categoria' => 'Facial & Cuidados', 'preco' => 15.00],
            ['nome' => 'Limpeza Nasal / Orelha', 'categoria' => 'Facial & Cuidados', 'preco' => 10.00],
            ['nome' => 'Hidratação na Barba', 'categoria' => 'Facial & Cuidados', 'preco' => 10.00],
            ['nome' => 'Escova', 'categoria' => 'Facial & Cuidados', 'preco' => 10.00],

            // Química & Cores
            ['nome' => 'Selagem', 'categoria' => 'Química & Cores', 'preco' => 70.00],
            ['nome' => 'Botox', 'categoria' => 'Química & Cores', 'preco' => 65.00],
            ['nome' => 'Luzes', 'categoria' => 'Química & Cores', 'preco' => 60.00],
            ['nome' => 'Camuflagem Cabelo', 'categoria' => 'Química & Cores', 'preco' => 20.00],
            ['nome' => 'Camuflagem Barba', 'categoria' => 'Química & Cores', 'preco' => 20.00],
            ['nome' => 'Pigmentação', 'categoria' => 'Química & Cores', 'preco' => 10.00],
        ];

        foreach ($servicos as $servico) {
            Servico::firstOrCreate(['nome' => $servico['nome']], $servico);
        }
    }
}