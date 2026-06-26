<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Agenda - Administração</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-nav-ui/1.0.0/css/line-awesome.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .gold-text { color: #f59e0b; }
        /* Estilização Dark para o SweetAlert combinar com o design da barbearia */
        .swal2-popup-dark {
            background: #09090b !important; /* zinc-950 */
            border: 1px solid #18181b !important; /* zinc-900 */
            color: #f4f4f5 !important; /* zinc-100 */
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto">
        {{-- Cabeçalho da Página --}}
        <header class="flex flex-col md:flex-row justify-center items-start md:items-center gap-4 mb-6 border-b border-zinc-900 pb-6">
            <div>
                <a href="{{ route('admin.painel') }}" class="text-xs font-bold text-zinc-500 hover:text-amber-500 transition flex items-center gap-1 mb-2">
                    <i class="la la-arrow-left"></i> Voltar para o Painel Financeiro
                </a>
                <h1 class="text-xl font-black uppercase tracking-widest">Controle de <span class="text-amber-500">Agenda</span></h1>
                <p class="text-xs text-zinc-500 mt-1">Visualizando atendimentos de: <b class="text-zinc-200">{{ $tituloData }}</b></p>
            </div>
        </header>

        {{-- SELETOR DE ABAS E FILTRO POR DATA PERSONALIZADA --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            
            {{-- Botões Rápidos --}}
            <div class="flex gap-2 bg-zinc-900/50 p-1 rounded-xl w-full sm:w-80 border border-zinc-900">
                <a href="{{ route('admin.agenda', ['dia' => 'hoje']) }}" 
                class="flex-1 text-center py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ $aba === 'hoje' ? 'bg-zinc-800 text-amber-500 border border-zinc-700/50' : 'text-zinc-400 hover:text-zinc-200' }}">
                    <i class="la la-calendar-day mr-1 text-sm"></i> Hoje
                </a>
                <a href="{{ route('admin.agenda', ['dia' => 'amanha']) }}" 
                class="flex-1 text-center py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition-all {{ $aba === 'amanha' ? 'bg-zinc-800 text-amber-500 border border-zinc-700/50' : 'text-zinc-400 hover:text-zinc-200' }}">
                    <i class="la la-calendar-plus mr-1 text-sm"></i> Amanhã
                </a>
            </div>

            {{-- Formulário de Filtro por Calendário --}}
            <form method="GET" action="{{ route('admin.agenda') }}" class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative w-full md:w-48">
                    <input type="text" 
                        id="data_busca"
                        name="data_busca" 
                        value="{{ $tituloData }}" {{-- 🌟 Força o uso da data já formatada em PT-BR pelo Controller --}}
                        placeholder="DD/MM/AAAA"
                        maxlength="10"
                        autocomplete="off" {{-- 🌟 Evita que o navegador force o preenchimento no padrão americano --}}
                        class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-2.5 text-xs text-zinc-200 focus:outline-none focus:border-amber-500 text-center">
                </div>
                <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-zinc-950 font-bold text-xs px-4 py-3 rounded-xl transition flex items-center gap-1 shrink-0">
                    <i class="la la-search text-sm"></i> Buscar
                </button>
            </form>
        </div>

        {{-- Grid de Barbeiros (Mantido original em colunas lado a lado) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($barbeiros as $barbeiro)
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 flex flex-col justify-between">
                    <div>
                        {{-- Topo do Card --}}
                        <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4">
                            <h3 class="font-bold text-zinc-100 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $aba === 'hoje' ? 'bg-amber-500' : ($aba === 'amanha' ? 'bg-blue-400' : 'bg-zinc-500') }}"></span>
                                {{ $barbeiro->nome }}
                            </h3>
                            <span class="text-[10px] bg-zinc-800 text-zinc-400 px-2 py-1 rounded-md font-medium">
                                {{ $atendimentosDoDia->where('barbeiro_id', $barbeiro->id)->where('status', 'Agendado')->count() }} pendentes
                            </span>
                        </div>

                        {{-- Lista de Horários do Barbeiro --}}
                        <div class="space-y-3 max-h-[450px] overflow-y-auto pr-1">
                            @php
                                $agendamentosBarbeiro = $atendimentosDoDia->where('barbeiro_id', $barbeiro->id);
                            @endphp

                            @forelse($agendamentosBarbeiro as $agendamento)
                                <div class="p-3 rounded-xl border {{ $agendamento->status == 'Concluído' ? 'bg-zinc-950/40 border-zinc-900/50 opacity-50' : 'bg-zinc-950 border-zinc-800 hover:border-zinc-700' }} transition">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-black text-amber-500 text-sm">
                                            {{ \Carbon\Carbon::parse($agendamento->data_hora)->format('H:i') }}
                                        </span>
                                        <span class="text-[9px] uppercase font-bold px-2 py-0.5 rounded {{ $agendamento->status == 'Concluído' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                            {{ $agendamento->status }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-xs text-zinc-300 font-medium truncate">
                                        <i class="la la-user text-zinc-500"></i> {{ $agendamento->cliente_nome }}
                                    </p>
                                    <p class="text-[11px] text-zinc-500 truncate mb-2">
                                        <i class="la la-scissors"></i> {{ $agendamento->servico }} — R$ {{ number_format($agendamento->preco, 2, ',', '.') }}
                                    </p>

                                    {{-- Controle Dinâmico do Botão de Conclusão --}}
                                    @if($agendamento->status == 'Agendado')
                                        @if(\Carbon\Carbon::parse($agendamento->data_hora)->isFuture() && !\Carbon\Carbon::parse($agendamento->data_hora)->isToday())
                                            {{-- Bloqueia a conclusão se o agendamento for estritamente em um dia futuro --}}
                                            <div class="w-full bg-zinc-950 border border-zinc-850 text-zinc-500 text-center py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider cursor-not-allowed">
                                                Aguardando Data
                                            </div>
                                        @else
                                            {{-- Permite a conclusão se for hoje ou uma data retroativa --}}
                                            <form method="POST" action="{{ route('admin.agendamentos.concluir', $agendamento->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full bg-zinc-800 hover:bg-emerald-600 text-zinc-200 hover:text-white transition py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider flex items-center justify-center gap-1">
                                                    <i class="la la-check-circle text-sm"></i> Concluir Serviço
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-zinc-600 text-center py-8">Nenhum horário para este dia.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Script para forçar a máscara DD/MM/AAAA e impedir digitação errada --}}
    <script>
        document.getElementById('data_busca').addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,2})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : x[1] + '/' + x[2] + (x[3] ? '/' + x[3] : '');
        });
    </script>

    @if(session('sucesso'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: "{{ session('sucesso') }}",
                showConfirmButton: false,
                timer: 2200,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-popup-dark'
                },
                iconColor: '#10b981' /* emerald-500 */
            });
        </script>
    @endif

</body>
</html>