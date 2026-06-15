<?php

namespace App\Http\Controllers;

use App\Models\Barbeiro;
use App\Models\Servico;
use App\Models\Plano;
use App\Models\Cliente;
use App\Models\Assinatura;

class PublicController extends Controller
{
    public function index()
    {
        $barbeiros = Barbeiro::all();

        $servicosHome = Servico::where('ativo', true)
            ->orderBy('categoria')
            ->orderBy('preco', 'asc')
            ->get()
            ->groupBy('categoria');

        $planosHome = Plano::where('ativo', true)
            ->orderBy('preco', 'asc')
            ->get();
        
        $minhaAssinatura = null;
    
        if (auth()->check()) {
            $usuarioLogado = auth()->user();
            $cliente = Cliente::where('email', $usuarioLogado->email)->first();

            if ($cliente) {
                $minhaAssinatura = Assinatura::where('cliente_id', $cliente->id)
                    ->where('status', 'Ativo')
                    ->where('data_fim', '>=', now()->toDateString())
                    ->with('plano')
                    ->first();
            }
        }

        return view('welcome', compact('barbeiros', 'servicosHome', 'planosHome', 'minhaAssinatura'));
    }

    public function planos()
    {
        $planos = Plano::where('ativo', true)->orderBy('preco', 'asc')->get();
        return view('planos', compact('planos'));
    }
}