<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Colaboradores - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        dialog::backdrop { background-color: rgba(9, 9, 11, 0.85); backdrop-filter: blur(4px); }
        .swal2-popup { background: #18181b !important; color: #f4f4f5 !important; border: 1px solid #27272a !important; border-radius: 1rem !important; }
        .swal2-title { color: #fff !important; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black uppercase tracking-widest">Gestão de <span class="gold-text">Colaboradores</span></h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Performance e Comissões da Equipe</p>
            </div>
            <a href="{{ route('admin.painel') }}" class="text-xs bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-lg hover:border-zinc-600 transition flex items-center gap-2">
                <i class="la la-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 mt-8 space-y-6">

        {{-- FILTRO DE PERÍODO --}}
        <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Auditar Dia Específico</label>
                    <input type="text" name="dia_especifico" placeholder="DD/MM/AAAA" maxlength="10" onkeypress="mascaraData(this)" value="{{ request('dia_especifico') }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
                </div>
                <div>
                    <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Filtrar por Mês Completo</label>
                    <select name="mes_ano" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200 appearance-none">
                        @php
                            $meses = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
                            $anoAtual = date('Y');
                            $mesAnoSelecionado = request('mes_ano', date('m/Y'));
                        @endphp
                        @foreach($meses as $num => $nome)
                            <option value="{{ $num }}/{{ $anoAtual }}" {{ $mesAnoSelecionado == "$num/$anoAtual" ? 'selected' : '' }}>{{ $nome }} de {{ $anoAtual }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full gold-bg text-zinc-950 font-bold text-sm py-3.5 rounded-xl hover:opacity-90 transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="la la-filter text-base"></i> Filtrar Relatórios
                </button>
            </form>
        </section>

        {{-- LAYOUT PRINCIPAL EM DUAS COLUNAS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- COLUNA 1: LISTA DE CARDS DOS COLABORADORES --}}
            <div class="space-y-4 lg:col-span-1">
                <h3 class="text-xs font-black uppercase tracking-wider text-zinc-500 mb-3">Selecione o Profissional</h3>
                
                @foreach($barbeirosRelatorio as $index => $rep)
                    @php $isGestor = in_array($rep['cargo'], ['proprietario', 'gestor']); @endphp
                    <div id="card-{{ $index }}"
                        class="barbeiro-card bg-zinc-900 border {{ $index === 0 ? 'border-[#D4AF37]' : 'border-zinc-800' }} rounded-2xl p-4 hover:border-zinc-700 transition cursor-pointer"
                        onclick="alternarColaborador('{{ $index }}')">
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-400 font-bold uppercase">
                                    {{ substr($rep['nome'], 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-zinc-100">{{ $rep['nome'] }}</h4>
                                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider {{ $isGestor ? 'bg-amber-950/50 text-[#D4AF37] border border-[#D4AF37]/20' : 'bg-zinc-800 text-zinc-400' }}">
                                        {{ $isGestor ? 'Gestor (100%)' : 'Colaborador (50%)' }}
                                    </span>
                                </div>
                            </div>
                            <i class="la la-angle-right text-zinc-600"></i>
                        </div>

                        <div class="mt-4 pt-3 border-t border-zinc-800/60 space-y-1">
                            @if($rep['total_adiantamentos'] > 0)
                                <div class="flex justify-between items-center text-[10px]">
                                    <span class="text-zinc-500 font-bold uppercase">Adiantado (-)</span>
                                    <span class="font-mono text-rose-400">R$ {{ number_format($rep['total_adiantamentos'], 2, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-zinc-400 uppercase font-bold">Saldo a Receber</span>
                                <span class="text-sm font-mono font-black {{ $rep['comissao_receber'] < 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                    R$ {{ number_format($rep['comissao_receber'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- COLUNA 2: PAINEL DINÂMICO DE COMPONENTES --}}
            <div class="lg:col-span-2 space-y-6">
                
                @foreach($barbeirosRelatorio as $index => $rep)
                    <div id="detalhes-{{ $index }}" class="detalhes-bloco {{ $index === 0 ? '' : 'hidden' }} space-y-4">
                        
                        {{-- CABEÇALHO DO PAINEL --}}
                        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <span class="text-[9px] uppercase font-black text-[#D4AF37] tracking-wider">Painel de Controle</span>
                                <h3 class="text-lg font-black text-zinc-100 uppercase">{{ $rep['nome'] }}</h3>
                            </div>
                            
                            {{-- Botões das Abas --}}
                            <div class="flex flex-wrap bg-zinc-950 p-1 rounded-xl border border-zinc-800/80 w-full sm:w-auto gap-1">
                                <button onclick="alternarAba('{{ $index }}', 'pagamento')" id="btn-aba-pagamento-{{ $index }}" class="w-full sm:w-auto text-xs font-bold px-3 py-2 rounded-lg bg-zinc-900 text-zinc-100 transition flex items-center justify-center gap-2 border border-zinc-800">
                                    <i class="la la-money-bill text-emerald-400"></i> Lançamento
                                </button>
                                <button onclick="alternarAba('{{ $index }}', 'extrato')" id="btn-aba-extrato-{{ $index }}" class="w-full sm:w-auto text-xs font-bold px-3 py-2 rounded-lg text-zinc-400 hover:text-zinc-200 transition flex items-center justify-center gap-2">
                                    <i class="la la-clock text-amber-400"></i> Pendentes
                                </button>
                                <button onclick="alternarAba('{{ $index }}', 'mensal')" id="btn-aba-mensal-{{ $index }}" class="w-full sm:w-auto text-xs font-bold px-3 py-2 rounded-lg text-zinc-400 hover:text-zinc-200 transition flex items-center justify-center gap-2">
                                    <i class="la la-history text-blue-400"></i> Mensal
                                </button>
                            </div>
                        </div>

                        {{-- SEÇÃO 1: ABA PAGAMENTO --}}
                        <div id="conteudo-pagamento-{{ $index }}" class="aba-conteudo-{{ $index }} grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-zinc-900 p-5 rounded-2xl border border-zinc-800">
                                <h4 class="text-xs font-bold uppercase text-zinc-300 mb-3 flex items-center gap-2">
                                    <i class="la la-lock text-amber-500 text-base"></i> Fechamento Financeiro
                                </h4>
                                {{-- Removido o 'onsubmit' daqui para controlar via JS de forma limpa --}}
                                <form action="{{ route('admin.pagamentos.store') }}" method="POST" class="form-fechamento space-y-3" data-nome="{{ $rep['nome'] }}">
                                    @csrf
                                    <input type="hidden" name="barbeiro_id" value="{{ $rep['id'] }}">
                                    <input type="hidden" name="data_inicio" value="{{ request('dia_especifico') ?? now()->startOfMonth()->format('Y-m-d') }}">
                                    <input type="hidden" name="data_fim" value="{{ request('dia_especifico') ?? now()->endOfMonth()->format('Y-m-d') }}">
                                    
                                    <div>
                                        <label class="text-[10px] uppercase text-zinc-500 font-bold block mb-1">Tipo de Operação</label>
                                        <select name="tipo_pagamento" id="tipo_pagamento_{{ $index }}" onchange="atualizarValorInput('{{ $index }}', '{{ $rep['comissao_receber'] }}')" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-300 focus:border-[#D4AF37] outline-none">
                                            <option value="repasse" selected>Fechamento Geral (Desconta adiantamentos)</option>
                                            <option value="adiantamento">Dar Adiantamento em Dinheiro</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-[10px] uppercase text-zinc-500 font-bold block mb-1">Período Base</label>
                                            <select name="tipo_periodo" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-300 outline-none">
                                                <option value="diario">Diário</option>
                                                <option value="semanal">Semanal</option>
                                                <option value="quinzenal">Quinzenal</option>
                                                <option value="mensal" selected>Mensal</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-[10px] uppercase text-zinc-500 font-bold block mb-1">Valor da Ação (R$)</label>
                                            {{-- Mudado para type="text" para permitir a formatação da máscara de Real --}}
                                            <input type="text" name="valor" id="valor_input_{{ $index }}" oninput="mascaraMoeda(this)" value="{{ number_format(max(0, $rep['comissao_receber']), 2, ',', '.') }}" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2.5 text-xs font-mono text-emerald-400 font-bold outline-none">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-[10px] uppercase text-zinc-500 font-bold block mb-1">Sua Senha de Administrador</label>
                                        <input type="password" name="password" required placeholder="••••••••" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-300 focus:border-amber-500 outline-none">
                                    </div>

                                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-2.5 px-4 rounded-lg transition uppercase tracking-wider cursor-pointer">
                                        Confirmar Operação Autenticada
                                    </button>
                                </form>
                            </div>

                            <div class="bg-zinc-900 p-5 rounded-2xl border border-zinc-800">
                                <h4 class="text-xs font-bold uppercase text-zinc-300 mb-3 flex items-center gap-2">
                                    <i class="la la-history text-zinc-500 text-base"></i> Últimos 5 Lançamentos
                                </h4>
                                <div class="overflow-y-auto max-h-[240px] space-y-2 pr-1">
                                    @forelse(\App\Models\Pagamento::where('barbeiro_id', $rep['id'])->latest()->take(5)->get() as $pago)
                                        <div class="flex justify-between items-center bg-zinc-950/60 p-2.5 rounded-lg border border-zinc-800/50">
                                            <div>
                                                <span class="text-[10px] font-mono text-zinc-500 block">{{ $pago->created_at->format('d/m/Y H:i') }}</span>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span class="text-[11px] font-bold text-zinc-300 uppercase tracking-wide">{{ $pago->tipo_periodo }}</span>
                                                    <span class="text-[8px] font-black px-1 rounded uppercase {{ ($pago->tipo_pagamento ?? 'repasse') === 'adiantamento' ? 'bg-rose-950 text-rose-400 border border-rose-900' : 'bg-emerald-950 text-emerald-400 border border-emerald-900' }}">
                                                        {{ $pago->tipo_pagamento ?? 'repasse' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="text-xs font-mono font-black {{ ($pago->tipo_pagamento ?? 'repasse') === 'adiantamento' ? 'text-rose-400' : 'text-emerald-400' }}">
                                                {{ ($pago->tipo_pagamento ?? 'repasse') === 'adiantamento' ? '-' : '+' }} R$ {{ number_format($pago->valor, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-center text-zinc-600 italic text-xs py-8">Nenhum histórico de repasse encontrado.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- SEÇÃO 2: ABA EXTRATO ANALÍTICO --}}
                        <div id="conteudo-extrato-{{ $index }}" class="aba-conteudo-{{ $index }} hidden bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-6">
                            @php
                                $servicosPendentes = collect($rep['detalhes_servicos'])->whereNull('pagamento_id');
                                $servicosConcluidos = collect($rep['detalhes_servicos'])->whereNotNull('pagamento_id');
                            @endphp

                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] text-zinc-500 uppercase font-bold block">Qtd. Pendentes</span>
                                    <span class="text-base font-black text-zinc-200 font-mono">{{ $servicosPendentes->count() }}</span>
                                </div>
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] text-emerald-500 uppercase font-bold block">Qtd. Já Pagos</span>
                                    <span class="text-base font-black text-emerald-400 font-mono">{{ $servicosConcluidos->count() }}</span>
                                </div>
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] text-zinc-500 uppercase font-bold block">Comissão Bruta</span>
                                    <span class="text-base font-black text-zinc-400 font-mono">R$ {{ number_format($rep['comissao_bruta'], 2, ',', '.') }}</span>
                                </div>
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] text-rose-400 uppercase font-bold block">Adiantamentos</span>
                                    <span class="text-base font-black text-rose-400 font-mono">R$ {{ number_format($rep['total_adiantamentos'], 2, ',', '.') }}</span>
                                </div>
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60 col-span-2 sm:col-span-1">
                                    <span class="text-[9px] uppercase font-bold block {{ $rep['comissao_receber'] < 0 ? 'text-rose-400' : 'text-emerald-400' }}">Saldo Líquido</span>
                                    <span class="text-base font-black font-mono {{ $rep['comissao_receber'] < 0 ? 'text-rose-400' : 'text-emerald-400' }}">R$ {{ number_format($rep['comissao_receber'], 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-2">
                                    <i class="la la-clock text-amber-500"></i> Trabalhos Aguardando Repasse
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs text-zinc-400">
                                        <thead>
                                            <tr class="text-zinc-500 border-b border-zinc-800 uppercase text-[9px] font-black">
                                                <th class="pb-2">Data / Hora</th>
                                                <th class="pb-2">Serviço Executado</th>
                                                <th class="pb-2 text-right">Preço Balcão</th>
                                                <th class="pb-2 text-right">Sua Comissão</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-800/40">
                                            @forelse($servicosPendentes as $servico)
                                            <tr class="hover:bg-zinc-950/20 transition">
                                                <td class="py-2.5 font-mono text-zinc-500">{{ \Carbon\Carbon::parse($servico->created_at)->format('d/m H:i') }}</td>
                                                <td class="py-2.5 font-bold text-zinc-200">{{ $servico->descricao }}</td>
                                                <td class="py-2.5 text-right font-mono text-zinc-400">R$ {{ number_format($servico->preco, 2, ',', '.') }}</td>
                                                <td class="py-2.5 text-right font-mono font-bold text-[#D4AF37]">R$ {{ number_format($servico->comissao_valor, 2, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="py-6 text-center text-zinc-600 italic">Nenhum serviço pendente.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- SEÇÃO 3: ABA EXTRATO MENSAL --}}
                        <div id="conteudo-mensal-{{ $index }}" class="aba-conteudo-{{ $index }} hidden bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-6">
                            @php
                                $servicosFaturados = collect($rep['detalhes_servicos'])->whereNotNull('pagamento_id');
                                $totalComissaoFaturada = collect($rep['detalhes_servicos'])->sum('comissao_valor');
                            @endphp

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] text-zinc-500 uppercase font-bold block">Serviços Pagos</span>
                                    <span class="text-base font-black text-zinc-200 font-mono">{{ $servicosFaturados->count() }}</span>
                                </div>
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] text-[#D4AF37] uppercase font-bold block">Comissão Concluída</span>
                                    <span class="text-base font-black text-[#D4AF37] font-mono">R$ {{ number_format($totalComissaoFaturada, 2, ',', '.') }}</span>
                                </div>
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] text-rose-400 uppercase font-bold block">Adiantamentos</span>
                                    <span class="text-base font-black text-rose-400 font-mono">R$ {{ number_format($rep['total_adiantamentos_mes'], 2, ',', '.') }}</span>
                                </div>
                                <div class="bg-zinc-950/40 p-3 rounded-xl border border-zinc-800/60">
                                    <span class="text-[9px] uppercase font-bold block {{ $rep['comissao_receber'] < 0 ? 'text-rose-400' : 'text-emerald-400' }}">Saldo Restante</span>
                                    <span class="text-base font-black font-mono {{ $rep['comissao_receber'] < 0 ? 'text-rose-400' : 'text-emerald-400' }}">R$ {{ number_format($rep['comissao_receber'], 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-2">
                                    <i class="la la-list text-blue-400"></i> Serviços Faturados e Pagos no Período
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs text-zinc-400">
                                        <thead>
                                            <tr class="text-zinc-500 border-b border-zinc-800 uppercase text-[9px] font-black">
                                                <th class="pb-2">Data / Hora</th>
                                                <th class="pb-2">Serviço Executado</th>
                                                <th class="pb-2 text-right">Preço Balcão</th>
                                                <th class="pb-2 text-right">Sua Comissão</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-800/40">
                                            @forelse($servicosFaturados as $servico)
                                            <tr class="hover:bg-zinc-950/20 transition">
                                                <td class="py-2.5 font-mono text-zinc-500">{{ \Carbon\Carbon::parse($servico->created_at)->format('d/m H:i') }}</td>
                                                <td class="py-2.5 font-bold text-zinc-200">
                                                    {{ $servico->descricao }}
                                                    <span class="ml-1 text-[8px] bg-blue-950 text-blue-400 border border-blue-900 px-1 rounded uppercase">Faturado (ID: {{ $servico->pagamento_id }})</span>
                                                </td>
                                                <td class="py-2.5 text-right font-mono text-zinc-500">R$ {{ number_format($servico->preco, 2, ',', '.') }}</td>
                                                <td class="py-2.5 text-right font-mono font-bold text-[#D4AF37]">R$ {{ number_format($servico->comissao_valor, 2, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="py-6 text-center text-zinc-600 italic">Nenhum serviço faturado encontrado.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <script>
        const barbeirosRelatorioData = @json($barbeirosRelatorio);

        // --- ATIVAÇÃO DO SWEETALERT2 PREMIUM ---
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.form-fechamento').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Impede o envio tradicional imediato
                    
                    const nomeBarbeiro = this.getAttribute('data-nome');
                    const tipoInput = this.querySelector('select[name="tipo_pagamento"]').value;
                    const acaoTexto = tipoInput === 'adiantamento' ? 'um ADIANTAMENTO' : 'um FECHAMENTO GERAL';

                    Swal.fire({
                        title: 'Confirmar Operação?',
                        text: `Você está prestes a registrar ${acaoTexto} para o profissional ${nomeBarbeiro}. Tem certeza?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981', // Emerald 500
                        cancelButtonColor: '#27272a',  // Zinc 800
                        confirmButtonText: 'Sim, Confirmar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Antes de enviar, removemos a formatação de moeda para enviar como float puro para o Laravel
                            const inputValor = this.querySelector('input[name="valor"]');
                            if(inputValor) {
                                inputValor.value = inputValor.value.replace(/\./g, '').replace(',', '.');
                            }
                            this.submit(); // Envia o formulário com sucesso
                        }
                    });
                });
            });
        });

        // --- MÁSCARA EM TEMPO REAL PARA REAL (R$) ---
        function mascaraMoeda(input) {
            let valor = input.value;
            
            // Remove tudo o que não for dígito
            valor = valor.replace(/\D/g, "");
            
            // Impede preenchimento com zeros extras à esquerda
            valor = (valor/100).toFixed(2) + '';
            
            // Aplica formatação brasileira
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            
            input.value = valor;
        }

        function alternarColaborador(index) {
            document.querySelectorAll('.detalhes-bloco').forEach(bloco => bloco.classList.add('hidden'));
            document.querySelectorAll('.barbeiro-card').forEach(card => {
                card.classList.remove('border-[#D4AF37]');
                card.classList.add('border-zinc-800');
            });

            document.getElementById('detalhes-' + index).classList.remove('hidden');
            document.getElementById('card-' + index).classList.remove('border-zinc-800');
            document.getElementById('card-' + index).classList.add('border-[#D4AF37]');
        }

        function alternarAba(index, abaAlvo) {
            document.querySelectorAll('.aba-conteudo-' + index).forEach(conteudo => conteudo.classList.add('hidden'));

            const abas = ['pagamento', 'extrato', 'mensal'];
            const classeInativo = "w-full sm:w-auto text-xs font-bold px-3 py-2 rounded-lg text-zinc-400 hover:text-zinc-200 transition flex items-center justify-center gap-2";
            const classeAtivo = "w-full sm:w-auto text-xs font-bold px-3 py-2 rounded-lg bg-zinc-900 text-zinc-100 transition flex items-center justify-center gap-2 border border-zinc-800";

            abas.forEach(aba => {
                const btn = document.getElementById(`btn-aba-${aba}-${index}`);
                if(btn) btn.className = classeInativo;
            });

            document.getElementById('conteudo-' + abaAlvo + '-' + index).classList.remove('hidden');
            const btnAtivo = document.getElementById(`btn-aba-${abaAlvo}-${index}`);
            if(btnAtivo) btnAtivo.className = classeAtivo;
        }

        function atualizarValorInput(index, comissaoReceber) {
            const select = document.getElementById(`tipo_pagamento_${index}`);
            const inputValor = document.getElementById(`valor_input_${index}`);
            
            if (select.value === 'adiantamento') {
                inputValor.value = '';
                inputValor.placeholder = '0,00';
            } else {
                // Ao voltar para repasse, formata novamente o valor dinâmico para moeda
                let valorFormatado = parseFloat(comissaoReceber);
                if (valorFormatado < 0) valorFormatado = 0; // Se estiver devendo, o repasse padrão inicia em 0
                
                inputValor.value = valorFormatado.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        function mascaraData(val) {
            var pass = val.value;
            for (i = 0; i < pass.length; i++) {
                var lchar = val.value.charAt(i);
                if (i == 0) {
                    if ((lchar != '0') && (lchar != '1') && (lchar != '2') && (lchar != '3')) val.value = "";
                } else if (i == 1) {
                    if (val.value.charAt(0) == '3') {
                        if ((lchar != '0') && (lchar != '1')) val.value = val.value.charAt(0);
                    }
                } else if (i == 2) {
                    if (lchar != '/') val.value = val.value.substring(0, 2) + '/';
                } else if (i == 3) {
                    if ((lchar != '0') && (lchar != '1')) val.value = val.value.substring(0, 3);
                } else if (i == 4) {
                    if (val.value.charAt(3) == '1') {
                        if ((lchar != '0') && (lchar != '1') && (lchar != '2')) val.value = val.value.substring(0, 4);
                    }
                } else if (i == 5) {
                    if (lchar != '/') val.value = val.value.substring(0, 5) + '/';
                }
            }
        }
    </script>
</body>
</html>