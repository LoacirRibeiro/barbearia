<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use CrudTrait;
    
    protected $fillable = ['cliente_id', 'barbeiro_id', 'servico', 'data_hora', 'status'];

    // Relacionamento: O agendamento pertence a um Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relacionamento: O agendamento foi feito por um Barbeiro
    public function barbeiro()
    {
        return $this->belongsTo(Barbeiro::class, 'barbeiro_id');
    }
}