<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia Premium</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
    </style>
</head>

<body class="bg-zinc-950 text-zinc-100 font-sans selection:bg-[#D4AF37] selection:text-black">

   <header class="border-b border-zinc-800 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        {{-- Aumentamos a altura do container de h-20 para h-32 --}}
        <div class="max-w-6xl mx-auto px-4 h-32 flex items-center justify-between">

            <!-- <a href="/" class="text-2xl font-black tracking-widest uppercase">Barber<span class="text-[#D4AF37]">Co.</span></a> -->
            
            <a href="/" class="flex items-center h-full py-2">
                {{-- Ajustamos para h-28 (112px) para que a logo fique grande, mas tenha um leve respiro nas bordas --}}
                <img src="{{ asset('images/logo coxxas.png') }}" 
                    alt="BarberCo. Logo" 
                    class="h-28 w-auto object-contain hover:opacity-90 transition-opacity">
            </a>
            
            <div class="flex items-center space-x-4">
               {{-- 🔒 Checa se o usuário está logado no site --}}
                @auth
                    {{-- 👑 💰 Se o usuário logado for 'admin' OU 'operador', o botão aparece na Home --}}
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operador'))
                        <a href="{{ route('admin.caixa') }}" class="bg-[#D4AF37] text-black text-[10px] font-black px-4 py-2 rounded-lg uppercase tracking-widest hover:bg-yellow-500 transition shadow-lg shadow-yellow-500/20">
                            <i class="la la-cog"></i> Painel Administrativo
                        </a>
                    @endif
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-zinc-400 hover:text-zinc-100 transition mr-2">
                        Entrar
                    </a>
                @endguest

                @auth
                    <span class="text-sm text-zinc-400 hidden sm:inline">Olá, <span class="text-[#D4AF37] font-bold">{{ auth()->user()->name }}</span></span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-rose-400 hover:text-rose-300 transition mr-2 cursor-pointer">
                            Sair
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    {{-- ✨ NOVA SEÇÃO EXCLUSIVA PARA A LOGO CENTRALIZADA (PREFERÊNCIA DO CLIENTE) ✨ --}}
    <div class="w-full pt-20 pb-6 flex items-center justify-center bg-zinc-950">
        <div class="flex flex-col items-center">
            <a href="/" class="block transform hover:scale-102 transition-transform duration-300">
                {{-- Logo robusta e centralizada --}}
                <img src="{{ asset('images/logo coxxas.png') }}" 
                     alt="BarberCo. Logo" 
                     class="h-56 md:h-72 w-auto object-contain">
            </a>
            {{-- Divisor sutil opcional abaixo da logo --}}
            <div class="w-70 h-[1px] bg-zinc-800 mt-2"></div>
        </div>
    </div>

    <section class="relative min-h-[70vh] flex items-center justify-center text-center px-4 py-20 bg-radial from-zinc-900 to-zinc-950">
        <div class="max-w-3xl">
            <span class="text-xs font-bold tracking-widest uppercase gold-text block mb-3">Estilo & Tradição</span>
            <h2 class="text-4xl md:text-6xl font-black uppercase tracking-tight mb-6 leading-tight">
                Mais que um corte,<br>uma <span class="gold-text">experiência</span>.
            </h2>
            <p class="text-zinc-400 text-lg md:text-xl mb-10 max-w-xl mx-auto leading-relaxed">
                Ambiente exclusivo, atendimento personalizado e profissionais qualificados. Agende seu horário em segundos.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('horarios.disponiveis') }}" class="gold-bg text-black font-bold px-8 py-4 rounded uppercase tracking-wider hover:bg-yellow-500 transition text-center">
                    Horários Disponíveis
                </a>
                @auth
                    <a href="{{ route('cliente.agendamentos') }}" class="border border-zinc-700 hover:border-zinc-500 text-zinc-200 font-bold px-8 py-4 rounded uppercase tracking-wider transition text-center">
                        Meus Agendamentos
                    </a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-xs text-zinc-500 hover:text-zinc-300 transition underline tracking-wider uppercase font-bold">
                        Já tem horário? Faça Login
                    </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- 📸 SEÇÃO DE FOTOS CENTRALIZADA E AMPLIADA --}}
    <div class="mt-16 w-full max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-8 px-4">
        @foreach($fotosHome as $foto)
            {{-- Mudamos para aspect-video para dar mais altura e presença visual --}}
            <div class="group relative aspect-video overflow-hidden rounded-2xl border border-zinc-800 hover:border-[#D4AF37]/50 transition-all duration-500 shadow-2xl bg-zinc-900/50">
                
                <img src="{{ Storage::url($foto->caminho) }}" 
                    alt="{{ $foto->titulo ?? 'Imagem Barbearia' }}" 
                    class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                
                @if(!empty(trim($foto->titulo)))
                    {{-- Aumentamos levemente o tamanho do texto para text-sm para acompanhar o tamanho da foto --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-transparent flex items-end p-6">
                        <span class="text-sm font-bold uppercase tracking-widest text-zinc-300 group-hover:text-white transition-colors">
                            {{ $foto->titulo }}
                        </span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <section id="servicos" class="max-w-7xl mx-auto px-4 py-24 border-t border-zinc-900">
        <div class="text-center mb-16">
            <span class="text-xs font-bold tracking-widest uppercase text-[#D4AF37] block mb-2">Tabela de Preços</span>
            <h3 class="text-3xl font-black uppercase tracking-wider">Nossos Serviços</h3>
            <p class="text-zinc-500 mt-2">Cuidado, estilo e confiança para o seu melhor visual!</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            @foreach($servicosHome as $categoria => $listaServicos)
                <div class="{{ $categoria === 'Combo' ? 'bg-zinc-900 border-2 border-[#D4AF37]/50 shadow-xl shadow-yellow-500/5 relative overflow-hidden' : 'bg-zinc-900/50 border border-zinc-800' }} p-5 rounded-xl flex flex-col justify-between">
                    @if($categoria === 'Combo')
                        <span class="absolute top-0 right-0 bg-[#D4AF37] text-black text-[9px] font-black uppercase px-3 py-1 tracking-widest rounded-bl">Melhor Custo</span>
                    @endif

                    <div>
                        <h4 class="text-base font-black uppercase tracking-wider text-[#D4AF37] mb-6 pb-2 border-b border-zinc-800">
                            {{ $categoria }}
                        </h4>
                        
                        <ul class="space-y-4">
                            @foreach($listaServicos as $item)
                                <li class="flex flex-col gap-0.5 pb-1 border-b border-zinc-800/20 last:border-0">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="{{ $categoria === 'Combo' ? 'text-zinc-100 font-bold' : 'text-zinc-300' }}">
                                            {{ $item->nome }}
                                        </span>
                                        <span class="font-mono font-bold {{ $categoria === 'Combo' ? 'text-[#D4AF37]' : 'text-zinc-100' }}">
                                            R$ {{ number_format($item->preco, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @if($minhaAssinatura)
        <div class="max-w-xl mx-auto mb-12 p-6 bg-zinc-900 border border-[#D4AF37] rounded-2xl shadow-xl shadow-yellow-500/5 relative overflow-hidden">
            <span class="absolute top-0 right-0 bg-emerald-500 text-zinc-950 text-[9px] font-black uppercase px-4 py-1.5 tracking-widest rounded-bl flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-zinc-950 rounded-full animate-ping"></span>
                Assinatura Ativa
            </span>

            <div class="flex items-start gap-4">
                <div class="p-3 bg-zinc-950 rounded-xl border border-zinc-800 text-[#D4AF37]">
                    <i class="la la-id-card text-2xl"></i>
                </div>
                
                <div class="flex-1">
                    <h4 class="text-lg font-black uppercase tracking-wide text-zinc-100 pr-24">
                        {{ $minhaAssinatura->plano->nome }}
                    </h4>
                    <p class="text-xs text-zinc-400 mt-2">
                        Sua assinatura renova ou expira em: 
                        <span class="text-zinc-200 font-mono font-bold">
                            {{ \Carbon\Carbon::parse($minhaAssinatura->data_fim)->format('d/m/Y') }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('planos.detalhes', $minhaAssinatura->id) }}" class="w-full block text-center bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-[#D4AF37] py-2.5 rounded-xl uppercase tracking-wider text-[10px] font-bold transition">
                    📄 Ver Detalhes do Plano & Contrato
                </a>
            </div>
        </div>
    @endif

    <section id="planos" class="max-w-7xl mx-auto px-4 py-24 border-t border-zinc-900 bg-radial from-zinc-900/20 to-transparent">
        <div class="text-center mb-16">
            <span class="text-xs font-bold tracking-widest uppercase text-[#D4AF37] block mb-2">Clube de Benefícios</span>
            <h3 class="text-3xl font-black uppercase tracking-wider">Planos de <span class="gold-text">Assinatura</span></h3>
            <p class="text-zinc-500 mt-2 max-w-md mx-auto text-sm">Corte o cabelo e faça a barra quantas vezes precisar com uma taxa fixa mensal. Economia e estilo andam juntos.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @foreach($planosHome as $plano)
                <div class="bg-zinc-900/40 border {{ $plano->preco > 100 ? 'border-[#D4AF37] shadow-xl shadow-yellow-500/5 bg-zinc-900/80' : 'border-zinc-800' }} p-8 rounded-2xl flex flex-col justify-between relative overflow-hidden transition-all duration-300 hover:scale-[1.02]">
                    @if($plano->preco > 100)
                        <span class="absolute top-0 right-0 bg-[#D4AF37] text-black text-[9px] font-black uppercase px-4 py-1.5 tracking-widest rounded-bl">Mais Vantajoso</span>
                    @endif

                    <div>
                        <h4 class="text-xl font-black uppercase tracking-wide text-zinc-100 mb-2">{{ $plano->nome }}</h4>
                        <p class="text-xs text-zinc-400 mb-6 leading-relaxed min-h-[36px]">{{ $plano->descricao }}</p>
                        
                        <div class="flex items-baseline mb-8">
                            <span class="text-xl font-bold text-zinc-500">R$</span>
                            <span class="text-5xl font-black tracking-tight gold-text mx-1">{{ number_format($plano->preco, 0, ',', '.') }}</span>
                            <span class="text-xs text-zinc-500">/mês</span>
                        </div>

                        <ul class="space-y-4 mb-8 text-sm text-zinc-300">
                            <li class="flex items-center gap-2.5">
                                <span class="text-[#D4AF37] font-bold text-base">✓</span> 
                                {{ $plano->limite_cortes == 0 ? 'Cortes de Cabelo Ilimitados' : $plano->limite_cortes . ' Cortes de Cabelo por mês' }}
                            </li>
                            
                            @if($plano->limite_barba > 0 || ($plano->limite_barba === 0 && str_contains(strtolower($plano->nome), 'barba')))
                                <li class="flex items-center gap-2.5">
                                    <span class="text-[#D4AF37] font-bold text-base">✓</span> 
                                    {{ $plano->limite_barba == 0 ? 'Barba Completa Ilimitada' : $plano->limite_barba . ' Serviços de Barba por mês' }}
                                </li>
                            @endif
                            
                            <li class="flex items-center gap-2.5">
                                <span class="text-[#D4AF37] font-bold text-base">✓</span> Atendimento Preferencial
                            </li>
                            <li class="flex items-center gap-2.5 text-zinc-500">
                                <span class="text-[#D4AF37] font-bold text-base">✓</span> Renovação Automática a cada 30 dias
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('planos.contratar', $plano->id) }}" class="w-full block text-center {{ $plano->preco > 100 ? 'bg-[#D4AF37] text-black hover:bg-yellow-500 font-black' : 'bg-zinc-800 text-zinc-100 hover:bg-zinc-700 font-bold' }} py-4 rounded-xl uppercase tracking-wider transition text-xs shadow-md cursor-pointer">
                        Contratar {{ $plano->nome }}
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <section id="barbeiros" class="bg-zinc-950 py-24 border-t border-zinc-900">
        <div class="max-w-5xl mx-auto px-4">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-black uppercase tracking-wider">Nossos <span class="gold-text">Especialistas</span></h3>
                <p class="text-zinc-500 mt-2">Escolha o profissional de sua preferência</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @forelse($barbeiros as $barbeiro)
                    <div class="group bg-zinc-900/40 p-4 rounded-2xl border border-zinc-800 text-center flex flex-col items-center hover:border-[#D4AF37]/40 transition-all duration-300 backdrop-blur-sm">
                        
                        {{-- Bloco de Foto RETANGULAR/QUADRADO Grande com Cantos Suaves --}}
                        <div class="w-full aspect-[4/5] mb-5 relative rounded-xl overflow-hidden border border-zinc-800 group-hover:border-[#D4AF37]/50 transition-all duration-500 bg-zinc-950 flex items-center justify-center shadow-2xl">
                            
                            {{-- Forçamos o trim para evitar espaços em branco passados pelo banco --}}
                            @if(!empty(trim($barbeiro->foto)))
                                <img src="{{ asset(Str::start(trim($barbeiro->foto), 'storage/')) }}" 
                                    alt="{{ $barbeiro->nome }}" 
                                    class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                            @else
                                {{-- Fallback Minimalista Retangular se realmente não tiver foto --}}
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-zinc-900 border border-dashed border-zinc-800 m-2 rounded-lg">
                                    <span class="text-[#D4AF37] font-black text-3xl uppercase tracking-widest opacity-60 group-hover:opacity-100 transition-opacity">
                                        {{ substr($barbeiro->nome, 0, 2) }}
                                    </span>
                                    <span class="text-[9px] text-zinc-600 uppercase tracking-wider mt-2">Sem Foto</span>
                                </div>
                            @endif
                        </div>

                        <h4 class="font-bold text-lg text-zinc-200 group-hover:text-white transition-colors mt-2">{{ $barbeiro->nome }}</h4>
                        
                        {{-- Badge moderno para a especialidade --}}
                        <span class="inline-block mt-3 px-3 py-1 bg-zinc-950 text-[#D4AF37] border border-zinc-800 text-[10px] font-black tracking-widest uppercase rounded-md group-hover:bg-[#D4AF37]/10 group-hover:border-[#D4AF37]/20 transition-all">
                            {{ $barbeiro->specialidade ?? 'Barbeiro VIP' }}
                        </span>
                        
                    </div>
                @empty
                    <div class="col-span-full text-center text-zinc-500 py-12 border border-dashed border-zinc-800 rounded-2xl">
                        Nenhum barbeiro cadastrado no painel administrativo ainda.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <footer class="border-t border-zinc-900 bg-zinc-950 py-8 text-center text-xs text-zinc-600">
        <p>&copy; 2026 BarberCo. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 🚀 CORREÇÃO DA ÂNCORA ULTRA-PRECISA (Para Tailwind v4)
            const urlParams = new URLSearchParams(window.location.search);
            
            // Verifica se veio com a hash #planos OU com o parâmetro ?scroll=planos
            if (window.location.hash === '#planos' || urlParams.get('scroll') === 'planos') {
                setTimeout(() => {
                    const secaoPlanos = document.getElementById('planos');
                    if (secaoPlanos) {
                        // Rola a tela suavemente até o topo da seção de planos
                        secaoPlanos.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        
                        // Limpa a URL visualmente tirando o "?scroll=planos" e mantendo o foco limpo
                        window.history.pushState({}, document.title, window.location.pathname + '#planos');
                    }
                }, 400); // 400ms é o tempo ideal para o motor do Tailwind v4 calcular o grid de planos
            }

            // --- ALERTAS SWEETALERT ---
            @if(session('sucesso'))
                Swal.fire({
                    title: '✨ Sucesso!',
                    text: "{{ session('sucesso') }}",
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: '#18181b',
                    color: '#f4f4f5',
                    iconColor: '#10b981',
                    customClass: { popup: 'border border-zinc-800 rounded-2xl' }
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    title: 'Atenção!',
                    html: `
                        <ul style="text-align: center; list-style: none; padding: 0; margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li style="margin-bottom: 5px;">⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    icon: 'error',
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: '#18181b',
                    color: '#f4f4f5',
                    iconColor: '#f43f5e',
                    customClass: { popup: 'border border-zinc-800 rounded-2xl' }
                });
            @endif
        });
    </script>
</body>
</html>