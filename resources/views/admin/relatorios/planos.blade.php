<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Planos - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    {{-- Cabeçalho --}}
    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.planos') }}" class="text-xs font-bold text-zinc-500 hover:text-amber-500 transition flex items-center gap-1 mb-1">
                    <i class="la la-arrow-left"></i> Voltar para Assinaturas
                </a>
                <h1 class="text-xl font-black uppercase tracking-widest">Relatório de <span class="text-amber-500">Planos & Caixa</span></h1>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 mt-8 space-y-8">

        {{-- 📊 CARDS DE METRICAS PRINCIPAIS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- Card Faturamento --}}
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-2xl bg-gradient-to-br from-emerald-500/[0.02] to-transparent">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Faturamento Confirmado</span>
                    <i class="la la-wallet text-2xl text-emerald-500"></i>
                </div>
                <div id="cardFaturamento" class="text-2xl font-black text-emerald-400 font-mono">
                    R$ {{ number_format($totalFaturado, 2, ',', '.') }}
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Apenas valores com recebimento confirmado no balcão.</p>
            </div>

            {{-- Card Ativos --}}
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-2xl bg-gradient-to-br from-amber-500/[0.02] to-transparent">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Contratos Ativos</span>
                    <i class="la la-users text-2xl text-amber-500"></i>
                </div>
                <div class="text-2xl font-black text-zinc-200">
                    <span id="cardAtivos">{{ $totalContratados }}</span> <span class="text-xs font-normal text-zinc-500">assinaturas</span>
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Clientes com acesso liberado neste momento.</p>
            </div>

            {{-- Card Cancelados --}}
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-2xl bg-gradient-to-br from-rose-500/[0.02] to-transparent">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Cancelados / Vencidos</span>
                    <i class="la la-user-slash text-2xl text-rose-500"></i>
                </div>
                <div class="text-2xl font-black text-rose-500">
                    <span id="cardCancelados">{{ $totalCancelados }}</span> <span class="text-xs font-normal text-zinc-500">inativos</span>
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Histórico total de quebras de contrato ou expirações.</p>
            </div>

        </div>

        {{-- 🕒 TABELA DE LOGS E MOVIMENTAÇÕES --}}
        <section>
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                    Fluxo Recent de Contratações, Cancelamentos e Reativações
                </h2>
                
                {{-- 🔍 ÁREA DOS FILTROS COMBINADOS --}}
                <div class="flex flex-col sm:flex-row items-center gap-4 bg-zinc-900/40 p-2 rounded-xl border border-zinc-800/40">
                    
                    {{-- Filtro por Plano --}}
                    <div class="flex items-center gap-2 min-w-[200px] w-full sm:w-auto">
                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider whitespace-nowrap">Plano:</span>
                        <select id="filtroPlano" onchange="filtrarTabela()" class="w-full bg-zinc-950 border border-zinc-800 text-xs rounded-lg px-2.5 py-1.5 text-zinc-300 focus:outline-none focus:border-amber-500 transition">
                            <option value="TODOS">Todos</option>
                            @php
                                $nomesPlanosUnicos = $movimentacoes->pluck('nome_plano')->unique();
                            @endphp
                            @foreach($nomesPlanosUnicos as $nomePlano)
                                <option value="{{ trim(strtoupper($nomePlano)) }}">{{ $nomePlano }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro por Mês --}}
                    <div class="flex items-center gap-2 min-w-[180px] w-full sm:w-auto">
                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider whitespace-nowrap">Mês:</span>
                        <select id="filtroMes" onchange="filtrarTabela()" class="w-full bg-zinc-950 border border-zinc-800 text-xs rounded-lg px-2.5 py-1.5 text-zinc-300 focus:outline-none focus:border-amber-500 transition">
                            <option value="TODOS">Todos os Meses</option>
                            <option value="01">Janeiro</option>
                            <option value="02">Fevereiro</option>
                            <option value="03">Março</option>
                            <option value="04">Abril</option>
                            <option value="05">Maio</option>
                            <option value="06">Junho</option>
                            <option value="07">Julho</option>
                            <option value="08">Agosto</option>
                            <option value="09">Setembro</option>
                            <option value="10">Outubro</option>
                            <option value="11">Novembro</option>
                            <option value="12">Dezembro</option>
                        </select>
                    </div>

                </div>
            </div>
            
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-400" id="tabelaMovimentacoes">
                        <thead>
                            <tr class="bg-zinc-950/40 border-b border-zinc-800 text-zinc-500 uppercase text-[9px] font-black tracking-wider">
                                <th class="p-4">Cliente</th>
                                <th class="p-4">Plano</th>
                                <th class="p-4">Data do Evento</th>
                                <th class="p-4 text-center">Status Contrato</th>
                                <th class="p-4 text-center">Pagamento</th>
                                <th class="p-4 text-right">Valor do Ciclo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse($movimentacoes as $mov)
                            @php
                                $mesDoEvento = \Carbon\Carbon::parse($mov->updated_at)->format('m');
                            @endphp
                            {{-- 💡 Injetamos dados limpos de status, pagamento e preço nos atributos da TR --}}
                            <tr class="hover:bg-zinc-950/20 transition linha-movimentacao" 
                                data-plano="{{ trim(strtoupper($mov->nome_plano)) }}"
                                data-mes="{{ $mesDoEvento }}"
                                data-status="{{ $mov->status }}"
                                data-pagamento="{{ $mov->status_pagamento }}"
                                data-preco="{{ $mov->preco_plano }}">
                                <td class="p-4">
                                    <div class="font-bold text-zinc-300">{{ $mov->cliente_nome }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="text-[10px] uppercase bg-zinc-800 text-zinc-400 font-medium px-2 py-0.5 rounded">
                                        {{ $mov->nome_plano }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono text-zinc-500">
                                    {{ \Carbon\Carbon::parse($mov->updated_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4 text-center">
                                    @if($mov->status == 'Ativo')
                                        <span class="text-[9px] uppercase font-bold text-emerald-400 bg-emerald-500/5 px-2 py-0.5 rounded border border-emerald-500/10">Ativo</span>
                                    @elseif($mov->status == 'Inativo')
                                        <span class="text-[9px] uppercase font-bold text-amber-400 bg-amber-500/5 px-2 py-0.5 rounded border border-amber-500/10">Pendente Balcão</span>
                                    @else
                                        <span class="text-[9px] uppercase font-bold text-rose-400 bg-rose-500/5 px-2 py-0.5 rounded border border-rose-500/10">{{ $mov->status }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    @if($mov->status_pagamento == 'Pago')
                                        <span class="text-emerald-400 text-[11px] font-medium"><i class="la la-check-circle"></i> Confirmado</span>
                                    @else
                                        <span class="text-zinc-500 text-[11px] italic">Aguardando</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right font-mono font-bold text-zinc-300">
                                    R$ {{ number_format($mov->preco_plano, 2, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr id="linhaVazia">
                                <td colspan="6" class="p-8 text-center text-zinc-600 italic">Nenhuma movimentação registrada no sistema.</td>
                            </tr>
                            @endforelse
                            
                            {{-- Feedback caso nenhum registro bata com os filtros selecionados --}}
                            <tr id="feedbackFiltroVazio" style="display: none !important;">
                                <td colspan="6" class="p-8 text-center text-zinc-600 italic">Nenhum registro encontrado para a combinação de filtros selecionada.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    {{-- ⚡ LÓGICA DO FILTRO E RECALCULO DE METRICAS EM JAVASCRIPT --}}
    <script>
        function filtrarTabela() {
            const planoSelecionado = document.getElementById('filtroPlano').value.trim();
            const mesSelecionado = document.getElementById('filtroMes').value.trim();
            
            const linhas = document.querySelectorAll('.linha-movimentacao');
            const feedbackVazio = document.getElementById('feedbackFiltroVazio');
            
            // Variáveis de acumulação para os novos totais baseados nas linhas visíveis
            let novoFaturamento = 0;
            let novosAtivos = 0;
            let novosCancelados = 0;
            let algumaLinhaVisivel = false;

            linhas.forEach(linha => {
                const planoDaLinha = (linha.getAttribute('data-plano') || '').trim();
                const mesDaLinha = (linha.getAttribute('data-mes') || '').trim();
                
                // Atributos de dados adicionais para somar as métricas
                const status = linha.getAttribute('data-status');
                const pagamento = linha.getAttribute('data-pagamento');
                const preco = parseFloat(linha.getAttribute('data-preco')) || 0;

                const batePlano = (planoSelecionado === 'TODOS' || planoDaLinha === planoSelecionado);
                const bateMes = (mesSelecionado === 'TODOS' || mesDaLinha === mesSelecionado);

                if (batePlano && bateMes) {
                    linha.style.setProperty('display', 'table-row', 'important');
                    algumaLinhaVisivel = true;

                    // 🧮 Processa as métricas apenas dos itens que passaram pelo filtro
                    if (pagamento === 'Pago') {
                        novoFaturamento += preco;
                    }

                    if (status === 'Ativo') {
                        novosAtivos++;
                    } else if (status !== 'Inativo') { 
                        // Se não for Ativo nem Pendente (Inativo), assume-se cancelamentos/vencidos do histórico
                        novosCancelados++;
                    }

                } else {
                    linha.style.setProperty('display', 'none', 'important');
                }
            });

            // ⚡ Atualiza os elementos HTML dos cards na tela
            document.getElementById('cardFaturamento').innerText = 'R$ ' + novoFaturamento.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('cardAtivos').innerText = novosAtivos;
            document.getElementById('cardCancelados').innerText = novosCancelados;

            // Gerencia o feedback visual de busca vazia
            if (feedbackVazio) {
                if (!algumaLinhaVisivel && linhas.length > 0) {
                    feedbackVazio.style.setProperty('display', 'table-row', 'important');
                } else {
                    feedbackVazio.style.setProperty('display', 'none', 'important');
                }
            }
        }
    </script>

</body>
</html>