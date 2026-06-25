<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            // 🧴 Produtos de Cosmética / Revenda
            [
                'nome' => 'Pomada Modeladora Efeito Matte 100g',
                'preco_venda' => 45.00,
                'estoque' => 15,
            ],
            [
                'nome' => 'Óleo para Barba Hidratante 30ml',
                'preco_venda' => 39.90,
                'estoque' => 10,
            ],
            [
                'nome' => 'Shampoo Sebastian Beard 250ml',
                'preco_venda' => 65.00,
                'estoque' => 8,
            ],
            [
                'nome' => 'Balm Alinhador de Barba 90g',
                'preco_venda' => 34.90,
                'estoque' => 12,
            ],

            // 🍺 Consumo / Bebidas (Gera muito giro no caixa)
            [
                'nome' => 'Cerveja Heineken Long Neck 330ml',
                'preco_venda' => 12.00,
                'estoque' => 48,
            ],
            [
                'nome' => 'Coca-Cola Lata 350ml',
                'preco_venda' => 6.00,
                'estoque' => 30,
            ],
            [
                'nome' => 'Água Mineral sem Gás 500ml',
                'preco_venda' => 4.00,
                'estoque' => 25,
            ],
            [
                'nome' => 'Energético Monster 473ml',
                'preco_venda' => 14.00,
                'estoque' => 15,
            ],
        ];

        foreach ($produtos as $produto) {
            Produto::create($produto);
        }
    }
}