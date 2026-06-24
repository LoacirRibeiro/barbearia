<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use CrudTrait;

    protected $table = 'caixas';

    // 🌟 Atualizado: Removido servico_id pois múltiplos itens estarão na tabela filha
    protected $fillable = [
        'nome_cliente', 
        'barbeiro_id', 
        'forma_pagamento', 
        'valor_pago' // Aqui guardaremos o Total Geral do carrinho somado
    ];

    // Relacionamento com Barbeiro
    public function barbeiro()
    {
        return $this->belongsTo(Barbeiro::class, 'barbeiro_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    // 🌟 NOVO: Relacionamento com os itens detalhados deste lançamento de caixa
    public function itens()
    {
        return $this->hasMany(CaixaItem::class, 'caixa_id');
    }
}