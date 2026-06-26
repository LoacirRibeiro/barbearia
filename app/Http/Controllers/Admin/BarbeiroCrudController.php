<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BarbeiroRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BarbeiroCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BarbeiroCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Barbeiro::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/barbeiro');
        CRUD::setEntityNameStrings('barbeiro', 'barbeiros');
    }

    /**
     * Define what happens when the List operation is loaded.
     * * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // Define colunas automaticamente do banco de dados

        // Customiza a exibição da coluna 'tipo' para mostrar nomes amigáveis em vez da string pura
        CRUD::modifyColumn('tipo', [
            'type'    => 'select_from_array',
            'label'   => 'Cargo / Vínculo',
            'options' => [
                'colaborador'  => 'Colaborador',
                'proprietario' => 'Sócio / Proprietário',
                'gestor'       => 'Gerente / Gestor',
            ]
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(BarbeiroRequest::class);
        CRUD::setFromDb(); // Configura os campos automaticamente a partir do banco

        // Remove os campos originais do setFromDb para evitar conflitos e inputs duplicados
        CRUD::removeField('foto');
        CRUD::removeField('tipo');

        // Adiciona novamente o campo 'foto', configurado de forma isolada e correta
        CRUD::addField([
            'label'      => "Foto do Barbeiro",
            'name'       => "foto",
            'type'       => 'upload',
            'upload'     => true,
            'disk'       => 'public', 
            'prefix'     => 'uploads/barbeiros/', 
            'wrapper'    => [
                'class' => 'form-group col-md-6'
            ]
        ]);

        // Adiciona novamente o campo 'tipo' estilizado como um Dropdown de seleção
        CRUD::addField([
            'name'        => 'tipo',
            'label'       => 'Tipo de Vínculo / Cargo',
            'type'        => 'select_from_array',
            'options'     => [
                'colaborador'  => 'Colaborador (Comissão 50%)',
                'proprietario' => 'Sócio / Proprietário (Comissão 100%)',
                'gestor'       => 'Gerente / Gestor (Comissão 100%)',
            ],
            'allows_null' => false,
            'default'     => 'colaborador',
            'wrapper'     => [
                'class' => 'form-group col-md-6'
            ]
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}