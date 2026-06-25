<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pagamento;
use App\Models\ServicoRealizado;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagamentoController extends Controller
{
    public function registrarPagamento(Request $request)
    {
        $request->validate([
            'barbeiro_id'    => 'required|exists:barbeiros,id',
            'valor'          => 'required|numeric|min:0.01',
            'tipo_periodo'   => 'required|string',
            'password'       => 'required|string',
            'data_inicio'    => 'required|date',
            'data_fim'       => 'required|date',
            'tipo_pagamento' => 'required|in:repasse,adiantamento', // Campo novo!
        ]);

        $adminLogado = auth()->user();
        if (!$adminLogado || !Hash::check($request->password, $adminLogado->password)) {
            return redirect()->back()->withErrors(['password' => 'Senha de Administrador incorreta!'])->withInput();
        }

        $dataInicio = Carbon::parse($request->data_inicio)->startOfDay();
        $dataFim = Carbon::parse($request->data_fim)->endOfDay();

        DB::beginTransaction();

        try {
            // 1. Se for apenas um ADIANTAMENTO
            if ($request->tipo_pagamento === 'adiantamento') {
                Pagamento::create([
                    'barbeiro_id'         => $request->barbeiro_id,
                    'valor'               => $request->valor,
                    'tipo_periodo'        => $request->tipo_periodo,
                    'data_inicio_periodo' => $request->data_inicio,
                    'data_fim_periodo'    => $request->data_fim,
                    'tipo_pagamento'      => 'adiantamento',
                    'observacoes'         => $request->get('observacoes', 'Adiantamento de dinheiro realizado.')
                ]);

                DB::commit();
                return redirect()->back()->with('sucesso', 'Adiantamento registrado com sucesso!');
            }

            // 2. Se for o REPASSE GERAL (Fechamento)
            // Primeiro criamos o repasse com o valor líquido enviado pelo formulário
            $pagamento = Pagamento::create([
                'barbeiro_id'         => $request->barbeiro_id,
                'valor'               => $request->valor, 
                'tipo_periodo'        => $request->tipo_periodo,
                'data_inicio_periodo' => $request->data_inicio,
                'data_fim_periodo'    => $request->data_fim,
                'tipo_pagamento'      => 'repasse',
                'observacoes'         => $request->get('observacoes', 'Fechamento geral com descontos aplicado.')
            ]);

            // Vincula e limpa os serviços pendentes da fila
            $atualizados = ServicoRealizado::where('barbeiro_id', $request->barbeiro_id)
                ->whereNull('pagamento_id')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->update(['pagamento_id' => $pagamento->id]);

            // Vincula também os adiantamentos do período a esse fechamento (para histórico)
            // Setamos uma observação ou atualizamos para saber que eles foram "reconciliados"
            Pagamento::where('barbeiro_id', $request->barbeiro_id)
                ->where('tipo_pagamento', 'adiantamento')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->update(['observacoes' => 'Adiantamento descontado no fechamento ID: ' . $pagamento->id]);

            DB::commit();
            return redirect()->back()->with('sucesso', 'Fechamento geral realizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['password' => 'Erro ao processar: ' . $e->getMessage()])->withInput();
        }
    }
}