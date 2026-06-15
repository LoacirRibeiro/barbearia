<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AgendamentoRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AgendamentoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AgendamentoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Agendamento::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/agendamento');
        CRUD::setEntityNameStrings('agendamento', 'agendamentos');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
{
    CRUD::setFromDb();

    // Mostra o nome do Barbeiro na tabela, em vez do número do ID
    CRUD::modifyColumn('barbeiro_id', [
        'label'     => 'Barbeiro',
        'type'      => 'select',
        'entity'    => 'barbeiro',
        'model'     => "App\Models\Barbeiro",
        'attribute' => 'nome',
    ]);

    // Mostra o nome do Cliente na tabela, em vez do número do ID
    CRUD::modifyColumn('cliente_id', [
        'label'     => 'Cliente',
        'type'      => 'select',
        'entity'    => 'cliente',
        'model'     => "App\Models\Cliente",
        'attribute' => 'nome',
    ]);
}

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
{
    CRUD::setFromDb(); // Puxa os campos padrão

    // Muda o campo do Barbeiro para um Select de verdade
    CRUD::modifyField('barbeiro_id', [
        'label'     => "Barbeiro",
        'type'      => 'select',
        'entity'    => 'barbeiro',
        'model'     => "App\Models\Barbeiro",
        'attribute' => 'nome',
    ]);

    // Muda o campo do Cliente para um Select de verdade
    CRUD::modifyField('cliente_id', [
        'label'     => "Cliente",
        'type'      => 'select',
        'entity'    => 'cliente',
        'model'     => "App\Models\Cliente",
        'attribute' => 'nome',
    ]);
}

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
