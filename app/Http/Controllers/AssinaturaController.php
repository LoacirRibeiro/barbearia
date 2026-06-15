<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Cliente;
use App\Models\Assinatura;

class AssinaturaController extends Controller
{
    public function subscreverPlanoForm($id)
    {
        $plano = Plano::findOrFail($id);
        return view('planos.confirmar', compact('plano'));
    }

    public function salvarAssinatura(Request $request)
    {
        $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'forma_pagamento' => 'required|string'
        ]);

        $usuarioLogado = auth()->user();
        
        $cliente = Cliente::firstOrCreate(
            ['email' => $usuarioLogado->email],
            [
                'nome' => $usuarioLogado->name,
                'telefone' => $usuarioLogado->telefone ?? 'Não informado'
            ]
        );

        $assinaturaAtiva = Assinatura::where('cliente_id', $cliente->id)
            ->where('status', 'Ativo')
            ->where('data_fim', '>=', now()->toDateString())
            ->first();

        if ($assinaturaAtiva) {
            return redirect('/')->withErrors(['erro' => 'Você já possui um plano ativo!']);
        }

        $formaPagamento = $request->forma_pagamento;
        $statusAssinatura = 'Inativo';
        $statusPagamento = 'Pendente';

        Assinatura::create([
            'cliente_id'       => $cliente->id,
            'plano_id'         => $request->plano_id,
            'forma_pagamento'  => $formaPagamento,
            'status_pagamento' => $statusPagamento,
            'data_inicio'      => now()->toDateString(),
            'data_fim'         => now()->addDays(30)->toDateString(),
            'status'           => $statusAssinatura,
            'gateway_id'       => ($formaPagamento === 'Cartão de Crédito') ? 'mock_id_' . uniqid() : null,
        ]);

        if ($formaPagamento === 'Dinheiro/Balcão') {
            return redirect('/')->with('sucesso', 'Solicitação realizada! Vá ao balcão para realizar o pagamento e liberar seus benefícios.');
        }

        return redirect('/')->with('sucesso', 'Plano contratado com sucesso! Aproveite seus benefícios.');
    }

    public function detalhesPlano($id)
    {
        $assinatura = Assinatura::where('id', $id)
            ->where('cliente_id', function($query) {
                $query->select('id')->from('clientes')->where('email', auth()->user()->email)->limit(1);
            })
            ->with('plano')
            ->firstOrFail();

        return view('planos.detalhes', compact('assinatura'));
    }
}