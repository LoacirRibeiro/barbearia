<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Carbon\Carbon;

class CaixaRelatorioController extends Controller
{
    public function relatorioMensal(Request $request)
    {
        // Define o idioma do Carbon explicitamente para português
        Carbon::setLocale('pt_BR');

        // Captura os filtros da requisição
        $filtroData = $request->input('data'); 
        $mes = $request->input('mes', Carbon::now()->month);
        $ano = $request->input('ano', Carbon::now()->year);

        // Inicializa as queries base limpas
        $queryVendas = Caixa::query();
        $queryMovimentacoes = CaixaMovimentacao::query();
        $queryFluxoDiario = Caixa::query(); // 👈 Limpado o join daqui para evitar duplicidade de tabelas

        // Aplica o filtro por DIA ou por MÊS/ANO de forma segura
        if ($filtroData) {
            $dataCarbon = Carbon::parse($filtroData);
            $mes = $dataCarbon->month;
            $ano = $dataCarbon->year;

            $queryVendas->whereDate('caixas.created_at', $filtroData);
            $queryMovimentacoes->whereDate('created_at', $filtroData);
            $queryFluxoDiario->whereDate('caixas.created_at', $filtroData);
        } else {
            $queryVendas->whereMonth('caixas.created_at', $mes)->whereYear('caixas.created_at', $ano);
            $queryMovimentacoes->whereMonth('created_at', $mes)->whereYear('created_at', $ano);
            $queryFluxoDiario->whereMonth('caixas.created_at', $mes)->whereYear('caixas.created_at', $ano);
        }

        // 1. Totais por Forma de Pagamento
        $faturamentoFormas = $queryVendas
            ->select('forma_pagamento', DB::raw('SUM(valor_pago) as total'))
            ->groupBy('forma_pagamento')
            ->get()
            ->pluck('total', 'forma_pagamento')
            ->toArray();

        $formas = ['Dinheiro' => 0, 'Pix' => 0, 'Cartão de Débito' => 0, 'Cartão de Crédito' => 0];
        $entradasVendas = array_merge($formas, $faturamentoFormas);
        $totalVendas = array_sum($entradasVendas);

        // 2. Movimentações de Gaveta (Suprimentos e Sangrias)
        $movimentacoes = $queryMovimentacoes
            ->select('tipo', DB::raw('SUM(valor) as total'))
            ->groupBy('tipo')
            ->get()
            ->pluck('total', 'tipo')
            ->toArray();

        $totalSuprimentos = $movimentacoes['suprimento'] ?? 0;
        $totalSangrias = $movimentacoes['sangria'] ?? 0;

        $faturamentoBrutoMês = $totalVendas; 
        
        // 3. Detalhamento Diário por Operador (Encadeamento correto via caixa_sessoes)
        $fluxoDiario = $queryFluxoDiario
            ->join('users', 'caixas.barbeiro_id', '=', 'users.id') // 👈 Ligação direta correta!
            ->select(
                DB::raw('DATE(caixas.created_at) as data'),
                'users.name as operador_nome',
                DB::raw("SUM(CASE WHEN forma_pagamento = 'Dinheiro' THEN valor_pago ELSE 0 END) as dinheiro"),
                DB::raw("SUM(CASE WHEN forma_pagamento = 'Pix' THEN valor_pago ELSE 0 END) as pix"),
                DB::raw("SUM(CASE WHEN forma_pagamento = 'Cartão de Débito' THEN valor_pago ELSE 0 END) as debito"),
                DB::raw("SUM(CASE WHEN forma_pagamento = 'Cartão de Crédito' THEN valor_pago ELSE 0 END) as credito"),
                DB::raw('SUM(valor_pago) as total_dia')
            )
            ->groupBy(DB::raw('DATE(caixas.created_at)'), 'users.name')
            ->orderBy('data', 'DESC')
            ->orderBy('total_dia', 'DESC')
            ->get();

        // Lista de meses traduzidos em português para o select da View
        $mesesPortuguis = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        return view('admin.relatorio_mensal', compact(
            'entradasVendas', 'totalVendas', 'totalSuprimentos', 
            'totalSangrias', 'faturamentoBrutoMês', 'fluxoDiario', 
            'mes', 'ano', 'filtroData', 'mesesPortuguis'
        ));
    }
}