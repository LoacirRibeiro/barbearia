<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Assinaturas - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    {{-- Cabeçalho --}}
    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.painel') }}" class="text-xs font-bold text-zinc-500 hover:text-amber-500 transition flex items-center gap-1 mb-1">
                    <i class="la la-arrow-left"></i> Voltar para o Painel
                </a>
                <h1 class="text-xl font-black uppercase tracking-widest">Controle de <span class="text-amber-500">Assinaturas</span></h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-xl font-bold border border-emerald-500/20">
                    {{ $planosAtivos->count() }} Ativos
                </span>
            </div>
        </div>
    </header>

    <!-- <div class="max-w-7xl mx-auto px-4 mt-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500 flex items-center gap-2">
            <i class="la la-wallet gold-text text-lg"></i> Faturamento e Auditoria de Fluxo
        </h2>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
            {{-- 📊 NOVO: Botão para abrir o Relatório Financeiro --}}
            <a href="{{ route('admin.planos.relatorio') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 text-zinc-300 font-bold px-4 py-2.5 rounded-xl border border-zinc-800 hover:border-zinc-700 transition flex items-center gap-2">
                <i class="la la-chart-bar text-base text-amber-500"></i> Ver Relatório
            </a>
        </div>
    </div> -->

    <main class="max-w-7xl mx-auto px-4 mt-8 space-y-12">

        {{-- Alertas de Feedback do Laravel --}}
        @if(session('sucesso'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="la la-check-circle text-lg"></i> {{ session('sucesso') }}
            </div>
        @endif

        @if(session('erro'))
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="la la-exclamation-circle text-lg"></i> {{ session('erro') }}
            </div>
        @endif

        {{-- 🟡 1. SEÇÃO: AGUARDANDO RECEBIMENTO NO BALCÃO --}}
        @if(isset($planosPendentes) && $planosPendentes->count() > 0)
        <section>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-amber-500 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                Aguardando Recebimento no Balcão (Ação Necessária)
            </h2>
            
            <div class="bg-zinc-900 rounded-2xl border border-amber-500/20 overflow-hidden bg-gradient-to-b from-amber-500/[0.02] to-transparent">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-400">
                        <thead>
                            <tr class="bg-zinc-950/60 border-b border-zinc-800 text-zinc-500 uppercase text-[9px] font-black tracking-wider">
                                <th class="p-4">Cliente</th>
                                <th class="p-4">Plano</th>
                                <th class="p-4">Forma de Pagamento</th>
                                <th class="p-4 text-center">Ações de Balcão</th>
                                <th class="p-4 text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @foreach($planosPendentes as $plano)
                            <tr class="hover:bg-zinc-950/40 transition cursor-pointer" onclick="abrirDetalhes('{{ $plano->assinatura_id }}')">
                                <td class="p-4">
                                    <div class="font-bold text-zinc-200 text-sm">{{ $plano->cliente_nome }}</div>
                                    <div class="text-[10px] text-zinc-500">{{ $plano->cliente_email }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="text-[10px] uppercase bg-amber-500/10 text-amber-400 font-bold px-2 py-0.5 rounded border border-amber-500/20">
                                        {{ $plano->nome_plano }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="text-[11px] text-zinc-300 font-medium">
                                        <i class="la la-money-bill text-amber-500"></i> {{ $plano->forma_pagamento }}
                                    </span>
                                </td>
                                
                                {{-- Coluna de Ações alinhada horizontalmente --}}
                                <td class="p-4 text-center" onclick="event.stopPropagation();">
                                    <div class="flex items-center justify-center gap-2">
                                        
                                        <form id="form-confirmar-{{ $plano->assinatura_id }}" method="POST" action="{{ route('admin.planos.confirmar', $plano->assinatura_id) }}" class="m-0">
                                            @csrf
                                            <input type="hidden" name="senha_admin" id="senha-confirmar-{{ $plano->assinatura_id }}">
                                            <button type="button" onclick="confirmarRecebimento('{{ $plano->assinatura_id }}', '{{ $plano->cliente_nome }}')" class="bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black px-4 py-2 rounded-xl transition text-xs uppercase tracking-wider cursor-pointer shadow-lg shadow-amber-500/10 whitespace-nowrap">
                                                Confirmar Recebimento
                                            </button>
                                        </form>
                                        
                                        <form id="form-cancelar-{{ $plano->assinatura_id }}" method="POST" action="{{ route('admin.planos.cancelar', $plano->assinatura_id) }}" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="senha_admin" id="senha-cancelar-{{ $plano->assinatura_id }}">
                                            <button type="button" onclick="solicitarSenhaCancelamento('{{ $plano->assinatura_id }}', '{{ $plano->cliente_nome }}')" class="bg-zinc-900 hover:bg-rose-950 border border-zinc-800 hover:border-rose-500/40 text-zinc-400 hover:text-rose-400 transition px-3 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 h-[36px]" title="Cancelar Pedido por Engano">
                                                <i class="la la-trash text-base"></i> Cancelar
                                            </button>
                                        </form>

                                    </div>
                                </td>
                                
                                <td class="p-4 text-right font-black text-zinc-200 text-sm">
                                    R$ {{ number_format($plano->preco_plano, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif

        {{-- 🟢 2. SEÇÃO: PLANOS ATIVOS --}}
        <section>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-emerald-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Assinaturas Ativas (Acesso Liberado)
            </h2>
            
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-400">
                        <thead>
                            <tr class="bg-zinc-950/40 border-b border-zinc-800 text-zinc-500 uppercase text-[9px] font-black tracking-wider">
                                <th class="p-4">Cliente</th>
                                <th class="p-4">Plano / Categoria</th>
                                <th class="p-4">Data de Adesão</th>
                                <th class="p-4">Próximo Vencimento</th>
                                <th class="p-4">Forma de Pagamento</th>
                                <th class="p-4 text-center">Status Pagamento</th>
                                <th class="p-4 text-center">Ações</th> 
                                <th class="p-4 text-right">Valor Mensal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse($planosAtivos as $plano)
                            {{-- Modificado para passar apenas o ID da assinatura --}}
                            <tr class="hover:bg-zinc-950/20 transition cursor-pointer" onclick="abrirDetalhes('{{ $plano->assinatura_id }}')">
                                <td class="p-4">
                                    <div class="font-bold text-zinc-200 text-sm">{{ $plano->cliente_nome }}</div>
                                    <div class="text-[10px] text-zinc-500">{{ $plano->cliente_email ?? 'Sem e-mail' }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="text-[10px] uppercase bg-emerald-500/10 text-emerald-400 font-bold px-2 py-0.5 rounded border border-emerald-500/20">
                                        {{ $plano->nome_plano }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono text-zinc-500">
                                    {{ \Carbon\Carbon::parse($plano->data_inicio)->format('d/m/Y') }}
                                </td>
                                <td class="p-4 font-mono font-bold text-zinc-300">
                                    {{ \Carbon\Carbon::parse($plano->data_fim)->format('d/m/Y') }}
                                </td>
                                <td class="p-4">
                                    <span class="text-[11px] text-zinc-400 bg-zinc-950 px-2.5 py-1 rounded-lg border border-zinc-800 font-medium">
                                        <i class="la la-money-bill text-xs text-zinc-500"></i> {{ $plano->forma_pagamento }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-[10px] uppercase font-bold text-emerald-400 bg-emerald-500/5 px-2.5 py-1 rounded-lg border border-emerald-500/10">
                                        <i class="la la-check-circle"></i> Pago
                                    </span>
                                </td>
                                <td class="p-4 text-center" onclick="event.stopPropagation();">
                                    <form id="form-cancelar-{{ $plano->assinatura_id }}" method="POST" action="{{ route('admin.planos.cancelar', $plano->assinatura_id) }}">
                                        @csrf
                                        <input type="hidden" name="senha_admin" id="senha-{{ $plano->assinatura_id }}">
                                        <button type="button" onclick="confirmarCancelamento('{{ $plano->assinatura_id }}')" class="bg-zinc-950 hover:bg-rose-600 hover:text-white text-zinc-400 font-bold px-2.5 py-1.5 rounded-lg transition text-[10px] uppercase tracking-wider cursor-pointer border border-zinc-800 hover:border-transparent">
                                            <i class="la la-trash"></i> Cancelar
                                        </button>
                                    </form>
                                </td>
                                <td class="p-4 text-right font-black text-zinc-200">
                                    R$ {{ number_format($plano->preco_plano, 2, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-zinc-600 italic">Nenhuma assinatura ativa encontrada no momento.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- 🔴 3. SEÇÃO: PLANOS VENCIDOS / INATIVOS --}}
        <section>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-rose-500 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                Histórico de Assinaturas Vencidas / Canceladas
            </h2>
            
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden opacity-75 hover:opacity-100 transition duration-300">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-400">
                        <thead>
                            <tr class="bg-zinc-950/40 border-b border-zinc-800 text-zinc-500 uppercase text-[9px] font-black tracking-wider">
                                <th class="p-4">Cliente</th>
                                <th class="p-4">Plano Anterior</th>
                                <th class="p-4">Período Ativo</th>
                                <th class="p-4">Inativado Em</th>
                                <th class="p-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            @forelse($planosVencidos as $plano)
                            {{-- Modificado para passar apenas o ID da assinatura --}}
                            <tr class="bg-zinc-950/10 cursor-pointer hover:bg-zinc-950/40 transition" onclick="abrirDetalhes('{{ $plano->assinatura_id }}')">
                                <td class="p-4">
                                    <div class="font-medium text-zinc-400">{{ $plano->cliente_nome }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="text-[10px] uppercase bg-zinc-800 text-zinc-400 font-medium px-2 py-0.5 rounded">
                                        {{ $plano->nome_plano }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono text-zinc-600 text-[11px]">
                                    {{ \Carbon\Carbon::parse($plano->data_inicio)->format('d/m/y') }} até {{ \Carbon\Carbon::parse($plano->data_fim)->format('d/m/y') }}
                                </td>
                                <td class="p-4 font-mono text-rose-500/70">
                                    {{ \Carbon\Carbon::parse($plano->data_fim)->format('d/m/Y') }}
                                </td>
                                <td class="p-4 text-right flex items-center justify-end gap-3" onclick="event.stopPropagation();">
                                    <span class="text-[9px] uppercase font-bold text-rose-400 bg-rose-500/5 px-2 py-0.5 rounded border border-rose-500/10">
                                        Expirado
                                    </span>
                                    <form id="form-reativar-{{ $plano->assinatura_id }}" method="POST" action="{{ route('admin.planos.reativar', $plano->assinatura_id) }}">
                                        @csrf
                                        <input type="hidden" name="senha_admin" id="senha-reativar-{{ $plano->assinatura_id }}">
                                        <button type="button" onclick="confirmarReativacao('{{ $plano->assinatura_id }}')" class="bg-zinc-800 hover:bg-amber-500 hover:text-zinc-950 text-zinc-400 font-bold px-2.5 py-1 rounded-lg transition text-[10px] uppercase tracking-wider cursor-pointer border border-zinc-700/60 hover:border-transparent">
                                            <i class="la la-redo-alt"></i> Reativar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-zinc-600 italic">Nenhum plano vencido no histórico.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    {{-- 📑 MODAL DE DETALHES DA ASSINATURA (DRAWER LATERAL DIREITO) --}}
    <div id="modal-detalhes" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-zinc-950/80 backdrop-blur-sm" onclick="fecharDetalhes()"></div>
        
        <div class="absolute left-1/2 top-0 bottom-0 -translate-x-1/2 w-full max-w-md bg-zinc-900 border-x border-zinc-800 shadow-2xl p-6 overflow-y-auto flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-zinc-800 pb-4 mb-6">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest text-amber-500 font-black">Ficha da Assinatura</span>
                        <h3 id="detalhe-plano-nome" class="text-lg font-black uppercase text-zinc-100">---</h3>
                    </div>
                    <button onclick="fecharDetalhes()" class="text-zinc-500 hover:text-zinc-200 transition text-xl cursor-pointer">
                        <i class="la la-times"></i>
                    </button>
                </div>

                <div class="space-y-4 mb-8">
                    <h4 class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Dados do Cliente</h4>
                    <div class="bg-zinc-950/50 border border-zinc-800/60 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-zinc-500">Nome:</span>
                            <span id="detalhe-cliente-nome" class="font-bold text-zinc-200">---</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-zinc-500">E-mail:</span>
                            <span id="detalhe-cliente-email" class="font-mono text-zinc-400">---</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-zinc-500">Telefone:</span>
                            <span id="detalhe-cliente-telefone" class="font-mono text-zinc-300">---</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <h4 class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Vigência e Contratação</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-zinc-950/50 border border-zinc-800/60 rounded-xl p-3">
                            <div class="text-[10px] text-zinc-500 mb-1">Adesão</div>
                            <div id="detalhe-data-inicio" class="text-xs font-mono font-bold text-zinc-300">---</div>
                        </div>
                        <div class="bg-zinc-950/50 border border-zinc-800/60 rounded-xl p-3">
                            <div class="text-[10px] text-zinc-500 mb-1">Próximo Vencimento</div>
                            <div id="detalhe-data-fim" class="text-xs font-mono font-bold text-emerald-400">---</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 mb-8">
                    <h4 class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Histórico de Pagamentos</h4>
                    <div class="bg-zinc-950/50 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-[11px]">
                            <thead class="bg-zinc-950 text-zinc-500 uppercase text-[9px] font-bold">
                                <tr>
                                    <th class="p-2.5">Referência</th>
                                    <th class="p-2.5">Forma</th>
                                    <th class="p-2.5 text-right">Status</th>
                                </tr>
                            </thead>
                            {{-- ID inserido aqui para manipulação via JS --}}
                            <tbody id="detalhe-historico-pagamentos" class="divide-y divide-zinc-800/40 text-zinc-400">
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-zinc-600 italic">Selecione um cliente...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Serviços Utilizados no Período</h4>
                    <div class="space-y-2" id="detalhe-historico-servicos">
                        <div class="text-zinc-600 text-xs italic p-2">Selecione um cliente...</div>
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-800 pt-4 mt-8 flex justify-end">
                <button onclick="fecharDetalhes()" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-bold text-xs uppercase tracking-wider px-4 py-2 rounded-xl transition cursor-pointer">
                    Fechar Janela
                </button>
            </div>
        </div>
    </div>


   <script>
    {{-- 🔄 Lógica AJAX (Fetch) para buscar dados dinâmicos do banco --}}
    function abrirDetalhes(assinaturaId) {
        // Coloca placeholders visuais de "Carregando"
        document.getElementById('detalhe-plano-nome').innerText = "Carregando...";
        document.getElementById('detalhe-cliente-nome').innerText = "Carregando...";
        document.getElementById('detalhe-cliente-email').innerText = "...";
        document.getElementById('detalhe-cliente-telefone').innerText = "...";
        document.getElementById('detalhe-data-inicio').innerText = "---";
        document.getElementById('detalhe-data-fim').innerText = "---";
        
        document.getElementById('detalhe-historico-pagamentos').innerHTML = `
            <tr>
                <td colspan="3" class="p-4 text-center text-zinc-500 italic">Buscando do banco...</td>
            </tr>
        `;
        document.getElementById('detalhe-historico-servicos').innerHTML = `
            <div class="text-zinc-500 text-xs italic p-2">Carregando histórico de uso...</div>
        `;
        
        // Abre a drawer lateral imediatamente
        document.getElementById('modal-detalhes').classList.remove('hidden');

        // Dispara a busca no banco de dados através da rota que criamos no Laravel
        fetch(`/admin/planos/${assinaturaId}/detalhes`)
            .then(response => {
                if (!response.ok) throw new Error('Falha ao obter dados.');
                return response.json();
            })
            .then(data => {
                // Preenche os dados cadastrais do cliente retornados do banco
                document.getElementById('detalhe-plano-nome').innerText = data.plano_nome;
                document.getElementById('detalhe-cliente-nome').innerText = data.cliente_nome;
                document.getElementById('detalhe-cliente-email').innerText = data.cliente_email || 'Não informado';
                document.getElementById('detalhe-cliente-telefone').innerText = data.cliente_telefone || 'Não informado';
                
                // Formata e renderiza datas
                if(data.data_inicio) {
                    let dtIn = new Date(data.data_inicio + 'T00:00:00'); // Evita fuso horário local
                    document.getElementById('detalhe-data-inicio').innerText = dtIn.toLocaleDateString('pt-BR');
                }
                if(data.data_fim) {
                    let dtFi = new Date(data.data_fim + 'T00:00:00');
                    document.getElementById('detalhe-data-fim').innerText = dtFi.toLocaleDateString('pt-BR');
                }

                // Renderiza o Histórico de Pagamentos dinamicamente
                let tbodyPags = document.getElementById('detalhe-historico-pagamentos');
                tbodyPags.innerHTML = '';

                if (!data.pagamentos || data.pagamentos.length === 0) {
                    tbodyPags.innerHTML = `<tr><td colspan="3" class="p-4 text-center text-zinc-600 italic">Nenhum pagamento registrado.</td></tr>`;
                } else {
                    data.pagamentos.forEach(pag => {
                        let statusCor = pag.status.toLowerCase() === 'pago' ? 'text-emerald-400' : 'text-amber-500';
                        tbodyPags.innerHTML += `
                            <tr class="hover:bg-zinc-950/20">
                                <td class="p-2.5 font-mono">${pag.referencia}</td>
                                <td class="p-2.5">${pag.forma}</td>
                                <td class="p-2.5 text-right font-bold ${statusCor}">${pag.status}</td>
                            </tr>
                        `;
                    });
                }

                // Renderiza o Histórico de Serviços Utilizados dinamicamente
                let divServicos = document.getElementById('detalhe-historico-servicos');
                divServicos.innerHTML = '';

                if (!data.servicos || data.servicos.length === 0) {
                    divServicos.innerHTML = `<div class="text-zinc-600 text-xs italic p-2">Nenhum serviço consumido neste período.</div>`;
                } else {
                    data.servicos.forEach(serv => {
                        divServicos.innerHTML += `
                            <div class="bg-zinc-950/30 border border-zinc-800/40 rounded-xl p-3 flex justify-between items-center">
                                <div>
                                    <div class="text-xs font-bold text-zinc-300">${serv.nome}</div>
                                    <div class="text-[10px] text-zinc-500 font-mono">${serv.data} - Com: ${serv.barbeiro}</div>
                                </div>
                                <span class="text-[9px] font-bold uppercase tracking-wider bg-zinc-800 px-2 py-0.5 rounded text-zinc-400">Plano</span>
                            </div>
                        `;
                    });
                }
            })
            .catch(error => {
                console.error(error);
                fecharDetalhes();
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Conexão',
                    text: 'Não conseguimos consultar o banco de dados para esta assinatura.',
                    background: '#18181b',
                    color: '#f4f4f5'
                });
            });
    }

    function fecharDetalhes() {
        document.getElementById('modal-detalhes').classList.add('hidden');
    }

    // 🔄 Função: Reativar Assinaturas Vencidas/Canceladas
    function confirmarReativacao(assinaturaId) {
        Swal.fire({
            title: 'Reativar esta assinatura?',
            text: "O plano será enviado de volta para a fila de recebimento no balcão.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'Sim, reativar',
            cancelButtonText: 'Voltar',
            background: '#18181b',
            color: '#f4f4f5'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Confirmação de Segurança',
                    text: 'Digite a senha do administrador para autorizar a reativação:',
                    input: 'password',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#27272a',
                    confirmButtonText: 'Confirmar Senha',
                    cancelButtonText: 'Cancelar',
                    background: '#18181b',
                    color: '#f4f4f5',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Você precisa digitar a senha!';
                        }
                    }
                }).then((senhaResult) => {
                    if (senhaResult.isConfirmed) {
                        document.getElementById('senha-reativar-' + assinaturaId).value = senhaResult.value;
                        document.getElementById('form-reativar-' + assinaturaId).submit();
                    }
                });
            }
        });
    }

    // 🟡 Função: Confirmar Recebimento (Balcão)
    function confirmarRecebimento(assinaturaId, clienteNome) {
        Swal.fire({
            title: 'Confirmar pagamento?',
            text: `Você confirma que recebeu o pagamento do(a) cliente ${clienteNome}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'Sim, desejo confirmar',
            cancelButtonText: 'Voltar',
            background: '#18181b',
            color: '#f4f4f5'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Confirmação de Segurança',
                    text: 'Digite a senha do administrador para autorizar o plano:',
                    input: 'password',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#27272a',
                    confirmButtonText: 'Confirmar Senha',
                    cancelButtonText: 'Cancelar',
                    background: '#18181b',
                    color: '#f4f4f5',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Você precisa digitar a senha!';
                        }
                    }
                }).then((senhaResult) => {
                    if (senhaResult.isConfirmed) {
                        document.getElementById('senha-confirmar-' + assinaturaId).value = senhaResult.value;
                        document.getElementById('form-confirmar-' + assinaturaId).submit();
                    }
                });
            }
        });
    }

    // 🛑 Função: Cancelar Assinaturas já ATIVAS
    function confirmarCancelamento(assinaturaId) {
        Swal.fire({
            title: 'Deseja cancelar?',
            text: "Esta ação não poderá ser desfeita facilmente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'Sim, desejo cancelar',
            cancelButtonText: 'Voltar',
            background: '#18181b',
            color: '#f4f4f5'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Confirmação de Segurança',
                    text: 'Digite a senha do administrador para prosseguir:',
                    input: 'password',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#27272a',
                    confirmButtonText: 'Confirmar Senha',
                    cancelButtonText: 'Cancelar',
                    background: '#18181b',
                    color: '#f4f4f5',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Você precisa digitar a senha!';
                        }
                    }
                }).then((senhaResult) => {
                    if (senhaResult.isConfirmed) {
                        document.getElementById('senha-' + assinaturaId).value = senhaResult.value;
                        document.getElementById('form-cancelar-' + assinaturaId).submit();
                    }
                });
            }
        });
    }

    // 🗑️ NOVA Função: Cancelar Pedidos PENDENTES do Balcão (Remoção segura com SweetAlert2)
    function solicitarSenhaCancelamento(assinaturaId, nomeCliente) {
        Swal.fire({
            title: 'Excluir Pedido?',
            text: `Tem certeza que deseja apagar e cancelar o pedido de: ${nomeCliente}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // Cor rose-600 do Tailwind
            cancelButtonColor: '#27272a',
            confirmButtonText: 'Sim, apagar pedido',
            cancelButtonText: 'Voltar',
            background: '#18181b',
            color: '#f4f4f5'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Validação de Segurança',
                    text: 'Digite a senha do administrador para deletar o registro:',
                    input: 'password',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#27272a',
                    confirmButtonText: 'Autorizar Exclusão',
                    cancelButtonText: 'Cancelar',
                    background: '#18181b',
                    color: '#f4f4f5',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Você precisa digitar a senha!';
                        }
                    }
                }).then((senhaResult) => {
                    if (senhaResult.isConfirmed) {
                        // Atribui a senha inserida ao campo correto do formulário pendente
                        document.getElementById('senha-cancelar-' + assinaturaId).value = senhaResult.value;
                        // Executa o envio
                        document.getElementById('form-cancelar-' + assinaturaId).submit();
                    }
                });
            }
        });
    }
</script>