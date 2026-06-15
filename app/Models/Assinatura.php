<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    use HasFactory;

    protected $table = 'assinaturas';

    // 🌟 CORREÇÃO: Liberando os campos para inserção em massa
    protected $fillable = [
        'cliente_id',
        'plano_id',
        'data_inicio',
        'data_fim',
        'status',
    ];

    /**
     * Relacionamento: Uma assinatura pertence a um plano
     */
    public function plano()
    {
        return $this->belongsTo(Plano::class, 'plano_id');
    }

    /**
     * Relacionamento: Uma assinatura pertence a um cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    
}
