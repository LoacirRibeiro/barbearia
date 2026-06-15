<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PlanoRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PlanoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PlanoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Plano::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/plano');
        CRUD::setEntityNameStrings('plano', 'planos');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Define quais colunas aparecem na tabela de listagem do painel
        CRUD::column('nome')->type('text')->label('Nome do Plano');
        CRUD::column('preco')->type('number')->label('Preço (R$)')->prefix('R$ ');
        CRUD::column('limite_cortes')->type('number')->label('Cortes /mês');
        CRUD::column('limite_barba')->type('number')->label('Barbas /mês');
        CRUD::column('ativo')->type('boolean')->label('Status Ativo');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'nome' => 'required|min:3|max:255',
            'preco' => 'required|numeric|min:0',
            'limite_cortes' => 'required|integer|min:0',
            'limite_barba' => 'required|integer|min:0',
            'ativo' => 'boolean'
        ]);

        // Campo de texto para o nome
        CRUD::field('nome')->type('text')->label('Nome do Plano')->attributes(['placeholder' => 'Ex: Plano Hair VIP, Combo Executivo']);
        
        // Campo numérico para o Preço
        CRUD::field('preco')->type('number')->label('Preço Mensal (R$)')->attributes(['step' => '0.01']);
        
        // Descrição em área de texto
        CRUD::field('descricao')->type('textarea')->label('Descrição / Regras do Contrato');
        
        // Limites de uso no mês (0 para ilimitado)
        CRUD::field('limite_cortes')
            ->type('number')
            ->label('Limite de Cortes de Cabelo por mês')
            ->hint('Digite 0 para cortes ILIMITADOS');
            
        CRUD::field('limite_barba')
            ->type('number')
            ->label('Limite de Serviços de Barba por mês')
            ->hint('Digite 0 para barbas ILIMITADAS');

        // Switch/Checkbox de Ativo
        CRUD::field('ativo')->type('checkbox')->label('Plano está disponível para contratação?')->default(true);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}