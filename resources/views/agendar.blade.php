<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Horário - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        .gold-border { border-color: #D4AF37; }
        /* Customização para o SweetAlert combinar com o tema dark/gold */
        .swal2-popup-custom {
            background: #18181b !important; /* bg-zinc-900 */
            border: 1px solid #27272a !important; /* border-zinc-800 */
            color: #f4f4f5 !important; /* text-zinc-100 */
            border-radius: 0.75rem !important;
        }
        .swal2-confirm-custom {
            background-color: #D4AF37 !important;
            color: #000000 !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 10px 24px !important;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen flex flex-col justify-between">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur">
        <div class="max-w-4xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="/" class="text-xl font-black tracking-widest uppercase">Barber<span class="gold-text">Co.</span></a>
            <a href="/" class="text-sm text-zinc-400 hover:text-zinc-100 transition">← Voltar para Início</a>
        </div>
    </header>

    <main class="max-w-xl w-full mx-auto px-4 py-12 flex-grow flex flex-col justify-center">
        <div class="bg-zinc-900/50 p-8 rounded-xl border border-zinc-800 shadow-2xl">
            <h2 class="text-2xl font-black uppercase tracking-wider text-center mb-2">Agende seu <span class="gold-text">Horário</span></h2>
            <p class="text-zinc-400 text-sm text-center mb-6">Confirme os dados da sua sessão e escolha o serviço desejado.</p>

            <div class="mb-6 p-4 bg-zinc-950 border border-zinc-900 rounded-xl space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#D4AF37] block">Resumo da Escolha</span>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-[11px] text-zinc-500 uppercase font-bold tracking-wider">Profissional</span>
                        <span class="text-zinc-200 font-bold">💈 {{ request('barbeiro_nome') ?? request('barbeiro') ?? 'Profissional Selecionado' }}</span>
                    </div>
                    <div>
                        <span class="block text-[11px] text-zinc-500 uppercase font-bold tracking-wider">Horário</span>
                        <span class="text-zinc-200 font-mono font-bold text-[#D4AF37]">
                            📅 {{ request('data') ? date('d/m', strtotime(request('data'))) : '' }} às {{ request('hora') ?? '--:--' }}
                        </span>
                    </div>
                </div>
            </div>

            <form action="{{ route('agendar.salvar') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Cenário 1: Usuário Logado e possui um histórico de plano --}}
                @if($minhaAssinatura)
                    @php
                        $isAtivo = $minhaAssinatura->status === 'Ativo' && \Carbon\Carbon::parse($minhaAssinatura->data_fim)->isFuture();
                    @endphp

                    @if($isAtivo)
                        <div class="mb-6 p-4 bg-emerald-950/20 border border-emerald-500/40 rounded-xl flex items-center justify-between">
                            <div class="space-y-1">
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400 block">Benefício Identificado</span>
                                <h4 class="text-xs font-bold text-zinc-200">Você possui o <span class="gold-text">{{ $minhaAssinatura->plano->nome }}</span> ativo!</h4>
                                <p class="text-[11px] text-zinc-400">Os serviços cobertos pelo seu plano serão zerados no carrinho.</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="usar_plano" value="1" id="toggle-plano" checked class="sr-only peer">
                                    <div class="w-24 h-8 bg-zinc-900 peer-focus:outline-none rounded-lg border border-zinc-800 peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-[#D4AF37] after:border-zinc-900 after:border after:rounded-md after:h-6 after:w-11 after:transition-all peer-checked:border-emerald-500/50 block">
                                        <span class="absolute left-3 top-2 text-[9px] font-black text-zinc-500 peer-checked:text-transparent uppercase">Não</span>
                                        <span class="absolute right-3 top-2 text-[9px] font-black text-transparent peer-checked:text-emerald-400 uppercase">Usar</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-rose-950/20 border border-rose-900/60 rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <span class="text-[10px] font-black uppercase tracking-widest text-rose-400 block">Assinatura Expirada</span>
                                <h4 class="text-xs font-bold text-zinc-300">Seu plano <span class="text-zinc-400 line-through">{{ $minhaAssinatura->plano->nome }}</span> venceu.</h4>
                                <p class="text-[11px] text-zinc-500">Este agendamento seguirá com o valor normal de tabela.</p>
                            </div>
                            <a href="/#clube-beneficios" class="text-center bg-zinc-950 border border-zinc-800 hover:border-[#D4AF37] text-zinc-300 hover:text-white px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition">
                                🔄 Renovar Plano
                            </a>
                        </div>
                    @endif

                {{-- Cenário 2: Usuário não tem plano ou é Visitante --}}
                @else
                    <div class="mb-6 p-4 bg-zinc-900/60 border border-zinc-800 rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#D4AF37] block">Economize Dinheiro</span>
                            <h4 class="text-xs font-bold text-zinc-200">Quer cortes e barba ilimitados o mês todo?</h4>
                            <p class="text-[11px] text-zinc-500">Faça parte do nosso Clube de Fidelidade VIP.</p>
                        </div>
                        <a href="/#clube-beneficios" class="text-center bg-[#D4AF37] hover:bg-yellow-500 text-black px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-wider transition shadow-lg shadow-yellow-500/5">
                            💎 Assinar Plano
                        </a>
                    </div>
                @endif

                <input type="hidden" name="horario_id" value="{{ request('horario_id') }}">
                <input type="hidden" name="barbeiro_id" value="{{ request('barbeiro_id') ?? request('barbeiro') }}">
                
                @php
                    $dataUrl = request('data'); 
                    $horaUrl = request('hora');
                    $dataHoraFormatada = ($dataUrl && $horaUrl) ? "{$dataUrl} {$horaUrl}:00" : null;
                @endphp
                <input type="hidden" name="data_hora" value="{{ $dataHoraFormatada }}">

                @auth
                    <p class="text-zinc-400 text-sm text-center bg-zinc-950/40 py-2 rounded border border-zinc-900 mb-4">
                        Agendando como: <span class="text-[#D4AF37] font-bold">{{ auth()->user()->name }}</span>
                    </p>
                @endauth

                @guest
                    <div class="p-4 bg-zinc-950/30 rounded border border-zinc-900 space-y-4 mb-4">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#D4AF37] block">Modo Visitante / Teste</span>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Seu Nome</label>
                            <input type="text" name="nome" required placeholder="Ex: Carlos Silva" class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-zinc-100 focus:outline-none focus:border-[#D4AF37] transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Seu Telefone / WhatsApp</label>
                            <input type="text" name="telefone" required placeholder="Ex: (11) 99999-9999" class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-zinc-100 focus:outline-none focus:border-[#D4AF37] transition text-sm">
                        </div>
                    </div>
                @endguest

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Escolha o Serviço</label>
                    <div class="relative">
                        <select name="servico_id" id="select-servico" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-zinc-100 focus:outline-none focus:border-[#D4AF37] transition text-sm appearance-none cursor-pointer">
                            <option value="" disabled selected>Selecione um serviço...</option>
                            
                            @foreach($servicosAgrupados as $categoria => $listaServicos)
                                <optgroup label="{{ $categoria }}" class="bg-zinc-900 text-[#D4AF37] font-bold">
                                    @foreach($listaServicos as $item)
                                        <option value="{{ $item->id }}" data-preco="{{ $item->preco }}" data-categoria="{{ $categoria }}" class="text-zinc-100">
                                            {{ $item->nome }} — R$ {{ number_format($item->preco, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-zinc-400">
                            ▼
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded uppercase tracking-wider hover:bg-yellow-500 transition text-sm shadow-lg shadow-yellow-500/5 mt-4 cursor-pointer">
                    Confirmar Agendamento
                </button>
            </form>
        </div>
    </main>

    <footer class="border-t border-zinc-900 bg-zinc-950 py-6 text-center text-xs text-zinc-600">
        <p>&copy; 2026 BarberCo. Todos os direitos reservados.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Verifica se a página atual recebeu a sessão de sucesso do controlador
            @if(session('sucesso'))
                Swal.fire({
                    icon: 'success',
                    title: 'AGENDADO COM SUCESSO!',
                    text: "{{ session('sucesso') }}",
                    confirmButtonText: 'VOLTAR PARA A HOME',
                    allowOutsideClick: false, // Força o usuário a interagir com o botão
                    customClass: {
                        popup: 'swal2-popup-custom',
                        confirmButton: 'swal2-confirm-custom'
                    }
                }).then((result) => {
                    // Quando o cliente clica no botão "VOLTAR PARA A HOME"
                    if (result.isConfirmed) {
                        window.location.href = "{{ url('/') }}"; // Direciona para a Home
                    }
                });
            @endif

            // Alerta em caso de erros de validação ou clique duplo
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'NÃO FOI POSSÍVEL AGENDAR',
                    html: `<p class="mb-3 text-zinc-400 text-sm">Por favor, corrija os seguintes pontos:</p>
                           <ul class="text-left list-disc list-inside space-y-1 text-sm text-rose-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                           </ul>`,
                    confirmButtonText: 'ENTENDIDO',
                    customClass: {
                        popup: 'swal2-popup-custom',
                        confirmButton: 'swal2-confirm-custom'
                    }
                });
            @endif

           // --- 🛒 2. LÓGICA DINÂMICA DE PREÇOS NO SELECT (VALIDAÇÃO POR CATEGORIA) ---
            const togglePlano = document.getElementById('toggle-plano');
            const selectServico = document.getElementById('select-servico');
            
            // Passa o nome do plano do Blade para o JS
            const planoUsuario = "{{ $minhaAssinatura && isset($isAtivo) && $isAtivo ? $minhaAssinatura->plano->nome : '' }}";
            
            // Salva as informações originais assim que a página carrega
            const opcoesOriginais = Array.from(selectServico.options).map(opt => {
                const partes = opt.text.split(/ — | —|— /);
                return {
                    value: opt.value,
                    id: opt.getAttribute('data-id'),
                    nomeLimpo: partes[0].trim(),
                    preco: opt.getAttribute('data-preco'),
                    categoria: opt.getAttribute('data-categoria') ? opt.getAttribute('data-categoria').toLowerCase().trim() : ''
                };
            });

            function atualizarPrecosVisuais() {
                const usarPlano = togglePlano ? togglePlano.checked : false;
                const planoTexto = planoUsuario.toLowerCase().trim();

                Array.from(selectServico.options).forEach((option, index) => {
                    if (index === 0) return; 

                    const infoOriginal = opcoesOriginais[index];
                    const precoFormatado = parseFloat(infoOriginal.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    
                    let cobertoPeloPlano = false;

                    if (usarPlano && planoTexto !== '') {
                        
                        // REGRA 1: Se o plano for o HAIR VIP, só zera o que for da categoria "Cabelo"
                        if (planoTexto.includes('hair')) {
                            if (infoOriginal.categoria === 'cabelo') {
                                cobertoPeloPlano = true;
                            }
                        } 
                        
                        // REGRA 2: Se for o VIP CLUB, zera quase tudo (Cabelo, Barba, etc.) menos tratamentos faciais complexos se quiser
                        else if (planoTexto.includes('club') || planoTexto.includes('vip')) {
                            // Libera as categorias permitidas para o plano completo
                            if (['cabelo', 'barba', 'combo'].includes(infoOriginal.categoria)) {
                                cobertoPeloPlano = true;
                            }
                        }
                    }

                    // Aplica o texto modificado ou o preço normal
                    if (cobertoPeloPlano) {
                        option.text = `${infoOriginal.nomeLimpo} — 🎁 GRÁTIS (No Plano)`;
                    } else {
                        option.text = `${infoOriginal.nomeLimpo} — ${precoFormatado}`;
                    }
                });
            }

            // Ativa os gatilhos se o botão de usar plano existir
            if (togglePlano) {
                togglePlano.addEventListener('change', atualizarPrecosVisuais);
                atualizarPrecosVisuais();
            }
        });
    </script>

</body>
</html>