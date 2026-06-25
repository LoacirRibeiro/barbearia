<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaixaSessao extends Model
{
    protected $table = 'caixa_sessoes';
    protected $fillable = [
        'user_id', 
        'valor_abertura', 
        'valor_fechamento_calculado', 
        'valor_fechamento_real', 
        'diferenca', 
        'status', 
        'fechado_em'
        ];

    public function movimentacoes() {
        return $this->hasMany(CaixaMovimentacao::class, 'caixa_sessao_id');
    }
}