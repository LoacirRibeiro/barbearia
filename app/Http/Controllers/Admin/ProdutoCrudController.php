<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ProdutoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Produto::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/produto');
        CRUD::setEntityNameStrings('produto', 'produtos');
    }

    // Configuração da Tabela (Listagem)
    protected function setupListOperation()
    {
        CRUD::column('nome')->label('Nome do Produto');
        
        CRUD::column('preco_venda')
            ->type('decimal')
            ->label('Preço de Venda')
            ->prefix('R$ ');

        CRUD::column('estoque')
            ->type('number')
            ->label('Estoque Atual')
            ->wrapper([
                'class' => function ($crud, $column, $entry, $related_key) {
                    return $entry->estoque <= 5 ? 'text-danger font-weight-bold' : ''; // Avisa se o estoque estiver baixo
                },
            ]);
    }

    // Configuração do Formulário (Criar e Editar)
    protected function setupCreateOperation()
    {
        CRUD::field('nome')->type('text')->label('Nome do Produto');

        CRUD::field('preco_venda')
            ->type('number')
            ->label('Preço de Venda')
            ->attributes(["step" => "0.01"]); // Permite centavos

        CRUD::field('estoque')
            ->type('number')
            ->label('Quantidade em Estoque');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
