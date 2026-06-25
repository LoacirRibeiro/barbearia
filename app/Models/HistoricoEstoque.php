<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoEstoque extends Model
{
    protected $table = 'historico_estoque';
    
    protected $fillable = [
        'produto_id',
        'user_id',
        'tipo',
        'quantidade',
        'motivo'
    ];

    public function produto() {
        return $this->belongsTo(Produto::class);
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
}