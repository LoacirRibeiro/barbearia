<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaixaItem extends Model
{
    // Mapeia explicitamente para a tabela que você criou na migration
    protected $table = 'caixa_itens';

    // Libera os campos para receberem os dados individuais salvos pelo laço do Controller
    protected $fillable = [
        'caixa_id',
        'item_id',    // ID do serviço ou do produto
        'tipo',       // 'servico' ou 'produto'
        'descricao',  // Nome do item (ex: Pomada, Degradê)
        'quantidade', 
        'preco_unitario',
        'subtotal'
    ];

    // Relacionamento reverso para saber a qual venda/caixa este item pertence
    public function caixa()
    {
        return $this->belongsTo(Caixa::class, 'caixa_id');
    }
}