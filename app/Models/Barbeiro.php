<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Barbeiro extends Model
{
    use CrudTrait;
    protected $table = 'barbeiros';

    // 🇧🇷 ADICIONE ESSA PROPRIEDADE AQUI:
    protected $fillable = [
        'nome',
        'especialidade',
    ];

    public function agendamentos()
    {
        // Um Barbeiro TEM MUITOS Agendamentos
        // (Ajuste 'barbeiro_id' ou 'barbeiro' se o nome da coluna de vínculo na sua tabela for diferente)
        return $this->hasMany(Agendamento::class, 'barbeiro_id'); 
    }
}
