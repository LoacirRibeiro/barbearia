<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barbeiro;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\HorarioDisponivel; 
use App\Models\Servico;
use App\Models\Assinatura;
use Carbon\Carbon;

class AgendamentoController extends Controller
{
    public function verHorarios()
    {
        $barbeiros = Barbeiro::all();
        
        $horasTeste = [
            '08:00', '08:40', '09:20', '10:00', '10:40', '11:20',
            '12:00', '12:40', '13:20', '14:00', '14:40', '15:20', 
            '16:00', '16:40', '17:20', '18:00', '18:40'
        ];
        
        $diasTeste = [
            now()->format('Y-m-d'),
            now()->addDay()->format('Y-m-d')
        ];

        foreach ($diasTeste as $dataStr) {
            foreach ($barbeiros as $barbeiro) {
                foreach ($horasTeste as $hora) {
                    $dataHoraFinal = Carbon::parse($dataStr . ' ' . $hora);

                    HorarioDisponivel::firstOrCreate([
                        'barbeiro_id' => $barbeiro->id,
                        'data_hora'   => $dataHoraFinal,
                    ], [
                        'disponivel'  => true
                    ]);
                }
            }
        }

        $horariosBanco = HorarioDisponivel::where('disponivel', true)
            ->where('data_hora', '>=', now())
            ->where('data_hora', '<=', now()->addDay()->endOfDay())
            ->with('barbeiro')
            ->orderBy('data_hora', 'asc')
            ->get();

        $agendaDinamica = [];

        foreach ($horariosBanco as $item) {
            $dataStr = trim($item->data_hora->format('Y-m-d')); 
            $horaStr = $item->data_hora->format('H:i');   
            $horaId  = $item->id;
            
            $horaInt = (int)$item->data_hora->format('H');
            if ($horaInt < 12) {
                $turno = 'Manhã';
            } elseif ($horaInt < 18) {
                $turno = 'Tarde';
            } else {
                $turno = 'Noite';
            }

            $agendaDinamica[$dataStr]['formatada'] = $item->data_hora->format('d/m');
            $agendaDinamica[$dataStr]['dia_semana'] = $item->data_hora->translatedFormat('D');
            
            $agendaDinamica[$dataStr]['turnos'][$turno][] = [
                'id' => $horaId,
                'hora' => $horaStr,
                'barbeiro_id' => $item->barbeiro_id,
                'barbeiro' => $item->barbeiro->nome
            ];
        }

        return view('horarios', [
            'agendaDinamica' => $agendaDinamica,
            'dataHojeReal'   => now()->toDateString(),
            'dataAmanhaReal' => now()->addDay()->toDateString()
        ]);
    }

    public function agendarForm(Request $request)
    {
        $barbeiros = Barbeiro::all();
        $horarioSelecionado = HorarioDisponivel::find($request->query('horario_id'));
        $servicosAgrupados = Servico::where('ativo', true)->get()->groupBy('categoria');

        $minhaAssinatura = null;
        if (auth()->check()) {
            $cliente = Cliente::where('email', auth()->user()->email)->first();
            if ($cliente) {
                $minhaAssinatura = Assinatura::where('cliente_id', $cliente->id)
                    ->with('plano')
                    ->latest()
                    ->first();
            }
        }

        if ($horarioSelecionado) {
            $request->merge([
                'barbeiro_id' => $horarioSelecionado->barbeiro_id,
                'barbeiro'    => $horarioSelecionado->barbeiro->nome ?? 'Profissional',
                'data'        => $horarioSelecionado->data_hora->format('Y-m-d'),
                'hora'        => $horarioSelecionado->data_hora->format('H:i')
            ]);
        }

        return view('agendar', compact('barbeiros', 'horarioSelecionado', 'servicosAgrupados', 'minhaAssinatura'));
    }

