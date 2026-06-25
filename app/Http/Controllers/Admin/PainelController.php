<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barbeiro;
use App\Models\Caixa;
use App\Models\ServicoRealizado;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PainelController extends Controller
{
    /**
     * Aplica os filtros de data na query do Caixa de forma padronizada
     */
    private function aplicarFiltrosData(Request $request, &$statsFinanceiras)
    {
        $mesAno = $request->get('mes_ano', date('m/Y'));
        [$mes, $ano] = explode('/', $mesAno);
        $diaEspecifico = $request->get('dia_especifico');

        $queryCaixa = Caixa::with(['barbeiro', 'servico', 'itens']);

        if (!empty($diaEspecifico)) {
            try {
                $dataFiltro = Carbon::createFromFormat('d/m/Y', $diaEspecifico)->format('Y-m-d');
                $queryCaixa->whereDate('created_at', $dataFiltro);
                $statsFinanceiras['dia_selecionado_formatado'] = $diaEspecifico;
            } catch (\Exception $e) {
                $queryCaixa->whereMonth('created_at', $mes)->whereYear('created_at', $ano);
                $statsFinanceiras['dia_selecionado_formatado'] = '';
            }
        } else {
            $queryCaixa->whereMonth('created_at', $mes)->whereYear('created_at', $ano);
            $statsFinanceiras['dia_selecionado_formatado'] = '';
        }

        return $queryCaixa->latest()->get();
    }

    /**
     * MÉTODOS DO PAINEL PRINCIPAL (HOME)
     */
    public function index(Request $request)
    {
        $statsFinanceiras = [];
        $vendasFiltradas = $this->aplicarFiltrosData($request, $statsFinanceiras);

        $faturamentoTotal = 0;
        $totalProdutos = 0;
        $totalServicos = 0;
        $totalAtendimentos = 0;
        $quantidadeProdutosVendidos = 0;

        $atendimentosDoPeriodo = [];

        foreach ($vendasFiltradas as $venda) {
            $faturamentoTotal += $venda->valor_pago;
            
            if ($venda->itens && $venda->itens->count() > 0) {
                foreach ($venda->itens as $itemFilho) {
                    $isProduto = isset($itemFilho->tipo) && $itemFilho->tipo === 'produto';

                    if ($isProduto) {
                        $totalProdutos += $itemFilho->subtotal;
                        $quantidadeProdutosVendidos += $itemFilho->quantidade;
                    } else {
                        $totalServicos += $itemFilho->subtotal;
                        $totalAtendimentos++;
                    }

                    $atendimentosDoPeriodo[] = (object)[
                        'created_at' => $venda->created_at->toDateTimeString(),
                        'tipo'       => $itemFilho->tipo ?? 'servico',
                        'descricao'  => $itemFilho->descricao,
                        'quantidade' => $itemFilho->quantidade,
                        'subtotal'   => $itemFilho->subtotal
                    ];
                }
            } else {
                $totalServicos += $venda->valor_pago;
                $totalAtendimentos++;

                $atendimentosDoPeriodo[] = (object)[
                    'created_at' => $venda->created_at->toDateTimeString(),
                    'tipo'       => 'servico',
                    'descricao'  => $venda->servico->nome ?? 'Serviço Direto',
                    'quantidade' => 1,
                    'subtotal'   => $venda->valor_pago
                ];
            }
        }

        // Calcula a comissão real gerada no período buscado
        $mesAno = $request->get('mes_ano', date('m/Y'));
        [$mes, $ano] = explode('/', $mesAno);
        
        $comissaoRealPeriodo = ServicoRealizado::whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('comissao_valor');

        $statsFinanceiras['faturamento_total'] = $faturamentoTotal;
        $statsFinanceiras['total_produtos'] = $totalProdutos;
        $statsFinanceiras['total_atendimentos'] = $totalAtendimentos;
        $statsFinanceiras['comissao_total'] = $comissaoRealPeriodo;
        $statsFinanceiras['lucro_barbearia'] = ($faturamentoTotal - $comissaoRealPeriodo);
        $statsFinanceiras['ticket_medio'] = $vendasFiltradas->count() > 0 ? ($faturamentoTotal / $vendasFiltradas->count()) : 0;
        
        $statsFinanceiras['quantidade_produtos_vendidos'] = $quantidadeProdutosVendidos;
        $statsFinanceiras['ticket_medio_produtos'] = $quantidadeProdutosVendidos > 0 ? ($totalProdutos / $quantidadeProdutosVendidos) : 0;

        $qtdTotalServicos = $totalAtendimentos;
        $ticketMedioServicos = $totalAtendimentos > 0 ? ($totalServicos / $totalAtendimentos) : 0;

        return view('admin.painel', compact('statsFinanceiras', 'atendimentosDoPeriodo', 'qtdTotalServicos', 'ticketMedioServicos'));
    }
   
    /**
     * GESTÃO DE COLABORADORES
     */
    public function colaboradores(Request $request)
    {
        $barbeiros = Barbeiro::all();
        $barbeirosRelatorio = [];

        $isBuscaMensal = !$request->filled('dia_especifico');

        if (!$isBuscaMensal) {
            $dataInicio = Carbon::createFromFormat('d/m/Y', $request->dia_especifico)->startOfDay();
            $dataFim = Carbon::createFromFormat('d/m/Y', $request->dia_especifico)->endOfDay();
        } else {
            $mesAno = $request->get('mes_ano', date('m/Y'));
            $partes = explode('/', $mesAno);
            $dataInicio = Carbon::createFromDate($partes[1], $partes[0], 1)->startOfMonth()->startOfDay();
            $dataFim = Carbon::createFromDate($partes[1], $partes[0], 1)->endOfMonth()->endOfDay();
        }

        foreach ($barbeiros as $barbeiro) {
            $detalhesServicos = ServicoRealizado::where('barbeiro_id', $barbeiro->id)
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->get();

            // Comissão de serviços pendentes (Trabalhos Aguardando Repasse)
            $comissaoAberta = $detalhesServicos->whereNull('pagamento_id')->sum('comissao_valor');
            $comissaoBrutaGeral = $detalhesServicos->sum('comissao_valor');

            // Busca os adiantamentos do período
            $todosAdiantamentos = Pagamento::where('barbeiro_id', $barbeiro->id)
                ->where('tipo_pagamento', 'adiantamento')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->get();

            // Filtra os adiantamentos ativos (sem a string de fechamento manual)
            $adiantamentosAtivosIniciais = $todosAdiantamentos->filter(function($pagamento) {
                return false === stripos($pagamento->observacoes, 'descontado no fechamento');
            })->sum('valor');

            // O valor exibido de adiantamento sempre será o valor bruto inteiro (ex: R$ 100,00 fixo)
            $totalAdiantamentosExibicao = $adiantamentosAtivosIniciais;

            // O saldo líquido calcula a comissão aberta MENOS o adiantamento total, permitindo valores negativos
            $comissaoReceberLiquida = $comissaoAberta - $adiantamentosAtivosIniciais;

            // Histórico completo para a aba mensal
            $historicoAdiantamentosMes = $todosAdiantamentos->sum('valor');

            $barbeirosRelatorio[] = [
                'id'                      => $barbeiro->id,
                'nome'                    => $barbeiro->nome ?? $barbeiro->name, 
                'cargo'                   => $barbeiro->tipo ?? $barbeiro->cargo ?? 'colaborador',
                'comissao_bruta'          => $comissaoBrutaGeral, 
                'total_adiantamentos'     => $totalAdiantamentosExibicao, // Fica inteiro (R$ 100,00) e não diminui com o tempo
                'comissao_receber'        => $comissaoReceberLiquida,    // Fica negativo se o adiantamento for maior (ex: -R$ 70,00)
                'total_adiantamentos_mes' => $historicoAdiantamentosMes, // Histórico intocado da aba mensal
                'faturamento_comum'       => $detalhesServicos->sum('preco'),
                'total_comuns'            => $detalhesServicos->count(), 
                'detalhes_servicos'       => $detalhesServicos 
            ];
        }

        return view('admin.colaboradores', compact('barbeirosRelatorio'));
    }

    /**
     * GRÁFICO: Evolução Mensal Comparativa de Atendimentos
     */
    public function evolucao(Request $request)
    {
        $mesAno = $request->get('mes_ano_filtro', date('m/Y'));
        [$mesFiltro, $anoFiltro] = explode('/', $mesAno);

        $barbeiros = Barbeiro::all();
        $barbeirosRelatorioData = [];

        foreach ($barbeiros as $barbeiro) {
            $detalhesServicos = ServicoRealizado::where('barbeiro_id', $barbeiro->id)
                ->whereMonth('created_at', $mesFiltro)
                ->whereYear('created_at', $anoFiltro)
                ->select('id', 'created_at', 'preco') 
                ->get();

            $barbeirosRelatorioData[] = [
                'id'                => $barbeiro->id,
                'nome'              => $barbeiro->nome ?? $barbeiro->name,
                'detalhes_servicos' => $detalhesServicos
            ];
        }

        $produtosGerais = Caixa::with('itens')
            ->whereMonth('created_at', $mesFiltro)
            ->whereYear('created_at', $anoFiltro)
            ->whereHas('itens', function($query) {
                $query->where('tipo', 'produto');
            })->get();

        return view('admin.evolucao', compact('barbeirosRelatorioData', 'produtosGerais'));
    }
}