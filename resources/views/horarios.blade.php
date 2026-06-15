<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horários Disponíveis - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col justify-between">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur">
        <div class="max-w-4xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="/" class="text-xl font-black tracking-widest uppercase">Barber<span class="text-[#D4AF37]">Co.</span></a>
            <a href="/" class="text-sm text-zinc-400 hover:text-zinc-100 transition">← Voltar</a>
        </div>
    </header>

    <main class="max-w-4xl w-full mx-auto px-4 py-12 flex-grow">
        <div class="text-center mb-8">
            <span class="text-xs font-bold tracking-widest uppercase text-[#D4AF37] block mb-2">Agendamento Online</span>
            <h2 class="text-3xl font-black uppercase tracking-wider">Horários Disponíveis</h2>
            <p class="text-zinc-400 text-sm mt-1">Selecione o dia e o profissional para listar os horários livres.</p>
        </div>

        @if(session('sucesso'))
            <div class="bg-emerald-950/50 border border-emerald-800 text-emerald-400 p-4 rounded-xl text-center mb-8 text-sm">
                {{ session('sucesso') }}
            </div>
        @endif

        @php
            // Mapeia todos os barbeiros presentes de forma global na lista para montar o filtro dinâmico
            $barbeirosNaLista = [];
            foreach($agendaDinamica as $dia) {
                foreach($dia['turnos'] as $turno) {
                    foreach($turno as $bloco) {
                        if (isset($bloco['barbeiro'])) {
                            $barbeirosNaLista[$bloco['barbeiro']] = true;
                        }
                    }
                }
            }
            $totalBarbeiros = count($barbeirosNaLista);
        @endphp

        <div class="space-y-4 mb-10 pb-6 border-b border-zinc-900">
            
            <div class="flex gap-2 justify-center max-w-sm mx-auto">
                <button onclick="filtrarDia('{{ $dataHojeReal }}', event)" id="btn-dia-hoje"
                        class="btn-dia-filtro px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-[#D4AF37] text-black transition cursor-pointer flex-1 text-center">
                    📅 Hoje
                </button>
                <button onclick="filtrarDia('{{ $dataAmanhaReal }}', event)" id="btn-dia-amanha"
                        class="btn-dia-filtro px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-zinc-100 transition flex-1 text-center">
                    #️⃣ Amanhã
                </button>
            </div>

            @if($totalBarbeiros >= 2)
                <div class="flex flex-wrap gap-2 justify-center pt-2">
                    <button onclick="filtrarBarbeiro('todos', event)" 
                            class="btn-barbeiro-filtro px-4 py-2 rounded-full text-[11px] font-bold uppercase tracking-wider bg-[#D4AF37] text-black transition cursor-pointer">
                        😎 Todos os Profissionais
                    </button>

                    @foreach(array_keys($barbeirosNaLista) as $nomeBarbeiro)
                        <button onclick="filtrarBarbeiro('{{ $nomeBarbeiro }}', event)" 
                                class="btn-barbeiro-filtro px-4 py-2 rounded-full text-[11px] font-bold uppercase tracking-wider bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-zinc-100 transition cursor-pointer">
                            💈 {{ $nomeBarbeiro }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="container-agenda">
            @forelse($agendaDinamica as $dataCompleta => $dadosDia)
                <div class="card-dia bg-zinc-900/30 border border-zinc-900 p-6 rounded-xl mb-8 hidden" data-data="{{ $dataCompleta }}">
                    <div class="flex items-center gap-3 mb-6 pb-2 border-b border-zinc-800/60">
                        <span class="text-2xl font-black text-[#D4AF37]">{{ $dadosDia['formatada'] }}</span>
                        <span class="text-xs uppercase font-bold text-zinc-400 tracking-wider">| {{ $dadosDia['dia_semana'] }}</span>
                    </div>

                    <div class="space-y-6">
                        @foreach($dadosDia['turnos'] as $turno => $listaHorarios)
                            <div class="secao-turno">
                                <h4 class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-3">{{ $turno }}</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                    @foreach($listaHorarios as $bloco)
                                        <a href="{{ route('agendar.form', [
                                                'horario_id'   => $bloco['id'], 
                                                'data'         => $dataCompleta, 
                                                'hora'         => $bloco['hora'],
                                                'barbeiro_id'  => $bloco['barbeiro_id'] ?? null,
                                                'barbeiro'     => $bloco['barbeiro']
                                           ]) }}" 
                                           data-barbeiro="{{ $bloco['barbeiro'] }}"
                                           class="bloco-horario bg-zinc-950 border border-zinc-800 hover:border-[#D4AF37] p-3 rounded text-center transition group flex flex-col items-center justify-center">
                                            <span class="font-mono font-bold text-sm text-zinc-200 group-hover:text-[#D4AF37]">{{ $bloco['hora'] }}</span>
                                            <span class="text-[9px] text-zinc-600 uppercase tracking-tight mt-0.5 group-hover:text-zinc-400">{{ $bloco['barbeiro'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12 border border-dashed border-zinc-800 rounded-xl">
                    <p class="text-zinc-500 text-sm italic">Nenhum horário de atendimento disponível no momento.</p>
                </div>
            @endforelse

            <div id="aviso-vazio" class="hidden text-center py-12 border border-dashed border-zinc-800 rounded-xl">
                <p class="text-zinc-500 text-sm italic">Não há horários livres para os filtros selecionados.</p>
            </div>
        </div>
    </main>

    <footer class="border-t border-zinc-900 bg-zinc-950 py-6 text-center text-xs text-zinc-600">
        <p>&copy; 2026 BarberCo. Todos os direitos reservados.</p>
    </footer>

    <script>
    // Inicializa as variáveis de controle dos filtros
    let diaSelecionado = '{{ $dataHojeReal }}';
    let barbeiroSelecionado = 'todos';

    document.addEventListener("DOMContentLoaded", function() {
        // CORREÇÃO INTELIGENTE: Se a página abrir e o card do dia padrão não existir no HTML,
        // o script adota automaticamente a primeira data disponível na listagem para evitar tela vazia.
        const primeiroCardDisponivel = document.querySelector('.card-dia');
        if (primeiroCardDisponivel && !document.querySelector(`.card-dia[data-data="${diaSelecionado}"]`)) {
            diaSelecionado = primeiroCardDisponivel.getAttribute('data-data');
            
            // Ajusta o visual dos botões de dia para dar o destaque ao dia correto
            const btnAmanha = document.getElementById('btn-dia-amanha');
            const btnHoje = document.getElementById('btn-dia-hoje');
            if (diaSelecionado === '{{ $dataAmanhaReal }}' && btnAmanha && btnHoje) {
                btnHoje.className = "btn-dia-filtro px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-zinc-100 transition flex-1 text-center cursor-pointer";
                btnAmanha.className = "btn-dia-filtro px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-[#D4AF37] text-black transition flex-1 text-center cursor-pointer";
            }
        }

        aplicarFiltros();
    });

    function filtrarDia(dia, event) {
        diaSelecionado = dia;
        
        // Reseta o estilo de todos os botões de dia
        document.querySelectorAll('.btn-dia-filtro').forEach(btn => {
            btn.classList.remove('bg-[#D4AF37]', 'text-black');
            btn.classList.add('bg-zinc-900', 'border-zinc-800', 'text-zinc-400', 'hover:text-zinc-100');
        });
        
        // Aplica o estilo ativo no botão clicado
        event.currentTarget.classList.remove('bg-zinc-900', 'border-zinc-800', 'text-zinc-400', 'hover:text-zinc-100');
        event.currentTarget.classList.add('bg-[#D4AF37]', 'text-black');
        
        aplicarFiltros();
    }

    function filtrarBarbeiro(barbeiro, event) {
        barbeiroSelecionado = barbeiro;
        
        // Reseta o estilo de todos os botões de barbeiros
        document.querySelectorAll('.btn-barbeiro-filtro').forEach(btn => {
            btn.classList.remove('bg-[#D4AF37]', 'text-black');
            btn.classList.add('bg-zinc-900', 'border-zinc-800', 'text-zinc-400');
        });
        
        // Aplica o estilo ativo no barbeiro selecionado
        event.currentTarget.classList.remove('bg-zinc-900', 'border-zinc-800', 'text-zinc-400');
        event.currentTarget.classList.add('bg-[#D4AF37]', 'text-black');
        
        aplicarFiltros();
    }

    function aplicarFiltros() {
        const dias = document.querySelectorAll('.card-dia');
        let existemHorariosVisiveisGlobal = false;

        dias.forEach(dia => {
            const dataDoCard = dia.getAttribute('data-data');
            
            // Se o card não pertence ao dia selecionado, oculta sumariamente
            if (dataDoCard !== diaSelecionado) {
                dia.classList.add('hidden');
                return;
            }

            let diaTemHorarioValido = false;
            const turnos = dia.querySelectorAll('.secao-turno');

            turnos.forEach(turno => {
                let turnoTemHorarioValido = false;
                const blocos = turno.querySelectorAll('.bloco-horario');

                blocos.forEach(bloco => {
                    const barbeiroDoBloco = bloco.getAttribute('data-barbeiro');
                    
                    // Valida se o bloco atende ao filtro do profissional selecionado
                    if (barbeiroSelecionado === 'todos' || barbeiroDoBloco === barbeiroSelecionado) {
                        bloco.classList.remove('hidden');
                        bloco.classList.add('flex');
                        turnoTemHorarioValido = true;
                        diaTemHorarioValido = true;
                        existemHorariosVisiveisGlobal = true;
                    } else {
                        bloco.classList.remove('flex');
                        bloco.classList.add('hidden');
                    }
                });

                // Se nenhum horário sobrou para este turno (ex: Manhã), esconde o título do turno
                if (turnoTemHorarioValido) {
                    turno.classList.remove('hidden');
                } else {
                    turno.classList.add('hidden');
                }
            });

            // Se o dia tem pelo menos um horário disponível correspondente, exibe o card
            if (diaTemHorarioValido) {
                dia.classList.remove('hidden');
            } else {
                dia.classList.add('hidden');
            }
        });

        // Controla a exibição da mensagem de "Nenhum horário livre"
        const aviso = document.getElementById('aviso-vazio');
        if (aviso) {
            if (existemHorariosVisiveisGlobal) {
                aviso.classList.add('hidden');
            } else {
                aviso.classList.remove('hidden');
            }
        }
    }
</script>

</body>
</html>