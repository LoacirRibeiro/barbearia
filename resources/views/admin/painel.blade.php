<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        .gold-border { border-color: #D4AF37; }
        dialog::backdrop { background-color: rgba(9, 9, 11, 0.85); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black uppercase tracking-widest">Painel <span class="gold-text">Admin</span></h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Gestão Operacional Avançada</p>
            </div>
            <a href="/" class="text-xs bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-lg hover:border-zinc-600 transition">Voltar para o Site</a>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 mt-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500 flex items-center gap-2">
            <i class="la la-wallet gold-text text-lg"></i> Faturamento e Auditoria de Fluxo
        </h2>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
            {{-- 📊 NOVO: Botão para abrir o Relatório Financeiro --}}
            <!-- <a href="{{ route('admin.planos.relatorio') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold px-4 py-2.5 rounded-xl border border-zinc-800 hover:border-zinc-700 transition flex items-center gap-2">
                <i class="la la-chart-bar text-base text-amber-500"></i> Ver Relatório
            </a> -->

            {{-- BOTÃO: ASSINATURAS --}}
            <a href="{{ route('admin.planos') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg">
                <i class="la la-certificate text-base text-emerald-500"></i> Assinaturas
            </a>

            {{-- BOTÃO: HORÁRIOS --}}
            <a href="{{ route('admin.agenda') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg">
                <i class="la la-clock text-base text-zinc-400"></i> Horários
            </a>

            {{-- BOTÃO: GRÁFICOS --}}
            <a href="{{ route('admin.colaboradores.evolucao') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-pink-500 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg">
                <i class="la la-line-chart text-base"></i> Gráficos
            </a>

            {{-- BOTÃO: GESTÃO DE COLABORADORES --}}
            <a href="{{ route('admin.colaboradores') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg">
                <i class="la la-users text-base text-amber-500"></i> Colaboradores
            </a>

            {{-- BOTÃO: RELATÓRIOS --}}
            <a href="{{ route('admin.relatorio_mensal') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-amber-400 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg">
                <i class="la la-chart-bar text-base"></i> Relatórios
            </a>

            {{-- BOTÃO: GERENCIAR ESTOQUE --}}
            <a href="{{ route('admin.estoque') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg">
                <i class="la la-box text-base text-amber-500"></i> Estoque
            </a>

            {{-- BOTÃO DE DESTAQUE: CAIXA --}}
            <a href="{{ route('admin.caixa') }}" class="text-xs bg-amber-500 hover:bg-amber-400 text-zinc-950 px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg shadow-amber-500/10 hover:scale-[1.02]">
                <i class="la la-cash-register text-base"></i> Caixa
            </a>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 space-y-8">

        {{-- FORMULÁRIO DE FILTROS AVANÇADOS --}}
        <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Auditar Dia Específico</label>
                    <input type="text" 
                        name="dia_especifico" 
                        placeholder="DD/MM/AAAA"
                        maxlength="10"
                        onkeypress="mascaraData(this)"
                        value="{{ $statsFinanceiras['dia_selecionado_formatado'] }}" 
                        class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
                </div>

                <div>
                    <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Filtrar por Mês Completo</label>
                    <select name="mes_ano" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200 appearance-none">
                        @php
                            $meses = [
                                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', 
                                '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', 
                                '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', 
                                '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                            ];
                            $anoAtual = date('Y');
                            $mesAnoSelecionado = request('mes_ano', date('m/Y'));
                        @endphp

                        @foreach($meses as $num => $nome)
                            <option value="{{ $num }}/{{ $anoAtual }}" {{ $mesAnoSelecionado == "$num/$anoAtual" ? 'selected' : '' }}>
                                {{ $nome }} de {{ $anoAtual }}
                            </option>
                        @endforeach
                        
                        @foreach($meses as $num => $nome)
                            <option value="{{ $num }}/{{ $anoAtual - 1 }}" {{ $mesAnoSelecionado == "$num/".($anoAtual - 1) ? 'selected' : '' }}>
                                {{ $nome }} de {{ $anoAtual - 1 }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full gold-bg text-zinc-950 font-bold text-sm py-3.5 rounded-xl hover:opacity-90 transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="la la-filter text-base"></i> Filtrar Painel
                </button>
            </form>
        </section>

        {{-- MÉTRICAS MACROS --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] uppercase font-black text-zinc-500 tracking-wider block">Faturamento Bruto</span>
                    <span class="text-2xl font-black text-zinc-100 block mt-1">
                        R$ {{ number_format($statsFinanceiras['faturamento_total'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center">
                    <i class="la la-wallet gold-text text-xl"></i>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] uppercase font-black text-amber-500 tracking-wider block">Venda de Produtos (Casa)</span>
                    <span class="text-2xl font-black text-amber-400 block mt-1">
                        R$ {{ number_format($statsFinanceiras['total_produtos'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center">
                    <i class="la la-box text-amber-400 text-xl"></i>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] uppercase font-black text-zinc-500 tracking-wider block">Lucro Líquido (Casa)</span>
                    <span class="text-2xl font-black text-emerald-400 block mt-1">
                        R$ {{ number_format($statsFinanceiras['lucro_barbearia'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center">
                    <i class="la la-money-bill-wave text-emerald-500 text-xl"></i>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 p-5 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-[10px] uppercase font-black text-zinc-500 tracking-wider block">Ticket Médio</span>
                    <span class="text-2xl font-black gold-text block mt-1">
                        R$ {{ number_format($statsFinanceiras['ticket_medio'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center">
                    <i class="la la-chart-line gold-text text-xl"></i>
                </div>
            </div>
        </section>

        {{-- 📊 CARDS LADO A LADO COM LISTAGEM EMBUTIDA (INTEGRADOS AO FILTRO) --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Card Esquerdo: Resumo e Listagem de Serviços --}}
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 p-6 flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-zinc-100 flex items-center gap-2">
                                <i class="la la-scissors text-zinc-400"></i> Serviços Efetuados
                            </h3>
                            <p class="text-[10px] text-zinc-500">Mão de obra realizada no período filtrado</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] uppercase font-black text-zinc-500 block">Total em Serviços</span>
                            <span class="text-2xl font-black gold-text font-mono">
                                R$ {{ number_format(($statsFinanceiras['faturamento_total'] ?? 0) - ($statsFinanceiras['total_produtos'] ?? 0), 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Tabela Interna de Serviços --}}
                    <div class="overflow-y-auto max-h-64 pr-1 mt-4 border-t border-zinc-800/60 pt-4">
                        <table class="w-full text-left text-xs text-zinc-400">
                            <thead>
                                <tr class="text-zinc-500 border-b border-zinc-800/80 uppercase text-[9px] font-black">
                                    <th class="pb-2">Hora</th>
                                    <th class="pb-2">Descrição do Serviço</th>
                                    <th class="pb-2 text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/40">
                                @php $temServico = false; @endphp
                                @foreach($atendimentosDoPeriodo as $at)
                                    @if(!isset($at->tipo) || $at->tipo !== 'produto')
                                        @php $temServico = true; @endphp
                                        <tr class="hover:bg-zinc-950/20">
                                            <td class="py-2 font-mono text-zinc-500">
                                                {{ \Carbon\Carbon::parse($at->data_hora ?? $at->created_at)->format('H:i') }}
                                            </td>
                                            <td class="py-2 font-bold text-zinc-200">
                                                {{ $at->servico ?? $at->descricao }}
                                            </td>
                                            <td class="py-2 text-right font-semibold font-mono text-zinc-300">
                                                R$ {{ number_format(($at->preco ?? $at->subtotal), 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$temServico)
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-zinc-600 italic">Nenhum serviço efetuado neste período.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 my-4">
                    <div class="bg-zinc-950/40 border border-zinc-800/50 rounded-xl p-3">
                        <span class="text-[9px] uppercase font-bold text-zinc-500 block mb-0.5">Qtd. Total Serviços</span>
                        <span class="text-lg font-black text-zinc-200 font-mono">{{ $qtdTotalServicos }}</span>
                    </div>
                    <div class="bg-zinc-950/40 border border-zinc-800/50 rounded-xl p-3">
                        <span class="text-[9px] uppercase font-bold text-zinc-500 block mb-0.5">Ticket Médio</span>
                        <span class="text-lg font-black text-amber-500 font-mono">R$ {{ number_format($ticketMedioServicos, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Card Direito: Resumo e Listagem de Produtos --}}
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 p-6 flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-wider text-amber-500 flex items-center gap-2">
                                <i class="la la-shopping-bag"></i> Vendas de Produtos
                            </h3>
                            <p class="text-[10px] text-zinc-500">Saídas registradas na vitrine do período filtrado</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] uppercase font-black text-zinc-500 block">Total em Produtos</span>
                            <span class="text-2xl font-black text-amber-400 font-mono">
                                R$ {{ number_format($statsFinanceiras['total_produtos'] ?? 0, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Tabela Interna de Produtos --}}
                    <div class="overflow-y-auto max-h-64 pr-1 mt-4 border-t border-zinc-800/60 pt-4">
                        <table class="w-full text-left text-xs text-zinc-400">
                            <thead>
                                <tr class="text-zinc-500 border-b border-zinc-800/80 uppercase text-[9px] font-black">
                                    <th class="pb-2">Hora</th>
                                    <th class="pb-2">Nome do Produto</th>
                                    <th class="pb-2 text-center">Qtd</th>
                                    <th class="pb-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/40">
                                @php $temProduto = false; @endphp
                                @foreach($atendimentosDoPeriodo as $at)
                                    @if(isset($at->tipo) && $at->tipo === 'produto')
                                        @php $temProduto = true; @endphp
                                        <tr class="hover:bg-zinc-950/20">
                                            <td class="py-2 font-mono text-zinc-500">
                                                {{ \Carbon\Carbon::parse($at->data_hora ?? $at->created_at)->format('H:i') }}
                                            </td>
                                            <td class="py-2 font-bold text-amber-100">
                                                {{ $at->servico ?? $at->descricao }}
                                            </td>
                                            <td class="py-2 text-center font-bold text-zinc-400">
                                                {{ $at->quantidade ?? 1 }}
                                            </td>
                                            <td class="py-2 text-right font-semibold font-mono text-amber-400">
                                                R$ {{ number_format(($at->preco ?? $at->subtotal), 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$temProduto)
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-zinc-600 italic">Nenhum produto vendido neste período.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 my-4">
                    <div class="bg-zinc-950/40 border border-zinc-800/50 rounded-xl p-3">
                        <span class="text-[9px] uppercase font-bold text-zinc-500 block mb-0.5">Quantidade total de produtos:</span>
                        <span class="text-lg font-black text-zinc-200 font-mono">{{ $statsFinanceiras['quantidade_produtos_vendidos'] ?? 0 }} itens</span>
                    </div>
                    <div class="bg-zinc-950/40 border border-zinc-800/50 rounded-xl p-3">
                        <span class="text-[9px] uppercase font-bold text-zinc-500 block mb-0.5">Ticket médio por item comprado:</span>
                        <span class="text-lg font-black text-amber-500 font-mono">R$ {{ number_format($statsFinanceiras['ticket_medio_produtos'] ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </section>

        {{-- 📋 MOVIMENTAÇÕES COMPLETA DO DIA (FLUXO INTEGRADO DIRETOR NA TELA) --}}
        <!-- <section class="bg-zinc-900 rounded-2xl border border-zinc-800 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-zinc-800 pb-4 mb-4 gap-2">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-zinc-100 flex items-center gap-2">
                        <i class="la la-list gold-text"></i> Extrato de Movimentações do Período Filtrado
                    </h3>
                    <p class="text-[10px] text-zinc-500">Exibindo fluxo completo unificado (Serviços executados e Produtos comercializados)</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-400">
                    <thead>
                        <tr class="text-zinc-500 border-b border-zinc-800 uppercase text-[9px] font-black">
                            <th class="pb-3">Data / Hora</th>
                            <th class="pb-3">Item / Descrição</th>
                            <th class="pb-3">Tipo de Operação</th>
                            <th class="pb-3 text-right">Valor Unitário</th>
                            <th class="pb-3 text-right">Qtd</th>
                            <th class="pb-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/40">
                        @forelse($atendimentosDoPeriodo as $at)
                        <tr class="hover:bg-zinc-950/30 transition">
                            {{-- Data e Hora --}}
                            <td class="py-3 font-mono text-zinc-500">
                                {{ \Carbon\Carbon::parse($at->data_hora ?? $at->created_at)->format('d/m/Y H:i') }}
                            </td>
                            
                            {{-- Descrição --}}
                            <td class="py-3 font-bold text-zinc-200">
                                {{ $at->servico ?? $at->descricao }}
                            </td>
                            
                            {{-- Badge de Operação --}}
                            <td class="py-3">
                                @if(isset($at->tipo) && $at->tipo === 'produto')
                                    <span class="text-[9px] bg-amber-950/80 border border-amber-900/50 text-amber-400 font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">Produto</span>
                                @else
                                    <span class="text-[9px] bg-zinc-800 text-zinc-300 font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">Serviço</span>
                                @endif
                            </td>
                            
                            {{-- 🛠️ VALOR UNITÁRIO CORRIGIDO --}}
                            <td class="py-3 text-right text-zinc-400 font-mono">
                                @php
                                    // Se existir preco_unitario original usa, senão calcula dinamicamente pelo subtotal/quantidade
                                    $valorUnitario = $at->preco_unitario ?? ($at->subtotal / ($at->quantidade ?? 1));
                                @endphp
                                R$ {{ number_format($valorUnitario, 2, ',', '.') }}
                            </td>
                            
                            {{-- Quantidade --}}
                            <td class="py-3 text-right text-zinc-500 font-bold">
                                {{ $at->quantidade ?? 1 }}
                            </td>
                            
                            {{-- Subtotal --}}
                            <td class="py-3 text-right font-black font-mono {{ $at->subtotal > 0 ? 'gold-text' : 'text-zinc-600' }}">
                                R$ {{ number_format($at->subtotal, 2, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-600 italic bg-zinc-950/10">
                                Nenhuma movimentação registrada para os critérios de filtragem selecionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section> -->

    </main>

    <script>
        function mascaraData(campo) {
            setTimeout(function() {
                var v = campo.value;
                v = v.replace(/\D/g, ""); 
                v = v.replace(/^(\d{2})(\d)/, "$1/$2"); 
                v = v.replace(/^(\d{2})\/(\d{2})(\d)/, "$1/$2/$3"); 
                campo.value = v;
            }, 1);
        }
    </script>
</body>
</html>