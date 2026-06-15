<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col justify-between">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur">
        <div class="max-w-4xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="/" class="text-xl font-black tracking-widest uppercase">Barber<span class="text-[#D4AF37]">Co.</span></a>
            <a href="/" class="text-sm text-zinc-400 hover:text-zinc-100 transition">← Voltar para Início</a>
        </div>
    </header>

    <main class="max-w-4xl w-full mx-auto px-4 py-12 flex-grow">
        <h2 class="text-3xl font-black uppercase mb-8 text-zinc-200">Meu <span class="text-[#D4AF37]">Painel</span></h2>

        <div class="flex gap-2 border-b border-zinc-900 mb-8 pb-4">
            <button onclick="alternarAba('agendados', event)" 
                    class="btn-aba px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-[#D4AF37] text-black transition cursor-pointer">
                📅 Agendamentos
            </button>
            <button onclick="alternarAba('realizados', event)" 
                    class="btn-aba px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-zinc-100 hover:border-zinc-700 transition cursor-pointer">
                ✅ Histórico / Realizados
            </button>
        </div>

        <div id="aba-agendados" class="secao-aba space-y-4">
            <div class="space-y-4">
                @forelse($proximos as $agendamento)
                    <div class="bg-zinc-900/50 p-5 rounded-xl border border-zinc-800 flex justify-between items-center">
                        <div>
                            <span class="block font-bold text-zinc-200 text-lg">{{ $agendamento->servico }}</span>
                            <span class="text-xs text-zinc-400">Profissional: <strong class="text-zinc-300">{{ $agendamento->barbeiro->nome }}</strong></span>
                        </div>
                        <div class="text-right">
                            <span class="block font-mono font-bold text-[#D4AF37]">{{ date('d/m/Y', strtotime($agendamento->data_hora)) }}</span>
                            <span class="text-xs font-mono text-zinc-500">{{ date('H:i', strtotime($agendamento->data_hora)) }}h</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 border border-dashed border-zinc-900 rounded-xl">
                        <p class="text-zinc-600 text-sm italic">Você não tem nenhum agendamento marcado para os próximos dias.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div id="aba-realizados" class="secao-aba space-y-4 hidden">
            <div class="space-y-3 opacity-75">
                @forelse($historico as $servicoAntigo)
                    <div class="bg-zinc-900/20 p-4 rounded-xl border border-zinc-900 flex justify-between items-center">
                        <div>
                            <span class="block font-semibold text-zinc-400 text-base line-through">{{ $servicoAntigo->servico }}</span>
                            <span class="text-xs text-zinc-600">Cortado com: {{ $servicoAntigo->barbeiro->nome }}</span>
                        </div>
                        <div class="text-right text-xs text-zinc-600">
                            <span class="block font-mono">{{ date('d/m/Y', strtotime($servicoAntigo->data_hora)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 border border-dashed border-zinc-900 rounded-xl">
                        <p class="text-zinc-600 text-sm italic">Nenhum serviço realizado anteriormente encontrado.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <footer class="border-t border-zinc-900 bg-zinc-950 py-6 text-center text-xs text-zinc-600">
        <p>&copy; 2026 BarberCo. Todos os direitos reservados.</p>
    </footer>

    <script>
        function alternarAba(abaNome, event) {
            // 1. Oculta todos os blocos de conteúdo das abas
            document.querySelectorAll('.secao-aba').forEach(secao => {
                secao.classList.add('hidden');
            });

            // 2. Remove o destaque visual de todos os botões
            document.querySelectorAll('.btn-aba').forEach(btn => {
                btn.classList.remove('bg-[#D4AF37]', 'text-black');
                btn.classList.add('bg-zinc-900', 'border-zinc-800', 'text-zinc-400');
            });

            // 3. Mostra o bloco de conteúdo selecionado
            document.getElementById('aba-' + abaNome).classList.remove('hidden');

            // 4. Aplica o visual dourado de aba ativa no botão clicado
            const btnClicado = event.currentTarget;
            btnClicado.classList.remove('bg-zinc-900', 'border-zinc-800', 'text-zinc-400');
            btnClicado.classList.add('bg-[#D4AF37]', 'text-black');
        }
    </script>

</body>
</html>