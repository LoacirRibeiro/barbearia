<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait; // 🇧🇷 Importante para o Backpack!
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plano extends Model
{
    use HasFactory, CrudTrait; // 🌟 Adicione a CrudTrait aqui dentro

    protected $table = 'planos';

    // Campos que o formulário do painel poderá preencher
    protected $fillable = [
        'nome',
        'slug',
        'preco',
        'descricao',
        'limite_cortes',
        'limite_barba',
        'ativo',
    ];

    /**
     * Mutator para gerar o slug automaticamente ao digitar o nome no painel
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($plano) {
            if (empty($plano->slug)) {
                $plano->slug = Str::slug($plano->nome);
            }
        });
    }
}