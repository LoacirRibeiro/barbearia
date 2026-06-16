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
        'foto', 
    ];

    public function agendamentos()
    {
        // Um Barbeiro TEM MUITOS Agendamentos
        // (Ajuste 'barbeiro_id' ou 'barbeiro' se o nome da coluna de vínculo na sua tabela for diferente)
        return $this->hasMany(Agendamento::class, 'barbeiro_id'); 
    }

    public function setFotoAttribute($value)
    {
        $attribute_name = "foto";
        $disk = "public"; 
        $destination_path = "uploads/barbeiros"; 

        // Se o usuário limpou a imagem no painel admin
        if ($value == null) {
            if ($this->{$attribute_name}) {
                \Storage::disk($disk)->delete($this->{$attribute_name});
            }
            $this->attributes[$attribute_name] = null;
            return;
        }

        // Se um arquivo válido estiver sendo enviado por upload
        if (is_file($value) || (is_object($value) && get_class($value) == 'Illuminate\Http\UploadedFile')) {
            
            // Deleta o arquivo antigo se ele existir no disco
            if ($this->{$attribute_name}) {
                \Storage::disk($disk)->delete($this->{$attribute_name});
            }
            
            // Salva o arquivo no disco 'public' dentro da pasta 'uploads/barbeiros'
            $path = $value->store($destination_path, $disk);
            
            // Grava no banco apenas o caminho correto relativo (ex: uploads/barbeiros/nome.jpg)
            $this->attributes[$attribute_name] = $path;
        } else {
            // Se o valor já for o caminho existente no banco, apenas mantém
            $this->attributes[$attribute_name] = $value;
        }
    }
}
