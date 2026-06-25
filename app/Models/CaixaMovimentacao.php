<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaixaMovimentacao extends Model
{
    protected $table = 'caixa_movimentacoes';
    protected $fillable = ['caixa_sessao_id', 'user_id', 'tipo', 'valor', 'motivo'];

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
}