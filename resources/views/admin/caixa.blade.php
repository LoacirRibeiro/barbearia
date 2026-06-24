<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa (Balcão) - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        .gold-border { border-color: #D4AF37; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #18181b; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 2px; }
        dialog::backdrop { background-color: rgba(9, 9, 11, 0.85); backdrop-filter: blur(4px); }
        
        .swal2-dark-popup {
            background: #18181b !important;
            border: 1px solid #27272a !important;
            border-radius: 1rem !important;
            color: #f4f4f5 !important;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black uppercase tracking-widest">Fluxo de <span class="gold-text">Caixa</span></h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Lançamento de Atendimento Presencial</p>
            </div>
            <a href="/" class="text-xs bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-lg hover:border-zinc-600 transition flex items-center gap-2">
                <i class="la la-arrow-left"></i> Home
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs bg-rose-950/40 border border-rose-900/60 text-rose-400 px-4 py-2 rounded-lg hover:border-rose-500 transition flex items-center gap-2 cursor-pointer">
                    <i class="la la-sign-out-alt"></i> Sair do Sistema
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 mt-8">

        {{-- MENSAGENS DE SUCESSO OU ERRO GLOBAIS --}}
        @if($errors->any() || $errors->has('caixa_erro'))
            <div class="js-alerta-temporario mb-6 bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl text-sm space-y-1 max-w-7xl mx-auto">
                @foreach ($errors->all() as $error)
                    <p class="font-medium">⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        @if(session('sucesso'))
            <div class="js-alerta-temporario mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm flex items-center gap-3 max-w-7xl mx-auto">
                <i class="la la-check-circle text-xl"></i>
                <span class="font-medium">{{ session('sucesso') }}</span>
            </div>
        @endif

        {{-- Nome do operador --}}
        <section class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 mb-6 flex flex-col md:flex-row items-center justify-center gap-4 max-w-7xl mx-auto shadow-xl">
            <div class="flex items-center gap-2.5 self-start sm:self-center">
                <div class="h-8 w-8 rounded-lg bg-zinc-950 border border-zinc-800 flex items-center justify-center text-amber-400">
                    <i class="la la-user-shield text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] uppercase font-black text-zinc-500 block tracking-wider">Operador do Turno</span>
                    <span class="text-xs font-bold text-zinc-200">{{ auth()->user()->nome ?? auth()->user()->name }}</span>
                </div>
            </div>
        </section>

        {{-- DASHBOARD DE GESTÃO DO CAIXA FÍSICO (GAVETA) --}}
        <section class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 mb-6 flex flex-col md:flex-row items-center justify-between gap-4 max-w-7xl mx-auto shadow-xl">
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                <div class="flex items-center gap-3 w-full sm:w-auto border-b sm:border-b-0 sm:border-r border-zinc-800 pb-3 sm:pb-0 pr-0 sm:pr-4">
                    <div class="p-3 rounded-xl {{ $caixaAberto ? 'bg-emerald-950/50 text-emerald-400 border border-emerald-900' : 'bg-rose-950/50 text-rose-400 border border-rose-900' }}">
                        <i class="la la-wallet text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider">Status do Caixa Físico</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="h-2 w-2 rounded-full {{ $caixaAberto ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                            <span class="text-[10px] uppercase font-black tracking-wider {{ $caixaAberto ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $caixaAberto ? 'Turno Ativo / Aberto' : 'Turno Encerrado / Fechado' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">

                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.painel') }}" class="text-xs bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-700 px-4 py-3 rounded-xl transition flex items-center gap-2 font-black uppercase tracking-wider text-amber-400 shadow-lg">
                        <i class="la la-chart-bar text-lg"></i> Gerenciamento 
                    </a>
                @endif

                @if(!$caixaAberto)
                    <button type="button" onclick="document.getElementById('modal_abrir_caixa').showModal()" class="flex-1 md:flex-initial bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black text-[10px] uppercase tracking-wider px-4 py-2.5 rounded-lg transition cursor-pointer">
                        <i class="la la-door-open"></i> Abrir Caixa
                    </button>
                @endif

                @if($caixaAberto)
                    <button type="button" onclick="abrirModalMovimentar('sangria')" class="flex-1 md:flex-initial bg-zinc-800 hover:bg-zinc-700 text-rose-400 border border-zinc-700/50 font-black text-[10px] uppercase tracking-wider px-4 py-2.5 rounded-lg transition cursor-pointer">
                        <i class="la la-cut"></i> Sangria
                    </button>
                    <button type="button" onclick="abrirModalMovimentar('suprimento')" class="flex-1 md:flex-initial bg-zinc-800 hover:bg-zinc-700 text-emerald-400 border border-zinc-700/50 font-black text-[10px] uppercase tracking-wider px-4 py-2.5 rounded-lg transition cursor-pointer">
                        <i class="la la-coins"></i> Suprimento
                    </button>
                    <button type="button" onclick="carregarEDispararFechamento()" class="flex-1 md:flex-initial bg-rose-950 hover:bg-rose-900 border border-rose-800 text-rose-300 font-black text-[10px] uppercase tracking-wider px-4 py-2.5 rounded-lg transition cursor-pointer">
                        <i class="la la-power-off"></i> Fechar Turno
                    </button>
                @endif
            </div>
        </section>

        {{-- FORMULÁRIO EM GRID (3 COLUNAS) --}}
        <form action="{{ route('admin.caixa.salvar') }}" method="POST" id="form-caixa" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @csrf

            <div class="lg:col-span-2 space-y-6">
                {{-- Seção 1: Dados do Atendimento --}}
                <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-xl space-y-4">
                    <div class="flex items-center gap-3 border-b border-zinc-800 pb-4">
                        <i class="la la-user-tag gold-text text-xl"></i>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider">Identificação do Atendimento</h2>
                            <p class="text-[10px] text-zinc-500">Vincule o barbeiro responsável e o cliente</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Nome do Cliente <span class="text-zinc-600">(Opcional)</span></label>
                            <input type="text" name="nome_cliente" id="nome_cliente" {{ !$caixaAberto ? 'disabled' : '' }} placeholder="Ex: João Silva (Vazio para 'Cliente Balcão')" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200 disabled:opacity-40">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Profissional / Barbeiro</label>
                            <select name="barbeiro_id" id="barbeiro_id" required {{ !$caixaAberto ? 'disabled' : '' }} class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200 appearance-none disabled:opacity-40">
                                <option value="">Quem realizou o atendimento?</option>
                                @foreach($barbeiros as $barbeiro)
                                    <option value="{{ $barbeiro->id }}">{{ $barbeiro->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                {{-- Seção 2: Adicionar Itens Componíveis --}}
                <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-xl space-y-6">
                    <div class="flex items-center gap-3 border-b border-zinc-800 pb-4">
                        <i class="la la-plus gold-text text-xl"></i>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider">Adicionar Serviços e Consumo</h2>
                            <p class="text-[10px] text-zinc-500">Combine múltiplos serviços ou produtos no mesmo caixa</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-zinc-950/40 p-4 rounded-xl border border-zinc-800/60 flex flex-col justify-between">
                            <div>
                                <label class="text-[10px] uppercase font-black gold-text block mb-2">Lançar Serviço</label>
                                <select id="select-servico" {{ !$caixaAberto ? 'disabled' : '' }} class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2.5 text-xs text-zinc-200 outline-none focus:border-amber-500 disabled:opacity-40">
                                    <option value="">Escolha um Serviço...</option>
                                    @foreach($servicos as $servico)
                                        <option value="{{ $servico->id }}" data-preco="{{ $servico->preco }}" data-nome="{{ $servico->nome }}">
                                            {{ $servico->nome }} — R$ {{ number_format($servico->preco, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" onclick="adicionarServico()" {{ !$caixaAberto ? 'disabled' : '' }} class="w-full mt-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold text-[11px] py-2 px-3 rounded-xl transition uppercase tracking-wider disabled:opacity-30">
                                + Incluir Serviço
                            </button>
                        </div>

                        <div class="bg-zinc-950/40 p-4 rounded-xl border border-zinc-800/60 flex flex-col justify-between">
                            <div>
                                <label class="text-[10px] uppercase font-black text-emerald-400 block mb-2">Lançar Produto / Consumo</label>
                                <select id="select-produto" {{ !$caixaAberto ? 'disabled' : '' }} class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2.5 text-xs text-zinc-200 outline-none focus:border-emerald-500 disabled:opacity-40">
                                    <option value="">Escolha um Produto (Pomada, Bebidas...)</option>
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" data-preco="{{ $produto->preco_venda }}" data-nome="{{ $produto->nome }}">
                                            {{ $produto->nome }} — R$ {{ number_format($produto->preco_venda, 2, ',', '.') }} (Estoque: {{ $produto->estoque }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" onclick="adicionarProduto()" {{ !$caixaAberto ? 'disabled' : '' }} class="w-full mt-4 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold text-[11px] py-2 px-3 rounded-xl transition uppercase tracking-wider disabled:opacity-30">
                                + Incluir Produto
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Coluna Direita: Resumo do Pedido --}}
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-xl flex flex-col justify-between min-h-[480px] h-fit sticky top-26">
                <div>
                    <div class="flex items-center gap-3 border-b border-zinc-800 pb-4 mb-4">
                        <i class="la la-shopping-basket gold-text text-xl"></i>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider">Resumo do Pedido</h2>
                            <p class="text-[10px] text-zinc-500">Itens inclusos no fechamento</p>
                        </div>
                    </div>

                    <div id="carrinho-vazio" class="text-center py-12 text-xs text-zinc-600 italic">
                        Nenhum item adicionado ao caixa ainda.
                    </div>

                    <ul id="lista-carrinho" class="divide-y divide-zinc-800/60 max-h-[220px] overflow-y-auto pr-1 hidden custom-scroll">
                    </ul>
                </div>

                <div class="pt-4 border-t border-zinc-800 space-y-4">
                    <div>
                        <label class="text-[10px] uppercase font-black text-zinc-500 block mb-2">Forma de Pagamento</label>
                        <select name="forma_pagamento" id="select-pagamento" {{ !$caixaAberto ? 'disabled' : '' }} required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-3 text-xs text-zinc-200 focus:border-amber-500 outline-none appearance-none disabled:opacity-40">
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Pix">Pix</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-end bg-zinc-950 p-4 rounded-xl border border-zinc-800">
                        <span class="text-[10px] uppercase font-black text-zinc-500">Total a Pagar</span>
                        <span id="txt-total" class="text-xl font-black gold-text">R$ 0,00</span>
                    </div>

                    <input type="hidden" name="itens_json" id="itens_json" value="[]">
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                    @if($caixaAberto)
                        <button type="submit" class="w-full gold-bg text-zinc-950 font-black text-xs py-4 rounded-xl hover:opacity-90 transition uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-amber-500/5 cursor-pointer">
                            <i class="la la-check-circle text-lg"></i> Finalizar Lançamento
                        </button>
                    @else
                        <div class="w-full bg-rose-950/30 border border-rose-900/60 p-3.5 rounded-xl text-center">
                            <p class="text-[10px] text-rose-400 font-black uppercase tracking-wider">
                                <i class="la la-lock text-sm"></i> Operação Bloqueada
                            </p>
                            <p class="text-[9px] text-zinc-500 mt-0.5">Abra o caixa físico antes de realizar lançamentos.</p>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </main>

    {{-- MODAL NATIVO 1: ABERTURA DE CAIXA --}}
    <dialog id="modal_abrir_caixa" class="fixed inset-0 m-auto bg-zinc-900 border border-zinc-800 rounded-2xl max-w-sm w-full p-6 text-zinc-100 shadow-2xl">
        <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4">
            <h3 class="text-xs font-black uppercase tracking-wider">Abertura de Turno</h3>
            <button onclick="document.getElementById('modal_abrir_caixa').close()" class="text-zinc-500 hover:text-zinc-200 cursor-pointer"><i class="la la-times text-lg"></i></button>
        </div>
        <form action="{{ route('admin.caixa.abrir') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Fundo de Troco Inicial (Dinheiro)</label>
                <input type="number" name="valor_abertura" step="0.01" min="0" required placeholder="Ex: 50.00" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 text-zinc-200">
            </div>
            <div>
                <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Sua Senha de Usuário</label>
                <input type="password" name="senha" required placeholder="Digite sua senha para confirmar" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 text-zinc-200">
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-zinc-950 font-black text-xs uppercase tracking-wider py-3.5 rounded-xl hover:opacity-90 transition cursor-pointer">
                Confirmar Abertura
            </button>
        </form>
    </dialog>

    {{-- MODAL NATIVO 2: SANGRIA / SUPRIMENTO --}}
    <dialog id="modal_movimentar_caixa" class="fixed inset-0 m-auto bg-zinc-900 border border-zinc-800 rounded-2xl max-w-sm w-full p-6 text-zinc-100 shadow-2xl">
        <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4">
            <h3 id="movimentar_titulo" class="text-xs font-black uppercase tracking-wider">Movimentação de Caixa</h3>
            <button onclick="document.getElementById('modal_movimentar_caixa').close()" class="text-zinc-500 hover:text-zinc-200 cursor-pointer"><i class="la la-times text-lg"></i></button>
        </div>
        
        <div id="wrapper_saldo_movimentar" class="bg-zinc-950 border border-zinc-800/80 rounded-xl p-3 mb-4 flex justify-between items-center text-xs">
            <span class="text-zinc-400 font-medium">Saldo Atual em Espécie:</span>
            <span id="movimentar_saldo_atual" class="font-black text-emerald-400">Carregando...</span>
        </div>

        <form action="{{ route('admin.caixa.movimentar') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="tipo" id="movimentar_tipo">
            
            <div>
                <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Valor da Operação (R$)</label>
                <input type="number" id="movimentar_valor_input" name="valor" step="0.01" min="0.01" required placeholder="0.00" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
            </div>
            <div>
                <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Motivação / Justificativa</label>
                <input type="text" name="motivo" required placeholder="Ex: Retirada para pó de café / Troco extra" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
            </div>
            <div>
                <label id="label_senha_movimentar" class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Sua Senha</label>
                <input type="password" name="senha" required placeholder="Digite a senha de liberação" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
            </div>
            <button type="submit" class="w-full gold-bg text-zinc-950 font-black text-xs uppercase tracking-wider py-3.5 rounded-xl hover:opacity-90 transition cursor-pointer">
                Salvar Movimentação
            </button>
        </form>
    </dialog>

    {{-- MODAL NATIVO 3: FECHAMENTO INTELIGENTE --}}
    <dialog id="modal_fechar_caixa" class="fixed inset-0 m-auto bg-zinc-900 border border-zinc-800 rounded-2xl max-w-sm w-full p-6 text-zinc-100 shadow-2xl">
        <div class="flex justify-between items-center border-b border-zinc-800 pb-3 mb-4">
            <h3 class="text-xs font-black uppercase tracking-wider text-rose-400">Conferência de Dinheiro Físico</h3>
            <button onclick="document.getElementById('modal_fechar_caixa').close()" class="text-zinc-500 hover:text-zinc-200 cursor-pointer"><i class="la la-times text-lg"></i></button>
        </div>
        
        <div class="space-y-2 text-xs border-b border-zinc-800/80 pb-4 mb-4">
            <div class="flex justify-between items-center text-zinc-400">
                <span>(+) Fundo de Abertura:</span>
                <span class="font-bold text-zinc-200" id="fechar_abertura">R$ 0,00</span>
            </div>
            <div class="flex justify-between items-center text-zinc-400">
                <span>(+) Entradas em Espécie:</span>
                <span class="font-bold text-emerald-400" id="fechar_vendas">+ R$ 0,00</span>
            </div>
            <div class="flex justify-between items-center text-zinc-400">
                <span>(+) Suprimentos (Reforços):</span>
                <span class="font-bold text-emerald-500" id="fechar_suprimentos">+ R$ 0,00</span>
            </div>
            <div class="flex justify-between items-center text-zinc-400">
                <span>(-) Retiradas (Sangrias):</span>
                <span class="font-bold text-rose-400" id="fechar_sangrias">- R$ 0,00</span>
            </div>
            <div class="bg-zinc-950 p-3 rounded-xl border border-zinc-800 text-center mt-2">
                <span class="text-[9px] uppercase font-black text-zinc-500 block tracking-wider">Total Esperado na Gaveta</span>
                <span class="text-xl font-black gold-text mt-0.5 block" id="fechar_esperado">R$ 0,00</span>
            </div>
        </div>

        <form action="{{ route('admin.caixa.fechar') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Quanto dinheiro tem na gaveta agora?</label>
                <input type="number" name="valor_fechamento_real" step="0.01" min="0" required placeholder="0.00" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 text-zinc-200">
            </div>
            <div>
                <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Sua Senha de Usuário</label>
                <input type="password" name="senha" required placeholder="Digite sua senha para fechar" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 text-zinc-200">
            </div>
            <button type="submit" class="w-full bg-rose-600 text-zinc-100 font-black text-xs uppercase tracking-wider py-3.5 rounded-xl hover:bg-rose-500 transition cursor-pointer">
                Encerrar Expediente
            </button>
        </form>
    </dialog>

    <script>
        let carrinho = [];
        let totalGeralVenda = 0;

        function carregarEDispararFechamento() {
            fetch("{{ route('admin.caixa.dados_fechamento') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert(data.erro);
                        return;
                    }

                    document.getElementById('fechar_abertura').innerText = 'R$ ' + data.valor_abertura.toFixed(2).replace('.', ',');
                    document.getElementById('fechar_vendas').innerText = '+ R$ ' + data.vendas_dinheiro.toFixed(2).replace('.', ',');
                    document.getElementById('fechar_suprimentos').innerText = '+ R$ ' + data.total_suprimentos.toFixed(2).replace('.', ',');
                    document.getElementById('fechar_sangrias').innerText = '- R$ ' + data.total_sangrias.toFixed(2).replace('.', ',');
                    document.getElementById('fechar_esperado').innerText = 'R$ ' + data.dinheiro_esperado.toFixed(2).replace('.', ',');

                    document.getElementById('modal_fechar_caixa').showModal();
                })
                .catch(error => {
                    console.error('Falha ao processar os dados de auditoria do turno:', error);
                });
        }

        function abrirModalMovimentar(tipo) {
            document.getElementById('movimentar_tipo').value = tipo;
            const campoSaldo = document.getElementById('movimentar_saldo_atual');
            const inputValor = document.getElementById('movimentar_valor_input');
            
            // Reseta estados anteriores
            campoSaldo.innerText = "Buscando...";
            campoSaldo.className = "font-black text-zinc-400";
            inputValor.removeAttribute('max'); 

            if(tipo === 'sangria') {
                document.getElementById('movimentar_titulo').innerText = 'Registrar Sangria (Retirada)';
                document.getElementById('label_senha_movimentar').innerHTML = 'Senha do <span class="text-rose-400">Administrador Master</span>';
            } else {
                document.getElementById('movimentar_titulo').innerText = 'Registrar Suprimento (Injeção)';
                document.getElementById('label_senha_movimentar').innerHTML = 'Sua Senha de Usuário';
            }
            
            // Abre o modal imediatamente para o UX não parecer travado
            document.getElementById('modal_movimentar_caixa').showModal();

            // Consulta os valores atualizados do caixa via Fetch
            fetch("{{ route('admin.caixa.dados_fechamento') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.erro) return;

                    // O dinheiro disponível real em caixa é o "dinheiro_esperado"
                    const saldoDisponivel = data.dinheiro_esperado;
                    
                    campoSaldo.innerText = 'R$ ' + saldoDisponivel.toFixed(2).replace('.', ',');
                    campoSaldo.className = "font-black text-emerald-400";

                    // Se for sangria, aplica validação nativa para não deixar retirar mais do que tem
                    if(tipo === 'sangria') {
                        inputValor.setAttribute('max', saldoDisponivel);
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar saldo atual:', error);
                    campoSaldo.innerText = "Indisponível";
                    campoSaldo.className = "font-black text-rose-400";
                });
        }

        function atualizarInterface() {
            const lista = document.getElementById('lista-carrinho');
            const vazio = document.getElementById('carrinho-vazio');
            const txtTotal = document.getElementById('txt-total');
            const inputJson = document.getElementById('itens_json');
            
            lista.innerHTML = '';
            totalGeralVenda = 0;

            if (carrinho.length === 0) {
                vazio.classList.remove('hidden');
                lista.classList.add('hidden');
            } else {
                vazio.classList.add('hidden');
                lista.classList.remove('hidden');

                carrinho.forEach((item, index) => {
                    totalGeralVenda += item.subtotal;
                    
                    lista.innerHTML += `
                        <li class="flex items-center justify-between py-3 text-xs">
                            <div class="pr-2">
                                <span class="font-bold text-zinc-200 block truncate max-w-[160px]">${item.nome}</span>
                                <span class="text-[10px] text-zinc-500 font-medium">${item.qtd}x de R$ ${item.preco.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span class="font-black text-zinc-300">R$ ${item.subtotal.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span>
                                <button type="button" onclick="removerItem(${index})" class="text-zinc-600 hover:text-rose-400 transition text-sm p-1 cursor-pointer"><i class="la la-trash"></i></button>
                            </div>
                        </li>
                    `;
                });
            }

            txtTotal.innerText = `R$ ${totalGeralVenda.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
            inputJson.value = JSON.stringify(carrinho);
        }

        function adicionarServico() {
            const select = document.getElementById('select-servico');
            const option = select.options[select.selectedIndex];
            if (!select.value) return;

            const itemExistente = carrinho.find(item => item.id === option.value && item.tipo === 'servico');

            if (itemExistente) {
                itemExistente.qtd += 1;
                itemExistente.subtotal = itemExistente.qtd * itemExistente.preco;
            } else {
                carrinho.push({
                    id: option.value,
                    tipo: 'servico',
                    nome: option.getAttribute('data-nome'),
                    preco: parseFloat(option.getAttribute('data-preco')),
                    qtd: 1,
                    subtotal: parseFloat(option.getAttribute('data-preco'))
                });
            }
            select.value = '';
            atualizarInterface();
        }

        function adicionarProduto() {
            const select = document.getElementById('select-produto');
            const option = select.options[select.selectedIndex];
            if (!select.value) return;

            const itemExistente = carrinho.find(item => item.id === option.value && item.tipo === 'produto');

            if (itemExistente) {
                itemExistente.qtd += 1;
                itemExistente.subtotal = itemExistente.qtd * itemExistente.preco;
            } else {
                carrinho.push({
                    id: option.value,
                    tipo: 'produto',
                    nome: option.getAttribute('data-nome'),
                    preco: parseFloat(option.getAttribute('data-preco')),
                    qtd: 1,
                    subtotal: parseFloat(option.getAttribute('data-preco'))
                });
            }
            select.value = '';
            atualizarInterface();
        }

        function removerItem(index) {
            carrinho.splice(index, 1);
            atualizarInterface();
        }

        document.getElementById('form-caixa').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (carrinho.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Carrinho Vazio',
                    text: 'Por favor, adicione pelo menos um serviço ou produto antes de finalizar.',
                    customClass: { popup: 'swal2-dark-popup' },
                    confirmButtonColor: '#D4AF37'
                });
                return;
            }

            const selectPagamento = document.getElementById('select-pagamento');
            const metodoEscolhido = selectPagamento.options[selectPagamento.selectedIndex].value;
            const valorFormatado = totalGeralVenda.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            
            const nomeOperador = "{{ auth()->user()->nome ?? auth()->user()->name }}";

            Swal.fire({
                title: 'Confirmar Lançamento?',
                html: `
                    <div class="text-left space-y-2 p-3 bg-zinc-950 rounded-xl border border-zinc-800 text-sm mt-3">
                        <p class="text-zinc-400">Total do Pedido: <strong class="text-amber-400 text-base">${valorFormatado}</strong></p>
                        <p class="text-zinc-400">Método Informado: <strong class="text-zinc-200">${metodoEscolhido}</strong></p>
                        <p class="text-zinc-400">Operador: <strong class="text-zinc-400 font-medium">${nomeOperador}</strong></p>
                    </div>
                    <p class="text-xs text-zinc-500 mt-4">Confirme se o dinheiro está na mão ou o comprovante do card/Pix foi validado antes de salvar.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sim, Registrar!',
                cancelButtonText: 'Voltar',
                customClass: {
                    popup: 'swal2-dark-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-caixa').submit();
                }
            });
        });

        (function() {
            function iniciarTimerAlerta() {
                const alertas = document.querySelectorAll('.js-alerta-temporario');
                alertas.forEach(function(alerta) {
                    setTimeout(function() {
                        alerta.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                        alerta.style.opacity = "0";
                        alerta.style.transform = "translateY(-10px)";
                        setTimeout(function() { alerta.remove(); }, 500);
                    }, 3500);
                });
            }
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", iniciarTimerAlerta);
            } else {
                iniciarTimerAlerta();
            }
        })();
    </script>
</body>
</html>