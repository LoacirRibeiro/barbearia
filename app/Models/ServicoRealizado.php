<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoRealizado extends Model
{
    protected $table = 'servicos_realizados';

    protected $fillable = [
        'barbeiro_id',
        'descricao',
        'preco',
        'comissao_valor',
        'pagamento_id',
        'caixa_item_id'
    ];

    public function barbeiro()
    {
        return $this->belongsTo(Barbeiro::class);
    }
}