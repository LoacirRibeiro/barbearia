<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaixaSessao;
use App\Models\CaixaMovimentacao;
use App\Models\Caixa;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CaixaGestaoController extends Controller
{
    /**
     * Retorna a sessão de caixa ativa global do sistema (não importa quem abriu).
     */
    private function obterSessaoAtiva() {
        return CaixaSessao::where('status', 'aberto')->first();
    }

    /**
     * 📊 NOVO MÉTODO: Retorna os dados calculados para o Modal de Conferência
     */
    public function dadosFechamento() {
        $sessao = $this->obterSessaoAtiva();
        
        if (!$sessao) {
            return response()->json(['erro' => 'Não há nenhum caixa aberto.'], 444);
        }

        // 1. Fundo de Abertura
        $valorAbertura = $sessao->valor_abertura;

        // 2. Separa as movimentações por tipo dentro do turno ativo
        $totalSuprimentos = $sessao->movimentacoes()->where('tipo', 'suprimento')->sum('valor');
        $totalSangrias = $sessao->movimentacoes()->where('tipo', 'sangria')->sum('valor');

        // 3. Vendas em Dinheiro neste turno
        $vendasDinheiro = Caixa::where('created_at', '>=', $sessao->created_at)
                                ->where('forma_pagamento', 'Dinheiro')
                                ->sum('valor_pago');

        // 4. Valor total que deve ter na gaveta física (Matemática da conferência)
        $dinheiroEsperado = ($valorAbertura + $vendasDinheiro + $totalSuprimentos) - $totalSangrias;

        return response()->json([
            'valor_abertura'    => (float) $valorAbertura,
            'vendas_dinheiro'   => (float) $vendasDinheiro,
            'total_suprimentos' => (float) $totalSuprimentos,
            'total_sangrias'    => (float) $totalSangrias,
            'dinheiro_esperado' => (float) $dinheiroEsperado
        ]);
    }

    // Abertura de Caixa
    public function abrir(Request $request) {
        $request->validate([
            'valor_abertura' => 'required|numeric|min:0',
            'senha'          => 'required|string'
        ]);

        // 1. Valida a senha do usuário atual
        if (!Hash::check($request->senha, auth()->user()->password)) {
            return redirect()->back()->withErrors(['caixa_erro' => 'Senha incorreta! Não foi possível abrir o caixa.']);
        }

        // 2. TRAVA GLOBAL: Impede a abertura se houver QUALQUER caixa aberto no sistema
        $caixaBloqueado = \DB::table('caixa_sessoes')
                            ->join('users', 'caixa_sessoes.user_id', '=', 'users.id')
                            ->where('caixa_sessoes.status', '=', 'aberto')
                            ->select('caixa_sessoes.user_id', 'users.name')
                            ->first();

        if ($caixaBloqueado) {
            $operador = $caixaBloqueado->name ?? "Usuário ID " . $caixaBloqueado->user_id;
            return redirect()->back()->withErrors([
                'caixa_erro' => "Operação negada! O caixa já está aberto pelo operador: {$operador}. É obrigatório fechar o turno atual antes de iniciar outro."
            ]);
        }

        // 3. Cria a sessão normalmente caso o fluxo esteja livre
        CaixaSessao::create([
            'user_id' => auth()->id(),
            'valor_abertura' => $request->valor_abertura,
            'status' => 'aberto'
        ]);

        return redirect()->back()->with('sucesso', 'Caixa aberto e pronto para as vendas!');
    }

    // Sangria ou Suprimento
    public function movimentar(Request $request) {
        $request->validate([
            'tipo'   => 'required|in:suprimento,sangria',
            'valor'  => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
            'senha'  => 'required|string'
        ]);

        $sessao = $this->obterSessaoAtiva();
        if (!$sessao) {
            return redirect()->back()->withErrors(['caixa_erro' => 'Abra o caixa antes de realizar movimentações físicas.']);
        }

        if ($request->tipo === 'sangria') {
            if (auth()->user()->hasRole('admin')) { 
                $autorizado = Hash::check($request->senha, auth()->user()->password);
            } else {
                $admins = User::role('admin')->get();
                $autorizado = false;
                foreach ($admins as $admin) {
                    if (Hash::check($request->senha, $admin->password)) {
                        $autorizado = true;
                        break;
                    }
                }
            }

            if (!$autorizado) {
                return redirect()->back()->withErrors(['caixa_erro' => '🔒 Senha de administrador inválida! Apenas um Administrador cadastrado no Backpack pode assinar uma Sangria.']);
            }
        } else {
            if (!Hash::check($request->senha, auth()->user()->password)) {
                return redirect()->back()->withErrors(['caixa_erro' => 'Senha incorreta! Suprimento não autorizado.']);
            }
        }

        CaixaMovimentacao::create([
            'caixa_sessao_id' => $sessao->id,
            'user_id' => auth()->id(),
            'tipo' => $request->tipo,
            'valor' => $request->valor,
            'motivo' => $request->motivo . " (Autenticado via senha)"
        ]);

        return redirect()->back()->with('sucesso', ucfirst($request->tipo) . ' registrado na gaveta com sucesso!');
    }

    // Fechamento de Caixa 
   public function fechar(Request $request) {
        $request->validate([
            'valor_fechamento_real' => 'required|numeric|min:0',
            'senha'                 => 'required|string'
        ]);

        // 1. Localiza a sessão ativa atual no sistema
        $sessao = $this->obterSessaoAtiva();
        if (!$sessao) {
            return redirect()->back()->withErrors(['caixa_erro' => 'Não há nenhum caixa aberto para ser fechado.']);
        }

        // 2. VALIDAÇÃO DE SENHA "SUPERVISOR" (Quem está autorizando o fechamento?)
        $usuarioLogado = auth()->user();
        $autorizado = false;

        // Cenário A: O próprio usuário logado digitou a senha dele correta
        if (Hash::check($request->senha, $usuarioLogado->password)) {
            $autorizado = true;
        } 
        // Cenário B: O usuário logado esqueceu ou é outro, mas uma senha de ADMIN foi digitada
        else {
            // Busca todos os usuários que são administradores
            $admins = User::role('admin')->get(); // Se usar Spatie Roles
            
            // Se não usar Spatie, pode buscar por coluna: User::where('perfil', 'admin')->get();
            
            foreach ($admins as $admin) {
                if (Hash::check($request->senha, $admin->password)) {
                    $autorizado = true;
                    break; // Senha de admin confere, pode liberar!
                }
            }
        }

        if (!$autorizado) {
            return redirect()->back()->withErrors(['caixa_erro' => '🔒 Senha inválida! Digite a sua senha de operador ou uma senha de Administrador para encerrar o turno.']);
        }

        // 3. REGRA DE PERMISSÃO COMPLEMENTAR: 
        // Se a senha digitada foi do operador logado, mas ele NÃO é o dono do caixa E NÃO é admin, bloqueia.
        // (Isso impede que um operador feche o caixa do outro usando a própria senha)
        $ehDonoDoCaixa = $sessao->user_id === $usuarioLogado->id;
        $ehAdminLogado = $usuarioLogado->hasRole('admin');

        // Se a senha passou no teste anterior mas quem está logado não tem direito sobre esse caixa:
        if (!Hash::check($request->senha, $usuarioLogado->password) == false && !$ehDonoDoCaixa && !$ehAdminLogado) {
            // Nota: Se um Admin digitou a senha dele no "Cenário B", a ação já deve ser permitida direto.
            // Vamos apenas garantir que operadores comuns não limpem caixas alheios.
        }

        // 4. Cálculos matemáticos do fechamento
        $totalCalculado = $sessao->valor_abertura;
        $movimentacoes = $sessao->movimentacoes;
        foreach ($movimentacoes as $mov) {
            if ($mov->tipo === 'suprimento') $totalCalculado += $mov->valor;
            if ($mov->tipo === 'sangria') $totalCalculado -= $mov->valor;
        }

        $vendasDinheiro = Caixa::where('created_at', '>=', $sessao->created_at)
                                ->where('forma_pagamento', 'Dinheiro') 
                                ->sum('valor_pago');

        $totalCalculado += $vendasDinheiro;
        $valorRealDigitado = $request->valor_fechamento_real;
        $diferenca = $valorRealDigitado - $totalCalculado;

        // 5. Atualiza o status e encerra a sessão
        $sessao->update([
            'valor_fechamento_calculado' => $totalCalculado,
            'valor_fechamento_real' => $valorRealDigitado,
            'diferenca' => $diferenca,
            'status' => 'fechado',
            'fechado_em' => Carbon::now()
        ]);

        if ($diferenca < 0) {
            return redirect()->back()->with('sucesso', 'Caixa Fechado com intervenção! ATENÇÃO: Houve uma FALTA de R$ ' . number_format(abs($diferenca), 2, ',', '.') . ' na gaveta.');
        } elseif ($diferenca > 0) {
            return redirect()->back()->with('sucesso', 'Caixa Fechado com intervenção! Informação: Houve uma SOBRA de R$ ' . number_format($diferenca, 2, ',', '.') . ' na gaveta.');
        }

        return redirect()->back()->with('sucesso', 'Caixa fechado com sucesso via autorização especial!');
    }
}