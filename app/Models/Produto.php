<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use CrudTrait;
    protected $fillable = ['nome', 'preco_venda', 'estoque'];
}