    public function salvarAgendamento(Request $request)
    {
        $regras = [
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'servico_id'  => 'required|exists:servicos,id',
            'data_hora'   => 'required|date',
            'horario_id'  => 'required|exists:horarios_disponiveis,id' 
        ];

        if (!auth()->check()) {
            $regras['nome'] = 'required|string|max:255';
            $regras['telefone'] = 'required|string|max:20';
        }

        $request->validate($regras);

        // 🛑 TRAVA 1: Impede agendamento em horário ocupado ou inexistente
        $horarioDoBanco = HorarioDisponivel::find($request->horario_id);
        if (!$horarioDoBanco || !$horarioDoBanco->disponivel) {
            return redirect()->route('horarios.disponiveis')->withErrors(['erro' => 'Desculpe, esse horário acabou de ser preenchido por outro cliente!']);
        }

        // Resgata ou cria a entidade Cliente
        if (auth()->check()) {
            $usuarioLogado = auth()->user();
            $cliente = Cliente::firstOrCreate(
                ['email' => $usuarioLogado->email],
                [
                    'nome' => $usuarioLogado->name, 
                    'telefone' => $usuarioLogado->telefone ?? 'Não informado'
                ]
            );
        } else {
            $cliente = Cliente::firstOrCreate(
                ['telefone' => $request->telefone],
                [
                    'nome' => $request->nome, 
                    'email' => 'visitante_' . time() . '@teste.com'
                ]
            );
        }

        $servicoBanco = Servico::findOrFail($request->servico_id);
        $observacaoAgendamento = 'Agendamento Padrão (Particular)';
        
        // 🛡️ TRAVA 2: Segurança Total do Plano de Assinatura (Evita F12 injection)
        if ($request->input('usar_plano') == 1 && auth()->check()) {
            $assinaturaAtiva = Assinatura::where('cliente_id', $cliente->id)
                ->where('status', 'Ativo')
                ->where('data_fim', '>=', now()->toDateString())
                ->with('plano')
                ->first();

            if ($assinaturaAtiva) {
                $nomePlano = strtolower($assinaturaAtiva->plano->nome);
                $categoriaServico = strtolower($servicoBanco->categoria);
                
                $coberto = false;
                if (str_contains($nomePlano, 'hair') && $categoriaServico === 'cabelo') {
                    $coberto = true;
                } elseif (str_contains($nomePlano, 'club') || str_contains($nomePlano, 'vip')) {
                    if (in_array($categoriaServico, ['cabelo', 'barba', 'combo'])) {
                        $coberto = true;
                    }
                }

                if ($coberto) {
                    $observacaoAgendamento = 'Serviço realizado através do Plano de Fidelidade (' . $assinaturaAtiva->plano->nome . ')';
                } else {
                    return redirect()->back()->withErrors(['erro' => 'Seu plano não possui cobertura para esta categoria de serviço!']);
                }
            } else {
                return redirect()->back()->withErrors(['erro' => 'Você não possui uma assinatura ativa para usar este benefício.']);
            }
        }

        // Criar o registro
        Agendamento::create([
            'cliente_id'   => $cliente->id,
            'barbeiro_id'  => $request->barbeiro_id,
            'servico'      => $servicoBanco->nome,
            'data_hora'    => $request->data_hora,
            'status'       => 'Agendado',
            'observacao'   => $observacaoAgendamento // Importante mapear essa coluna na sua Migration se não existir
        ]);

        $horarioDoBanco->update(['disponivel' => false]);

        return redirect('/')->with('sucesso', 'Horário agendado com sucesso!');
    }

    public function meusAgendamentos()
    {
        $usuarioLogado = auth()->user();
        $cliente = Cliente::where('email', $usuarioLogado->email)->first();

        if (!$cliente) {
            return view('meus_agendamentos', ['proximos' => [], 'historico' => []]);
        }

        $proximos = Agendamento::where('cliente_id', $cliente->id)
            ->where('data_hora', '>=', now())
            ->with('barbeiro')
            ->orderBy('data_hora', 'asc')
            ->get();

        $historico = Agendamento::where('cliente_id', $cliente->id)
            ->where('data_hora', '<', now())
            ->with('barbeiro')
            ->orderBy('data_hora', 'desc')
            ->get();

        return view('meus_agendamentos', compact('proximos', 'historico'));
    }
}