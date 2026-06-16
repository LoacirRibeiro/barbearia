<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class FotoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Foto::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/foto');
        CRUD::setEntityNameStrings('foto', 'galeria de fotos');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'  => 'titulo',
            'label' => 'Título da Foto',
            'type'  => 'text',
        ]);

        // Mostra uma miniatura da imagem diretamente na listagem do painel
        CRUD::addColumn([
            'name'      => 'caminho',
            'label'     => 'Imagem',
            'type'      => 'image',
            'prefix'    => 'storage/',
            'height'    => '80px',
            'width'     => '110px',
        ]);

        CRUD::addColumn([
            'name'  => 'ativo',
            'label' => 'Visível no Site',
            'type'  => 'boolean',
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'caminho' => 'required', // A foto é obrigatória ao criar
        ]);

        CRUD::addField([
            'name'  => 'titulo',
            'label' => 'Título / Legenda (Opcional)',
            'type'  => 'text',
        ]);

        // Campo de upload corrigido para a versão gratuita (Nativa)
        CRUD::addField([
            'label'  => "Selecione a Imagem",
            'name'   => "caminho",
            'type'   => 'upload',
            'upload' => true,
            'disk'   => 'public',
        ]);

        CRUD::addField([
            'name'    => 'ativo',
            'label'   => 'Ativa (Exibir na Home)',
            'type'    => 'checkbox',
            'default' => true,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}