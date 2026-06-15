<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServicoRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ServicoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Servico::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/servico');
        CRUD::setEntityNameStrings('serviço', 'serviços');
    }

    /**
     * Configuração da tabela de listagem (o que aparece quando você entra na página)
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'  => 'nome',
            'label' => 'Nome do Serviço',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'categoria',
            'label' => 'Categoria',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'preco',
            'label' => 'Preço',
            'type'  => 'number',
            'prefix' => 'R$ ',
            'decimals' => 2,
            'dec_point' => ',',
            'thousands_sep' => '.',
        ]);

        CRUD::addColumn([
            'name'  => 'ativo',
            'label' => 'Status',
            'type'  => 'boolean',
            'options' => [0 => 'Inativo', 1 => 'Ativo'],
        ]);
    }

    /**
     * Configuração do Formulário de Cadastro e Edição
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServicoRequest::class);

        CRUD::addField([
            'name'  => 'nome',
            'label' => 'Nome do Serviço',
            'type'  => 'text',
        ]);

        // Criamos um select fixo com as categorias que você usa no HTML
        CRUD::addField([
            'name'        => 'categoria',
            'label'       => 'Categoria',
            'type'        => 'select_from_array',
            'options'     => [
                'Cabelo'            => 'Cabelo',
                'Barba'             => 'Barba',
                'Combo'             => 'Combo',
                'Facial & Cuidados' => 'Facial & Cuidados',
                'Química & Cores'   => 'Química & Cores',
            ],
            'allows_null' => false,
        ]);

        CRUD::addField([
            'name'  => 'preco',
            'label' => 'Preço (R$)',
            'type'  => 'number',
            'attributes' => [
                'step' => '0.01', // Permite centavos no input
                'min'  => '0',
            ],
        ]);

        CRUD::addField([
            'name'  => 'ativo',
            'label' => 'Serviço Ativo? (Aparece no site)',
            'type'  => 'checkbox',
            'default' => 1,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}