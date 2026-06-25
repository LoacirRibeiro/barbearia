<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fechamento Mensal - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        .gold-border { border-color: #D4AF37; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black uppercase tracking-widest">Fechamento <span class="gold-text">Mensal</span></h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Auditoria Geral e Fluxo de Meios de Pagamento</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Formulário de Filtros Avançados --}}
                <form method="GET" class="flex flex-wrap items-center gap-2 bg-zinc-900 p-2 rounded-xl border border-zinc-800">
                    
                    {{-- Alternador de Filtro --}}
                    <select id="tipo_filtro" onchange="alternarCamposFiltro()" class="bg-zinc-950 text-xs font-bold uppercase p-1.5 rounded-lg outline-none text-amber-400 border border-zinc-800">
                        <option value="mes" {{ empty($filtroData) ? 'selected' : '' }}>Por Mês</option>
                        <option value="dia" {{ !empty($filtroData) ? 'selected' : '' }}>Por Dia</option>
                    </select>

                    {{-- Campos de Mês/Ano --}}
                    <div id="wrapper_mes_ano" class="flex gap-1 {{ !empty($filtroData) ? 'hidden' : '' }}">
                        <select name="mes" class="bg-transparent text-xs font-bold uppercase px-2 outline-none text-zinc-300 cursor-pointer">
                            @foreach($mesesPortuguis as $num => $nome)
                                <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nome }}</option>
                            @endforeach
                        </select>
                        <select name="ano" class="bg-transparent text-xs font-bold uppercase px-2 outline-none text-zinc-300 cursor-pointer">
                            @foreach(range(Carbon\Carbon::now()->year, Carbon\Carbon::now()->year - 3) as $a)
                                <option value="{{ $a }}" {{ $ano == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Campo de Data Específica (Calendário Nativo em PT-BR) --}}
                    <div id="wrapper_data" class="{{ empty($filtroData) ? 'hidden' : '' }}">
                        <input type="date" name="data" value="{{ $filtroData }}" class="bg-zinc-950 text-xs font-bold text-zinc-300 p-1.5 rounded-lg outline-none border border-zinc-800 [color-scheme:dark]">
                    </div>

                    <button type="submit" class="gold-bg text-zinc-950 px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition hover:opacity-90">Filtrar</button>
                    
                    @if(!empty($filtroData) || $mes != Carbon\Carbon::now()->month)
                        <a href="{{ route('admin.relatorio_mensal') }}" class="bg-zinc-800 text-zinc-400 p-1.5 rounded-lg hover:text-zinc-200 text-xs" title="Limpar Filtros"><i class="la la-times-circle"></i></a>
                    @endif
                </form>

                <a href="{{ route('admin.painel') }}" class="text-xs bg-zinc-900 border border-zinc-800 px-4 py-2.5 rounded-xl hover:border-zinc-600 transition flex items-center gap-2">
                    <i class="la la-arrow-left"></i> Voltar ao Painel
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 mt-8 space-y-6">

        {{-- 📊 CARDS DE GRANDES TOTAIS DO MÊS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-zinc-900 p-5 rounded-2xl border border-zinc-800 shadow-xl">
                <span class="text-[9px] uppercase font-black text-zinc-500 tracking-wider block">Faturamento Bruto</span>
                <span class="text-2xl font-black gold-text block mt-1">R$ {{ number_format($faturamentoBrutoMês, 2, ',', '.') }}</span>
                <span class="text-[9px] text-zinc-600 font-medium block mt-0.5">Total bruto originado de vendas</span>
            </div>

            <div class="bg-zinc-900 p-5 rounded-2xl border border-zinc-800 shadow-xl">
                <span class="text-[9px] uppercase font-black text-zinc-500 tracking-wider block">Injeções (Suprimentos)</span>
                <span class="text-2xl font-black text-emerald-400 block mt-1">R$ {{ number_format($totalSuprimentos, 2, ',', '.') }}</span>
                <span class="text-[9px] text-zinc-600 font-medium block mt-0.5">Dinheiro externo colocado para troco</span>
            </div>

            <div class="bg-zinc-900 p-5 rounded-2xl border border-zinc-800 shadow-xl">
                <span class="text-[9px] uppercase font-black text-zinc-500 tracking-wider block">Retiradas (Sangrias)</span>
                <span class="text-2xl font-black text-rose-400 block mt-1">R$ {{ number_format($totalSangrias, 2, ',', '.') }}</span>
                <span class="text-[9px] text-zinc-600 font-medium block mt-0.5">Dinheiro recolhido por segurança/gastos</span>
            </div>

            <div class="bg-zinc-900 p-5 rounded-2xl border border-zinc-800 shadow-xl">
                <span class="text-[9px] uppercase font-black text-zinc-500 tracking-wider block">Saldo Líquido em Dinheiro</span>
                @php $saldoDinheiroLiquido = ($entradasVendas['Dinheiro'] + $totalSuprimentos) - $totalSangrias; @endphp
                <span class="text-2xl font-black block mt-1 {{ $saldoDinheiroLiquido >= 0 ? 'text-zinc-100' : 'text-rose-500' }}">
                    R$ {{ number_format($saldoDinheiroLiquido, 2, ',', '.') }}
                </span>
                <span class="text-[9px] text-zinc-600 font-medium block mt-0.5">Volume teórico atualizado da gaveta física</span>
            </div>
        </div>

        {{-- 💳 DETALHAMENTO POR CANAL DE ENTRADA --}}
        <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-xl">
            <h2 class="text-xs font-black uppercase tracking-wider mb-4 text-zinc-400"><i class="la la-pie-chart gold-text"></i> Divisão por Meios de Recebimento</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-zinc-950 p-4 rounded-xl border border-zinc-800/60">
                    <p class="text-[9px] font-black uppercase text-zinc-500">💵 Dinheiro (Vendas)</p>
                    <p class="text-base font-black text-zinc-200 mt-1">R$ {{ number_format($entradasVendas['Dinheiro'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-zinc-950 p-4 rounded-xl border border-zinc-800/60">
                    <p class="text-[9px] font-black uppercase text-zinc-500">⚡ Pix</p>
                    <p class="text-base font-black text-zinc-200 mt-1">R$ {{ number_format($entradasVendas['Pix'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-zinc-950 p-4 rounded-xl border border-zinc-800/60">
                    <p class="text-[9px] font-black uppercase text-zinc-500">💳 Cartão Débito</p>
                    <p class="text-base font-black text-zinc-200 mt-1">R$ {{ number_format($entradasVendas['Cartão de Débito'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-zinc-950 p-4 rounded-xl border border-zinc-800/60">
                    <p class="text-[9px] font-black uppercase text-zinc-500">💳 Cartão Crédito</p>
                    <p class="text-base font-black text-zinc-200 mt-1">R$ {{ number_format($entradasVendas['Cartão de Crédito'], 2, ',', '.') }}</p>
                </div>
            </div>
        </section>

        {{-- 📅 TABELA COMPLETA DIÁRIA DETALHADA --}}
        <section class="bg-zinc-900 rounded-2xl border border-zinc-800 shadow-xl overflow-hidden">
            <div class="p-6 border-b border-zinc-800">
                <h2 class="text-xs font-black uppercase tracking-wider text-zinc-400"><i class="la la-calendar-alt gold-text"></i> Histórico Diário Cronológico</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-950 border-b border-zinc-800 text-[9px] uppercase font-black text-zinc-500 tracking-wider">
                            <th class="py-4 px-6">Data</th>
                            <th class="py-4 px-4 text-right">Dinheiro (R$)</th>
                            <th class="py-4 px-4 text-right">Pix (R$)</th>
                            <th class="py-4 px-4 text-right">Débito (R$)</th>
                            <th class="py-4 px-4 text-right">Crédito (R$)</th>
                            <th class="py-4 px-6 text-right gold-text">Total do Dia (R$)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50 text-xs font-medium text-zinc-300">
                        @forelse($fluxoDiario as $dia)
                            <tr class="hover:bg-zinc-800/30 transition">
                                <td class="py-4 px-6 font-bold text-zinc-400">{{ \Carbon\Carbon::parse($dia->data)->format('d/m/Y') }}</td>
                                <td class="py-4 px-4 text-right">R$ {{ number_format($dia->dinheiro, 2, ',', '.') }}</td>
                                <td class="py-4 px-4 text-right text-zinc-200">R$ {{ number_format($dia->pix, 2, ',', '.') }}</td>
                                <td class="py-4 px-4 text-right">R$ {{ number_format($dia->debito, 2, ',', '.') }}</td>
                                <td class="py-4 px-4 text-right">R$ {{ number_format($dia->credito, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-black gold-text text-sm">R$ {{ number_format($dia->total_dia, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 italic text-zinc-600 text-xs">Nenhum lançamento efetuado neste período.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script>
        function alternarCamposFiltro() {
            const tipo = document.getElementById('tipo_filtro').value;
            const wrapperMesAno = document.getElementById('wrapper_mes_ano');
            const wrapperData = document.getElementById('wrapper_data');
            
            if (tipo === 'dia') {
                wrapperMesAno.classList.add('hidden');
                wrapperData.classList.remove('hidden');
                // Desativa os campos de mês para não enviar sujeira na URL
                wrapperMesAno.querySelectorAll('select').forEach(el => el.disabled = true);
                wrapperData.querySelector('input').disabled = false;
            } else {
                wrapperMesAno.classList.remove('hidden');
                wrapperData.classList.add('hidden');
                wrapperMesAno.querySelectorAll('select').forEach(el => el.disabled = false);
                wrapperData.querySelector('input').disabled = true;
                wrapperData.querySelector('input').value = '';
            }
        }

        // Executa uma vez ao carregar a página para fixar o estado atual do filtro aplicado
        document.addEventListener("DOMContentLoaded", function() {
            alternarCamposFiltro();
        });
    </script>

</body>
</html>