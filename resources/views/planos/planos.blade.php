<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos de Assinatura - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        .gold-border { border-color: #D4AF37; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen flex flex-col justify-between">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur">
        <div class="max-w-6xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="/" class="text-xl font-black tracking-widest uppercase">Barber<span class="gold-text">Co.</span></a>
            <a href="/" class="text-sm text-zinc-400 hover:text-zinc-100 transition">← Voltar para Início</a>
        </div>
    </header>

    <main class="max-w-6xl w-full mx-auto px-4 py-16 flex-grow">
        <div class="text-center mb-16">
            <span class="text-xs font-bold tracking-widest uppercase text-[#D4AF37] block mb-2">Clube de Benefícios</span>
            <h2 class="text-4xl font-black uppercase tracking-wider">Nossos <span class="gold-text">Planos Mensais</span></h2>
            <p class="text-zinc-400 mt-2 max-w-md mx-auto text-sm">Corte o cabelo e faça a barba quantas vezes precisar com uma assinatura fixa mensal. Sem taxas escondidas.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
            
            @foreach($planos as $plano)
                <div class="bg-zinc-900/50 border {{ $plano->preco > 100 ? 'border-[#D4AF37] shadow-xl shadow-yellow-500/5' : 'border-zinc-800' }} p-8 rounded-2xl flex flex-col justify-between relative overflow-hidden">
                    
                    @if($plano->preco > 100)
                        <span class="absolute top-0 right-0 bg-[#D4AF37] text-black text-[9px] font-black uppercase px-4 py-1.5 tracking-widest rounded-bl">Mais Assinado</span>
                    @endif

                    <div>
                        <h3 class="text-xl font-black uppercase tracking-wide text-zinc-100 mb-2">{{ $plano->nome }}</h3>
                        <p class="text-xs text-zinc-400 mb-6 leading-relaxed">{{ $plano->descricao }}</p>
                        
                        <div class="flex items-baseline mb-8">
                            <span class="text-2xl font-bold text-zinc-400">R$</span>
                            <span class="text-5xl font-black tracking-tight gold-text mx-1">{{ number_format($plano->preco, 0, ',', '.') }}</span>
                            <span class="text-xs text-zinc-500">/mês</span>
                        </div>

                        <ul class="space-y-4 mb-8 text-sm text-zinc-300">
                            <li class="flex items-center gap-2">
                                <span class="gold-text">✓</span> 
                                {{ $plano->limite_cortes == 0 ? 'Cortes de Cabelo Ilimitados' : $plano->limite_cortes . ' Cortes de Cabelo por mês' }}
                            </li>
                            @if($plano->limite_barba > 0 || $plano->limite_barba === 0 && str_contains(strtolower($plano->nome), 'barba'))
                                <li class="flex items-center gap-2">
                                    <span class="gold-text">✓</span> 
                                    {{ $plano->limite_barba == 0 ? 'Serviços de Barba Ilimitados' : $plano->limite_barba . ' Serviços de Barba por mês' }}
                                </li>
                            @endif
                            <li class="flex items-center gap-2">
                                <span class="gold-text">✓</span> Atendimento preferencial
                            </li>
                            <li class="flex items-center gap-2 text-zinc-500">
                                <span class="gold-text">✓</span> Vigência de 30 dias renováveis
                            </li>
                        </ul>
                    </div>

                    <a href="#" class="w-full block text-center {{ $plano->preco > 100 ? 'bg-[#D4AF37] text-black hover:bg-yellow-500' : 'bg-zinc-800 text-zinc-100 hover:bg-zinc-700' }} font-bold py-4 rounded-xl uppercase tracking-wider transition text-xs shadow-md">
                        Contratar Plano
                    </a>
                </div>
            @endforeach

        </div>
    </main>

    <footer class="border-t border-zinc-900 bg-zinc-950 py-6 text-center text-xs text-zinc-600">
        <p>&copy; 2026 BarberCo. Todos os direitos reservados.</p>
    </footer>

</body>
</html>