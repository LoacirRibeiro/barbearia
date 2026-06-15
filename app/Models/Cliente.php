<?php

namespace App\Models;
use Backpack\CRUD\app\Models\Traits\CrudTrait; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Cliente extends Model
{
    use HasFactory;
    use CrudTrait;
    

    // 🔓 Adicione os campos que o formulário/controller podem preencher
    protected $fillable = [
        'nome',
        'telefone',
        'email',
    ];

    // Se houver relacionamentos ou outras funções abaixo, pode mantê-los normalmente...
}