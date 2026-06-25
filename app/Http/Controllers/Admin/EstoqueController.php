<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\HistoricoEstoque;
use Illuminate\Support\Facades\Hash;

class EstoqueController extends Controller
{
    public function index()
    {
        $produtos = Produto::orderBy('nome', 'asc')->get();

        // Busca os últimos 30 movimentos do histórico
        $historico = HistoricoEstoque::with(['produto', 'usuario'])
                        ->latest()
                        ->take(30)
                        ->get();

        return view('admin.estoque', compact('produtos', 'historico'));
    }

    public function repor(Request $request, $id)
    {
        // 1. Validação dos campos (Se falhar no AJAX, o Laravel já devolve JSON automaticamente)
        $request->validate([
            'quantidade' => 'required|integer|min:1',
            'senha_admin' => 'required|string'
        ]);

        // 2. Verifica se a senha confere com o usuário atualmente logado
        $usuarioLogado = auth()->user();
        
        if (!Hash::check($request->input('senha_admin'), $usuarioLogado->password)) {
            // Se a requisição veio via AJAX (Fetch), retorna a mensagem em JSON com erro 422
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'senha_erro' => 'Acesso negado: A senha de administrador informada está incorreta.'
                ], 422);
            }

            // Fallback caso a requisição seja tradicional
            return redirect()->route('admin.estoque')->withErrors([
                'senha_erro' => 'Acesso negado: A senha de administrador informada está incorreta.'
            ]);
        }

        // 3. Se a senha estiver correta, segue o fluxo normal de reposição
        $produto = Produto::findOrFail($id);
        $produto->increment('estoque', $request->input('quantidade'));

        // Grava o rastro da ENTRADA no histórico de auditoria
        HistoricoEstoque::create([
            'produto_id' => $produto->id,
            'user_id'    => auth()->id(),
            'tipo'       => 'entrada',
            'quantidade' => $request->input('quantidade'),
            'motivo'     => "Reposição manual de estoque autorizada"
        ]);

        // Se for AJAX, retorna uma resposta de sucesso limpa em JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => "Estoque do produto '{$produto->nome}' reabastecido com sucesso!"
            ], 200);
        }

        // Retorno tradicional (fallback)
        return redirect()->route('admin.estoque')->with('sucesso', "Estoque do produto '{$produto->nome}' reabastecido com sucesso!");
    }

    public function darBaixa(Request $request, $id)
    {
        // 1. Validação dos campos
        $request->validate([
            'quantidade' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'senha_admin' => 'required|string'
        ]);

        // 2. Verifica a senha do usuário logado
        $usuarioLogado = auth()->user();
        
        if (!Hash::check($request->input('senha_admin'), $usuarioLogado->password)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'senha_erro' => 'Acesso negado: A senha de administrador informada está incorreta.'
                ], 422);
            }
            return redirect()->route('admin.estoque')->withErrors([
                'senha_erro' => 'Acesso negado: A senha de administrador informada está incorreta.'
            ]);
        }

        $produto = Produto::findOrFail($id);

        // Verifica se há estoque suficiente para dar baixa
        if ($produto->estoque < $request->input('quantidade')) {
            return response()->json([
                'senha_erro' => "Quantidade indisponível. O estoque atual é de apenas {$produto->estoque} un."
            ], 422);
        }

        // 3. Decrementa o estoque
        $produto->decrement('estoque', $request->input('quantidade'));

        // Grava o rastro da SAÍDA no histórico
        HistoricoEstoque::create([
            'produto_id' => $produto->id,
            'user_id'    => auth()->id(),
            'tipo'       => 'saida', // Define como saída para pintar a linha de vermelho
            'quantidade' => $request->input('quantidade'),
            'motivo'     => "Quebra/Avaria: " . $request->input('motivo')
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'sucesso' => "Baixa do produto '{$produto->nome}' registrada com sucesso!"
            ], 200);
        }

        return redirect()->route('admin.estoque')->with('sucesso', "Baixa do produto '{$produto->nome}' registrada com sucesso!");
    }
}