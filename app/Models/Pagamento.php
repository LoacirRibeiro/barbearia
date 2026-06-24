<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $table = 'pagamentos';

    protected $fillable = [
        'barbeiro_id',
        'valor',
        'data_inicio_periodo',
        'data_fim_periodo',
        'tipo_periodo',
        'observacoes',
        'tipo_pagamento'
    ];

    public function Barbeiro()
    {
        return $this->belongsTo(Barbeiro::class);
    }
}