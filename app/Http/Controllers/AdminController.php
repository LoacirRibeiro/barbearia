<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barbeiro;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\Assinatura;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth'); // Protege todos os métodos do painel admin
    // }

    // public function painelAdmin(Request $request)
    // {
    //     $diaInput = $request->input('dia_especifico');
    //     if ($diaInput) {
    //         $diaSelecionado = Carbon::createFromFormat('d/m/Y', $diaInput)->toDateString();
    //         $diaSelecionadoFormatado = $diaInput;
    //     } else {
    //         $diaSelecionado = now()->toDateString();
    //         $diaSelecionadoFormatado = now()->format('d/m/Y');
    //     }

    //     $mesAnoInput = $request->input('mes_ano'); 
    //     if ($mesAnoInput) {
    //         $carbonMes = Carbon::createFromFormat('m/Y', $mesAnoInput);
    //         $dataInicial = $carbonMes->startOfMonth()->toDateString();
    //         $dataFinal = $carbonMes->endOfMonth()->toDateString();
    //     } else {
    //         $dataInicial = now()->startOfMonth()->toDateString();
    //         $dataFinal = now()->endOfMonth()->toDateString();
    //     }

    //     $financeiroDiaQuery = Agendamento::join('servicos', 'agendamentos.servico', '=', 'servicos.nome')
    //         ->whereDate('agendamentos.data_hora', $diaSelecionado);

    //     $financeiroPeriodoQuery = Agendamento::join('servicos', 'agendamentos.servico', '=', 'servicos.nome')
    //         ->whereBetween(DB::raw('DATE(agendamentos.data_hora)'), [$dataInicial, $dataFinal]);

    //     $statsFinanceiras = [
    //         'total_dia' => (clone $financeiroDiaQuery)->where('servicos.preco', '>', 0)->sum('servicos.preco'),
    //         'qtd_dia'   => (clone $financeiroDiaQuery)->count(),
    //         'total_periodo' => (clone $financeiroPeriodoQuery)->where('servicos.preco', '>', 0)->sum('servicos.preco'),
    //         'qtd_periodo'   => (clone $financeiroPeriodoQuery)->count(),
    //         'dia_selecionado' => $diaSelecionado,
    //         'dia_selecionado_formatado' => $diaSelecionadoFormatado,
    //         'data_inicial' => $dataInicial,
    //         'data_final' => $dataFinal,
    //     ];

    //     $atendimentosDoDia = (clone $financeiroDiaQuery)
    //         ->join('barbeiros', 'agendamentos.barbeiro_id', '=', 'barbeiros.id')
    //         ->join('clientes', 'agendamentos.cliente_id', '=', 'clientes.id')
    //         ->select('agendamentos.*', 'servicos.preco', 'barbeiros.nome as barbeiro_nome', 'clientes.nome as cliente_nome')
    //         ->orderBy('agendamentos.data_hora', 'asc')
    //         ->get();

    //     $atendimentosDoPeriodo = (clone $financeiroPeriodoQuery)
    //         ->join('barbeiros', 'agendamentos.barbeiro_id', '=', 'barbeiros.id')
    //         ->join('clientes', 'agendamentos.cliente_id', '=', 'clientes.id')
    //         ->select('agendamentos.*', 'servicos.preco', 'barbeiros.nome as barbeiro_nome', 'clientes.nome as cliente_nome')
    //         ->orderBy('agendamentos.data_hora', 'desc')
    //         ->get();

    //     $barbeirosRelatorio = Barbeiro::get()->map(function($barbeiro) use ($dataInicial, $dataFinal) {
    //         $agendamentosNoPeriodo = Agendamento::join('servicos', 'agendamentos.servico', '=', 'servicos.nome')
    //             ->where('agendamentos.barbeiro_id', $barbeiro->id)
    //             ->where('agendamentos.status', 'Concluído')
    //             ->whereBetween(DB::raw('DATE(agendamentos.data_hora)'), [$dataInicial, $dataFinal])
    //             ->select('agendamentos.*', 'servicos.preco', 'servicos.categoria')
    //             ->get();

    //         $comuns = $agendamentosNoPeriodo->where('preco', '>', 0);
    //         $planos = $agendamentosNoPeriodo->where('preco', '<=', 0);

    //         return [
    //             'nome' => $barbeiro->nome,
    //             'total_comuns' => $comuns->count(),
    //             'total_planos' => $planos->count(),
    //             'faturamento_comum' => $comuns->sum('preco'),
    //             'comissao_receber' => $comuns->sum('preco') * 0.50,
    //             'detalhes_servicos' => $agendamentosNoPeriodo
    //         ];
    //     });

    //     $cortesPlanoPorCategoria = Agendamento::join('servicos', 'agendamentos.servico', '=', 'servicos.nome')
    //         ->whereBetween(DB::raw('DATE(agendamentos.data_hora)'), [$dataInicial, $dataFinal])
    //         ->where('servicos.preco', '<=', 0)
    //         ->select('servicos.categoria', DB::raw('count(*) as total'))
    //         ->groupBy('servicos.categoria')
    //         ->get();

    //     $planosAtivos = Assinatura::where('status', 'Ativo')
    //         ->where('status_pagamento', 'Pago')
    //         ->whereDate('data_fim', '>=', now()->toDateString())
    //         ->get();

    //     return view('admin.painel', compact(
    //         'barbeirosRelatorio', 
    //         'cortesPlanoPorCategoria', 
    //         'statsFinanceiras',
    //         'atendimentosDoDia',
    //         'atendimentosDoPeriodo',
    //         'planosAtivos'
    //     ));
    // }

    public function agendaAdmin(Request $request)
    {
        $aba = $request->input('dia', 'hoje');
        $dataBusca = $request->input('data_busca');

        if ($dataBusca) {
            try {
                if (str_contains($dataBusca, '/')) {
                    $dataFiltrada = Carbon::createFromFormat('d/m/Y', $dataBusca)->toDateString();
                } else {
                    $dataFiltrada = Carbon::parse($dataBusca)->toDateString();
                }
                $aba = 'custom';
                $tituloData = Carbon::parse($dataFiltrada)->format('d/m/Y');
            } catch (\Exception $e) {
                $dataFiltrada = now()->toDateString();
                $tituloData = now()->format('d/m/Y');
                $aba = 'hoje';
            }
        } else {
            if ($aba === 'amanha') {
                $dataFiltrada = now()->addDay()->toDateString();
                $tituloData = now()->addDay()->format('d/m/Y');
            } else {
                $dataFiltrada = now()->toDateString();
                $tituloData = now()->format('d/m/Y');
                $aba = 'hoje';
            }
        }

        $atendimentosDoDia = Agendamento::join('servicos', 'agendamentos.servico', '=', 'servicos.nome')
            ->join('barbeiros', 'agendamentos.barbeiro_id', '=', 'barbeiros.id')
            ->join('clientes', 'agendamentos.cliente_id', '=', 'clientes.id')
            ->whereDate('agendamentos.data_hora', $dataFiltrada)
            ->select('agendamentos.*', 'servicos.preco', 'barbeiros.nome as barbeiro_nome', 'clientes.nome as cliente_nome')
            ->orderBy('agendamentos.data_hora', 'asc')
            ->get();

        $barbeiros = Barbeiro::all();

        return view('admin.agenda', compact('atendimentosDoDia', 'barbeiros', 'aba', 'tituloData', 'dataFiltrada'));
    }

    public function concluirAgendamento($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update(['status' => 'Concluído']);

        return redirect()->back()->with('sucesso', 'Atendimento concluído com sucesso!');
    }

    public function relatorioPlanos()
{
    $hoje = now()->format('Y-m-d');

    $planosPendentes = DB::table('assinaturas')
        ->join('clientes', 'assinaturas.cliente_id', '=', 'clientes.id')
        ->join('planos', 'assinaturas.plano_id', '=', 'planos.id')
        ->where('assinaturas.status_pagamento', 'Pendente')
        ->where('assinaturas.status', '!=', 'Cancelado') // Garante que cancelados saiam daqui
        ->select(
            'assinaturas.id as assinatura_id', 
            'clientes.nome as cliente_nome', 
            'clientes.email as cliente_email', 
            'assinaturas.data_inicio', 
            'assinaturas.data_fim',
            'assinaturas.forma_pagamento',
            'assinaturas.status_pagamento',    
            'planos.nome as nome_plano',   
            'planos.preco as preco_plano'  
        )
        ->orderBy('assinaturas.created_at', 'desc')
        ->get();

    $planosAtivos = DB::table('assinaturas')
        ->join('clientes', 'assinaturas.cliente_id', '=', 'clientes.id')
        ->join('planos', 'assinaturas.plano_id', '=', 'planos.id')
        ->where(DB::raw('DATE(assinaturas.data_fim)'), '>=', $hoje)
        ->where('assinaturas.status', 'Ativo')
        ->where('assinaturas.status_pagamento', 'Pago')
        ->select(
            'assinaturas.id as assinatura_id',
            'clientes.nome as cliente_nome', 
            'clientes.email as cliente_email', 
            'assinaturas.data_inicio', 
            'assinaturas.data_fim',
            'assinaturas.forma_pagamento',
            'assinaturas.status_pagamento',
            'planos.nome as nome_plano',   
            'planos.preco as preco_plano'  
        )
        ->orderBy('assinaturas.data_fim', 'asc')
        ->get();

    // 🛠️ QUERY CORRIGIDA PARA MOSTRAR OS CANCELADOS NO HISTÓRICO
    $planosVencidos = DB::table('assinaturas')
        ->join('clientes', 'assinaturas.cliente_id', '=', 'clientes.id')
        ->join('planos', 'assinaturas.plano_id', '=', 'planos.id')
        ->where(function($query) use ($hoje) {
            $query->where(DB::raw('DATE(assinaturas.data_fim)'), '<', $hoje) // Vencidos por data
                  ->orWhere('assinaturas.status', 'Cancelado')             // 👈 Captura os Cancelados explicitamente
                  ->orWhere('assinaturas.status', 'Inativo');               // Inativos que já saíram do balcão
        })
        ->select(
            'assinaturas.id as assinatura_id',
            'clientes.nome as cliente_nome',
            'assinaturas.data_inicio',
            'assinaturas.data_fim',
            'assinaturas.forma_pagamento',
            'assinaturas.status', // Puxando o status real para você saber o que é vencido e o que é cancelado
            'planos.nome as nome_plano',
            'planos.preco as preco_plano'
        )
        ->orderBy('assinaturas.updated_at', 'desc') // Mais recentes no topo
        ->get();

    return view('admin.planos', compact('planosPendentes', 'planosAtivos', 'planosVencidos'));
}

    public function confirmarPagamento(Request $request, $id) 
    {
        if ($request->input('senha_admin') !== 'admin123') { 
            return redirect()->back()->with('erro', 'Senha do administrador incorreta para liberação.');
        }

        DB::table('assinaturas')
            ->where('id', $id)
            ->update([
                'status'           => 'Ativo',  
                'status_pagamento' => 'Pago',   
                'data_inicio'      => now()->toDateString(),
                'data_fim'         => now()->addDays(30)->toDateString(),
                'updated_at'       => now()
            ]);

        return redirect()->back()->with('sucesso', 'Assinatura liberada e pagamento confirmado com sucesso!');
    }

    public function reativarAssinatura(Request $request, $id)
    {
        if ($request->input('senha_admin') !== 'admin123') { 
            return redirect()->back()->with('erro', 'Senha do administrador incorreta para liberação.');
        }

        $assinatura = DB::table('assinaturas')->where('id', $id)->first();

        if (!$assinatura) {
            return redirect()->back()->with('erro', 'Assinatura não encontrada.');
        }

        DB::table('assinaturas')
            ->where('id', $id)
            ->update([
                'status'           => 'Inativo',
                'status_pagamento' => 'Pendente',
                'updated_at'       => now()
            ]);

        return redirect()->back()->with('sucesso', 'Plano enviado com sucesso para a fila de recebimento do Balcão!');
    }

    public function cancelarAssinatura(Request $request, $id) 
    {
        if ($request->input('senha_admin') !== 'admin123') { 
            return redirect()->back()->with('erro', 'Senha do administrador incorreta.');
        }

        DB::table('assinaturas')
            ->where('id', $id)
            ->update([
                'status' => 'Cancelado', 
                'updated_at' => now()
            ]);

        return redirect()->back()->with('sucesso', 'Assinatura cancelada com sucesso!');
    }

    public function visualizarRelatorio()
{
    $hoje = now()->format('Y-m-d');

    $totalFaturado = DB::table('assinaturas')
        ->join('planos', 'assinaturas.plano_id', '=', 'planos.id')
        ->where('assinaturas.status_pagamento', 'Pago')
        ->sum('planos.preco');

    $totalContratados = DB::table('assinaturas')
        ->where('status', 'Ativo')
        ->where('status_pagamento', 'Pago')
        ->where(DB::raw('DATE(data_fim)'), '>=', $hoje)
        ->count();

    // 🔥 CORRIGIDO: Agora conta TODOS os cancelados ou vencidos, independente de estar pendente ou não
    $totalCancelados = DB::table('assinaturas')
        ->where(function($query) use ($hoje) {
            $query->where(DB::raw('DATE(data_fim)'), '<', $hoje)
                  ->orWhere('status', 'Cancelado')
                  ->orWhere('status', '!=', 'Ativo');
        })
        ->count();

    $movimentacoes = DB::table('assinaturas')
        ->join('clientes', 'assinaturas.cliente_id', '=', 'clientes.id')
        ->join('planos', 'assinaturas.plano_id', '=', 'planos.id')
        ->select(
            'clientes.nome as cliente_nome',
            'planos.nome as nome_plano',
            'planos.preco as preco_plano',
            'assinaturas.status',
            'assinaturas.status_pagamento',
            'assinaturas.updated_at'
        )
        ->orderBy('assinaturas.updated_at', 'desc')
        ->take(50)
        ->get();

    return view('admin.relatorios.planos', compact('totalFaturado', 'totalContratados', 'totalCancelados', 'movimentacoes'));
}

    public function obterDetalhes($id)
    {
        $assinatura = Assinatura::with(['plano', 'cliente'])->find($id);

        if (!$assinatura) {
            return response()->json(['erro' => 'Assinatura não encontrada'], 404);
        }

        return response()->json([
            'plano_nome'       => $assinatura->plano->nome ?? 'Plano não identificado',
            'cliente_nome'     => $assinatura->cliente->nome ?? 'Cliente não cadastrado',
            'cliente_email'    => $assinatura->cliente->email ?? 'Sem e-mail',
            'cliente_telefone' => $assinatura->cliente->telefone ?? 'Não cadastrado',
            'data_inicio'      => $assinatura->data_inicio,
            'data_fim'         => $assinatura->data_fim,
            'pagamentos'       => [
                [
                    'referencia' => Carbon::parse($assinatura->data_inicio)->format('m/Y'),
                    'forma'      => $assinatura->forma_pagamento,
                    'status'     => $assinatura->status_pagamento,
                ]
            ],
            'servicos'         => []
        ]);
    }

    public function caixaForm()
    {
        $barbeiros = Barbeiro::all();
        $servicos = Servico::where('ativo', true)->where('preco', '>', 0)->orderBy('nome')->get();

        return view('admin.caixa', compact('barbeiros', 'servicos'));
    }

    public function caixaSalvar(Request $request)
    {
        $request->validate([
            'barbeiro_id'     => 'required|exists:barbeiros,id',
            'servico_id'      => 'required|exists:servicos,id',
            'forma_pagamento' => 'required|string|in:Dinheiro,Pix,Cartão de Crédito,Cartão de Débito',
            'nome_cliente'    => 'nullable|string|max:255',
        ]);

        $nomeCliente = $request->input('nome_cliente') ?: 'Cliente Balcão';
        
        $cliente = Cliente::firstOrCreate(
            ['nome' => $nomeCliente, 'email' => 'balcao_' . time() . '@barbearia.com'],
            ['telefone' => 'Não informado']
        );

        $servicoBanco = Servico::findOrFail($request->servico_id);

        Agendamento::create([
            'cliente_id'   => $cliente->id,
            'barbeiro_id'  => $request->barbeiro_id,
            'servico'      => $servicoBanco->nome,
            'data_hora'    => now(),
            'status'       => 'Concluído',
        ]);

        return redirect()->route('admin.caixa.form')->with('sucesso', 'Venda de balcão registrada com sucesso!');
    }

    public function cancelarAssinaturaPendete(Request $request, $id)
    {
        $request->validate([
            'senha_admin' => 'required|string',
        ]);

        if ($request->input('senha_admin') !== 'admin123') { 
            return redirect()->back()->withErrors(['senha_admin' => 'Senha administrativa incorreta. Operação cancelada.']);
        }

        // 🔥 CORRIGIDO: Em vez de ->delete(), atualizamos o status para salvar no histórico
        $assinatura = Assinatura::findOrFail($id);
        $assinatura->update([
            'status' => 'Cancelado',
            'updated_at' => now()
        ]);

        return redirect()->back()->with('sucesso', 'Pedido de assinatura cancelado com sucesso de forma segura!');
    }
}