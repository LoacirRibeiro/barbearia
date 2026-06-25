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
            <a href="{{ route('admin.planos.relatorio') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold px-4 py-2.5 rounded-xl border border-zinc-800 hover:border-zinc-700 transition flex items-center gap-2">
                <i class="la la-chart-bar text-base text-amber-500"></i> Ver Relatório
            </a>

            <!-- <span class="text-xs bg-emerald-500/10 text-emerald-400 px-3 py-2 rounded-xl font-bold border border-emerald-500/20">
                {{ $planosAtivos->count() }} Ativos
            </span> -->

            {{-- 🛒 NOVO ATALHO: Lançamento Manual / Caixa Balcão --}}
            <a href="{{ route('admin.caixa') }}" class="bg-amber-500 hover:bg-amber-400 text-zinc-950 transition px-4 py-2.5 rounded-xl text-xs font-black flex items-center justify-center gap-2 shadow-lg shadow-amber-500/10">
                <i class="la la-cash-register text-base"></i> Abrir Caixa (Balcão)
            </a>

            {{-- BOTÃO DE ATALHO PARA O CONTROLE DE PLANOS/ASSINATURAS --}}
            <a href="{{ route('admin.planos') }}" class="bg-zinc-900 border border-zinc-800 hover:border-emerald-500/50 text-zinc-300 hover:text-emerald-400 transition px-4 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2">
                <i class="la la-certificate text-base text-emerald-500"></i> Assinaturas
            </a>
            
            {{-- BOTÃO DE ATALHO PARA A TELA DE HORÁRIOS --}}
            <a href="{{ route('admin.agenda') }}" class="bg-zinc-900 border border-zinc-800 hover:border-zinc-700 text-zinc-200 transition px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="la la-clock text-base text-zinc-400"></i> Horários
            </a>

            {{-- BOTÃO: GERENCIAR ESTOQUE --}}
            <a href="{{ route('admin.estoque') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition flex items-center justify-center gap-2 font-black uppercase tracking-wider shadow-lg">
                <i class="la la-box text-base text-amber-500"></i> GERENCIAR ESTOQUE
            </a>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 space-y-8">

        {{-- FORMULÁRIO DE FILTROS AVANÇADOS --}}
        <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                
                {{-- Filtro Diário --}}
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

                {{-- Filtro por Mês do Ano --}}
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
                        
                        {{-- Listagem do Ano Anterior --}}
                        @foreach($meses as $num => $nome)
                            <option value="{{ $num }}/{{ $anoAtual - 1 }}" {{ $mesAnoSelecionado == "$num/".($anoAtual - 1) ? 'selected' : '' }}>
                                {{ $nome }} de {{ $anoAtual - 1 }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botão Filtrar --}}
                <button type="submit" class="w-full gold-bg text-zinc-950 font-bold text-sm py-3.5 rounded-xl hover:opacity-90 transition flex items-center justify-center gap-2">
                    <i class="la la-filter text-base"></i> Filtrar Painel
                </button>
            </form>
        </section>

        {{-- PERFORMANCE DETALHADA DO BARBEIRO COM LISTAGEM DE HISTÓRICO --}}
        <section>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500 mb-4 flex items-center gap-2">
                <i class="la la-users gold-text text-lg"></i> Relatório de Serviços Efetuados por Barbeiro (No Período)
            </h2>
            <div class="grid grid-cols-1 gap-6">
                @forelse($barbeirosRelatorio as $rep)
                <div class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden grid grid-cols-1 lg:grid-cols-3">
                    
                    {{-- Lado Esquerdo: Estatísticas Consolidadas --}}
                    <div class="p-6 border-r border-zinc-800 bg-zinc-950/20 flex flex-col justify-between space-y-4">
                        <div>
                            <span class="text-[9px] uppercase font-black bg-zinc-800 px-2 py-1 rounded text-zinc-400">Profissional</span>
                            <h3 class="text-xl font-black uppercase mt-1 tracking-wider text-zinc-100">{{ $rep['nome'] }}</h3>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between"><span class="text-zinc-500">Comuns Pagos</span><span class="font-bold">{{ $rep['total_comuns'] }}</span></div>
                            <div class="flex justify-between"><span class="text-zinc-500">Via Assinatura</span><span class="font-bold text-emerald-500">{{ $rep['total_planos'] }}</span></div>
                        </div>
                        <div class="pt-4 border-t border-zinc-800 grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[9px] uppercase font-black text-zinc-500 block">Faturamento</span>
                                <span class="text-base font-bold text-zinc-200">R$ {{ number_format($rep['faturamento_comum'], 2, ',', '.') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] uppercase font-black text-[#D4AF37] block">Comissão (50%)</span>
                                <span class="text-lg font-black gold-text">R$ {{ number_format($rep['comissao_receber'], 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Lado Direito: Tabela Analítica de Ordens --}}
                    <div class="lg:col-span-2 p-6 overflow-x-auto">
                        <span class="text-[10px] uppercase font-black text-zinc-500 block mb-3 tracking-wider">Histórico Analítico de Comissões</span>
                        <table class="w-full text-left text-xs text-zinc-400">
                            <thead>
                                <tr class="border-b border-zinc-800 text-zinc-500 uppercase text-[9px] font-black">
                                    <th class="pb-2">Data/Hora</th>
                                    <th class="pb-2">Serviço Prestado</th>
                                    <th class="pb-2">Tipo</th>
                                    <th class="pb-2 text-right">Preço</th>
                                    <th class="pb-2 text-right">Sua Comissão</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800/50">
                                @forelse($rep['detalhes_servicos'] as $servicoEfetuado)
                                <tr>
                                    {{-- 🌟 MODIFICADO: Uso explícito do Carbon para formatar dia/mês e hora no formato brasileiro --}}
                                    <td class="py-2.5 font-mono text-zinc-400">
                                        {{ \Carbon\Carbon::parse($servicoEfetuado->data_hora)->format('d/m H:i') }}
                                    </td>
                                    <td class="py-2.5 font-bold text-zinc-200">{{ $servicoEfetuado->servico }}</td>
                                    <td class="py-2.5">
                                        @if($servicoEfetuado->preco > 0)
                                            <span class="text-[9px] uppercase bg-zinc-800 text-zinc-300 font-bold px-1.5 py-0.5 rounded">Avulso</span>
                                        @else
                                            <span class="text-[9px] uppercase bg-emerald-950/50 text-emerald-400 font-bold px-1.5 py-0.5 rounded">Clube</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 text-right font-semibold {{ $servicoEfetuado->preco > 0 ? 'text-zinc-300' : 'text-zinc-600' }}">
                                        R$ {{ number_format($servicoEfetuado->preco, 2, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 text-right font-bold {{ $servicoEfetuado->preco > 0 ? 'gold-text' : 'text-zinc-600' }}">
                                        R$ {{ number_format($servicoEfetuado->preco * 0.50, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-zinc-600 italic">Sem atendimentos neste período configurado.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                <div class="text-center text-sm text-zinc-500 italic p-8 bg-zinc-900 rounded-2xl border border-zinc-800">Nenhum profissional listado.</div>
                @endforelse
            </div>
        </section>

        {{-- SEÇÃO 3: CLUBES --}}
        <section>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500 mb-4 flex items-center gap-2">
                <i class="la la-certificate gold-text text-lg"></i> Uso do Clube de Benefícios (No Período)
            </h2>
            <div class="flex flex-wrap gap-4">
                @forelse($cortesPlanoPorCategoria as $cat)
                <div class="bg-zinc-900 px-6 py-4 rounded-xl border border-zinc-800 flex items-center gap-4">
                    <div class="w-10 h-10 gold-bg text-black rounded-full flex items-center justify-center font-black">{{ $cat->total }}</div>
                    <div>
                        <span class="text-[10px] uppercase font-black text-zinc-500 block">Categoria</span>
                        <span class="font-bold text-zinc-100">{{ $cat->categoria ?? 'Não Definida' }}</span>
                    </div>
                </div>
                @empty
                <div class="text-sm text-zinc-500 italic p-6 bg-zinc-900 rounded-xl border border-zinc-800 w-full">Nenhum consumo por assinatura no período selecionado.</div>
                @endforelse
            </div>
        </section>

    </main>

    {{-- MODAL INTERNO - AUDITORIA DO DIA --}}
    <dialog id="modal_dia" class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-3xl w-full p-6 text-zinc-100">
        <div class="flex justify-between items-center border-b border-zinc-800 pb-4 mb-4">
            <div>
                <h3 class="text-base font-black uppercase">Auditoria de Vendas: Dia</h3>
                <p class="text-[10px] text-zinc-500">Exibindo listagem cronológica</p>
            </div>
            <button onclick="document.getElementById('modal_dia').close()" class="text-zinc-400 hover:text-zinc-100 text-xl"><i class="la la-times"></i></button>
        </div>
        <div class="overflow-y-auto max-h-[60vh]">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-zinc-500 border-b border-zinc-800 uppercase text-[9px] font-black">
                        <th class="pb-2">Hora</th>
                        <th class="pb-2">Cliente</th>
                        <th class="pb-2">Profissional</th>
                        <th class="pb-2">Serviço</th>
                        <th class="pb-2 text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/40">
                    @forelse($atendimentosDoDia as $at)
                    <tr>
                        <td class="py-2.5 font-mono text-zinc-500">{{ \Carbon\Carbon::parse($at->data_hora)->format('H:i') }}</td>
                        <td class="py-2.5 font-bold text-zinc-300">{{ $at->cliente_nome }}</td>
                        <td class="py-2.5 text-zinc-400">{{ $at->barbeiro_nome }}</td>
                        <td class="py-2.5 text-zinc-300">{{ $at->servico }}</td>
                        <td class="py-2.5 text-right font-black {{ $at->preco > 0 ? 'gold-text' : 'text-zinc-600' }}">R$ {{ number_format($at->preco, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 text-center text-zinc-500 italic">Nenhum atendimento agendado ou executado neste dia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </dialog>

    {{-- MODAL INTERNO - AUDITORIA DO PERÍODO --}}
    <dialog id="modal_periodo" class="bg-zinc-900 border border-zinc-800 rounded-2xl max-w-3xl w-full p-6 text-zinc-100">
        <div class="flex justify-between items-center border-b border-zinc-800 pb-4 mb-4">
            <div>
                <h3 class="text-base font-black uppercase">Auditoria de Vendas: Histórico do Período</h3>
                <p class="text-[10px] text-zinc-500">Exibindo fluxo completo de faturamento</p>
            </div>
            <button onclick="document.getElementById('modal_periodo').close()" class="text-zinc-400 hover:text-zinc-100 text-xl"><i class="la la-times"></i></button>
        </div>
        <div class="overflow-y-auto max-h-[60vh]">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-zinc-500 border-b border-zinc-800 uppercase text-[9px] font-black">
                        <th class="pb-2">Data</th>
                        <th class="pb-2">Cliente</th>
                        <th class="pb-2">Profissional</th>
                        <th class="pb-2">Serviço</th>
                        <th class="pb-2 text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/40">
                    @forelse($atendimentosDoPeriodo as $at)
                    <tr>
                        <td class="py-2.5 font-mono text-zinc-500">{{ \Carbon\Carbon::parse($at->data_hora)->format('d/m/Y H:i') }}</td>
                        <td class="py-2.5 font-bold text-zinc-300">{{ $at->cliente_nome }}</td>
                        <td class="py-2.5 text-zinc-400">{{ $at->barbeiro_nome }}</td>
                        <td class="py-2.5 text-zinc-300">{{ $at->servico }}</td>
                        <td class="py-2.5 text-right font-black {{ $at->preco > 0 ? 'text-zinc-200' : 'text-zinc-600' }}">R$ {{ number_format($at->preco, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 text-center text-zinc-500 italic">Nenhum faturamento registrado no intervalo selecionado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </dialog>

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