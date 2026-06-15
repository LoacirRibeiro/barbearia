<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioDisponivel extends Model
{
    use HasFactory;

    // Define explicitamente o nome da tabela no banco de dados
    protected $table = 'horarios_disponiveis';

    // Permite a gravação em massa desses campos
    protected $fillable = [
        'barbeiro_id',
        'data_hora',
        'disponivel',
    ];

    // Converte automaticamente o campo do banco para um objeto de data Carbon do PHP
    protected $casts = [
        'data_hora' => 'datetime',
        'disponivel' => 'boolean',
    ];

    // Relacionamento: Esse horário pertence a um Barbeiro específico
    public function barbeiro()
    {
        return $this->belongsTo(Barbeiro::class, 'barbeiro_id');
    }
}