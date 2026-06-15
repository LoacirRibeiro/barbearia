<?php

namespace Database\Seeders;

use App\Models\Plano;
use Illuminate\Database\Seeder;

class PlanoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Plano 1: Apenas Cabelo
        Plano::updateOrCreate(
            ['slug' => 'plano-cabelo-vip'], // Evita duplicar se rodar duas vezes
            [
                'nome'          => 'Plano Hair VIP',
                'preco'         => 60.00,
                'descricao'     => 'Cortes de cabelo à vontade durante o mês vigente do contrato. Mantenha o degradê sempre na régua!',
                'limite_cortes' => 0, // 0 significa Ilimitado conforme nossa regra do Blade
                'limite_barba'  => 0, // Não inclui barba por padrão (ou configure limite se necessário)
                'ativo'         => true,
            ]
        );

        // Plano 2: Cabelo e Barba (Mais Vantajoso)
        Plano::updateOrCreate(
            ['slug' => 'plano-vip-club-completo'],
            [
                'nome'          => 'Plano VIP Club',
                'preco'         => 105.00,
                'descricao'     => 'Cabelo e barba completos à vontade durante todo o mês. O tratamento completo que você merece.',
                'limite_cortes' => 0, // Ilimitado
                'limite_barba'  => 0, // Ilimitado
                'ativo'         => true,
            ]
        );
    }
}