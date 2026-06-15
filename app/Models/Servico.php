<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait; 
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use CrudTrait; 

    protected $table = 'servicos';
    
    // Defina os campos que podem ser preenchidos no formulário do Backpack
    protected $fillable = [
        'nome',
        'categoria',
        'preco',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'preco' => 'decimal:2',
    ];
}