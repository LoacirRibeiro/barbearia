<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Caixa;
use App\Models\CaixaItem;
use App\Models\Produto;
use App\Models\Barbeiro;
use App\Models\Servico;
use App\Models\HistoricoEstoque;
use App\Models\CaixaSessao;
use App\Models\ServicoRealizado; // ⚡ IMPORTANTE: Importando o novo Model
use Illuminate\Support\Facades\DB; 

class CaixaBalcaoController extends Controller
{
    // Exibe a tela do caixa com os dados dinâmicos do banco
    public function index()
    {
        $barbeiros = Barbeiro::all();
        $servicos = Servico::all();
        // Busca apenas produtos que possuem pelo menos 1 unidade em estoque
        $produtos = Produto::where('estoque', '>', 0)->get(); 
        $caixaAberto = CaixaSessao::where('status', 'aberto')->first();

        return view('admin.caixa', compact('barbeiros', 'servicos', 'produtos', 'caixaAberto'));
    }

    // Processa, valida estoque e salva a venda realizada
    public function salvar(Request $request)
    {
        // 1. Validação básica de segurança
        $request->validate([
            'barbeiro_id'     => 'required|integer',
            'forma_pagamento' => 'required|string',
            'itens_json'      => 'required|string',
        ]);

        // 2. Transforma o JSON do Javascript em Array do PHP
        $carrinho = json_decode($request->input('itens_json'), true);

        if (empty($carrinho)) {
            return redirect()->back()->withErrors(['itens_json' => 'Adicione pelo menos um item ao carrinho antes de finalizar.']);
        }

        // PRÉ-VALIDAÇÃO DE ESTOQUE (Evita vender o que não tem e deixar estoque negativo)
        foreach ($carrinho as $item) {
            if ($item['tipo'] === 'produto') {
                $produto = Produto::find($item['id']);
                
                if (!$produto) {
                    return redirect()->back()->withErrors(['itens_json' => "O produto '{$item['nome']}' não foi encontrado no sistema."])->withInput();
                }

                if ($produto->estoque < $item['qtd']) {
                    return redirect()->back()->withErrors([
                        'itens_json' => "Estoque insuficiente para '{$produto->nome}'. Você tentou vender {$item['qtd']} un., mas só existem {$produto->estoque} un. disponíveis."
                    ])->withInput();
                }
            }
        }

        // 3. Calcula o Valor Total Geral somando os subtotais dos itens
        $totalGeral = collect($carrinho)->sum('subtotal');
        $nomeCliente = $request->input('nome_cliente') ?? 'Cliente Balcão';
        $barbeiroId = $request->input('barbeiro_id');

        // ⚡ Usando Database Transactions para garantir consistência total entre tabelas e estoque
        DB::beginTransaction();

        try {
            // 4. Salva o registro MACRO na tabela 'caixas'
            $caixaMestre = Caixa::create([
                'nome_cliente'    => $nomeCliente,
                'barbeiro_id'     => $barbeiroId,
                'forma_pagamento' => $request->input('forma_pagamento'),
                'valor_pago'      => $totalGeral
            ]);

            // 5. Salva cada item individualmente e dá a baixa
            foreach ($carrinho as $item) {
                $caixaItemSalvo = CaixaItem::create([
                    'caixa_id'       => $caixaMestre->id,
                    'item_id'        => $item['id'],
                    'tipo'           => $item['tipo'], // 'servico' ou 'produto'
                    'descricao'      => $item['nome'],
                    'quantidade'     => $item['qtd'],
                    'preco_unitario' => $item['preco'],
                    'subtotal'       => $item['subtotal']
                ]);

                // ⚡ NOVA REGRA: Se o item for um serviço, joga para a auditoria de comissões pendentes
                if ($item['tipo'] === 'servico') {
                    // Descobrir a porcentagem de comissão do barbeiro (padrão 50% = 0.50)
                    $barbeiro = Barbeiro::find($barbeiroId);
                    $porcentagem = ($barbeiro && in_array($barbeiro->tipo, ['proprietario', 'gestor'])) ? 1.00 : 0.50;

                    ServicoRealizado::create([
                        'barbeiro_id'    => $barbeiroId,
                        'descricao'      => $item['nome'],
                        'preco'          => $item['subtotal'], // Salva o valor gerado (multiplicado pela qtd)
                        'comissao_valor' => $item['subtotal'] * $porcentagem,
                        'pagamento_id'   => null, // Nasce sem estar pago
                        'caixa_item_id'  => $caixaItemSalvo->id
                    ]);
                }

                // 6. Se for um produto físico, realiza a baixa automática no estoque
                if ($item['tipo'] === 'produto') {
                    $produto = Produto::find($item['id']);
                    if ($produto) {
                        // Dá a baixa no estoque do produto
                        $produto->decrement('estoque', $item['qtd']);

                        // Grava o rastro da SAÍDA no histórico de auditoria
                        HistoricoEstoque::create([
                            'produto_id' => $produto->id,
                            'user_id'    => auth()->id(), // ID do usuário logado que realizou a venda
                            'tipo'       => 'saida',
                            'quantidade' => $item['qtd'],
                            'motivo'     => "Venda Balcão - Cliente: " . $nomeCliente . " (Cód. Caixa: #{$caixaMestre->id})"
                        ]);
                    }
                }
            }

            // Se tudo correu bem, confirma as alterações no banco de dados
            DB::commit();

            return redirect()->route('admin.caixa')->with('sucesso', 'Atendimento e venda balcão registrados com absoluto sucesso!');

        } catch (\Exception $e) {
            // Se der qualquer erro interno, desfaz tudo o que foi feito para não quebrar o estoque e as comissões
            DB::rollBack();
            return redirect()->back()->withErrors(['itens_json' => 'Ocorreu um erro ao processar a venda. O estoque não foi alterado. Erro: ' . $e->getMessage()])->withInput();
        }
    }
}