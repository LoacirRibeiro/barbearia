<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Foto extends Model
{
    use CrudTrait;

    protected $table = 'fotos';
    protected $fillable = ['titulo', 'caminho', 'ativo'];

    /**
     * Mutator para tratar o upload da foto vinda do Backpack (Base64)
     */
    public function setCaminhoAttribute($value)
    {
        $attribute_name = "caminho";
        $disk = "public"; 
        $destination_path = "uploads/galeria"; 

        // 1. Se o usuário limpou a imagem ou não enviou nada
        if ($value == null) {
            if ($this->{$attribute_name}) {
                \Storage::disk($disk)->delete($this->{$attribute_name});
            }
            $this->attributes[$attribute_name] = null;
            return;
        }

        // 2. Intercepta o arquivo real vindo pelo upload do formulário
        if (request()->hasFile($attribute_name)) {
            $file = request()->file($attribute_name);
            
            if ($file->isValid()) {
                // Deleta a foto anterior se ela existir para não acumular lixo no storage
                if ($this->{$attribute_name}) {
                    \Storage::disk($disk)->delete($this->{$attribute_name});
                }
                
                // Salva o arquivo fisicamente na pasta storage/app/public/uploads/galeria
                $path = $file->store($destination_path, $disk);
                
                // Grava no banco apenas o caminho limpo: uploads/galeria/nome_gerado.png
                $this->attributes[$attribute_name] = $path;
                return;
            }
        }

        // 3. Mantém o caminho original caso seja uma atualização de outros campos sem mudar de foto
        if (is_string($value) && !\Str::contains($value, 'tmp')) {
            $this->attributes[$attribute_name] = $value;
        }
    }
}