<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa (Balcão) - BarberCo.</title>
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
                <h1 class="text-xl font-black uppercase tracking-widest">Fluxo de <span class="gold-text">Caixa</span></h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Lançamento de Atendimento Presencial</p>
            </div>
            <a href="{{ route('admin.painel') }}" class="text-xs bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-lg hover:border-zinc-600 transition flex items-center gap-2">
                {{-- Nota: Caso sua rota do painel tenha outro nome, ajuste aqui (ex: admin.painel) --}}
                <i class="la la-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </header>

    <main class="max-w-xl mx-auto px-4 mt-8">
        
        @if(session('sucesso'))
            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm flex items-center gap-3">
                <i class="la la-check-circle text-xl"></i>
                <span class="font-medium">{{ session('sucesso') }}</span>
            </div>
        @endif

        <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800 shadow-xl">
            <div class="flex items-center gap-3 border-b border-zinc-800 pb-4 mb-6">
                <i class="la la-cash-register gold-text text-2xl"></i>
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider">Registrar Venda Direta</h2>
                    <p class="text-[10px] text-zinc-500">O serviço será computado e finalizado imediatamente</p>
                </div>
            </div>

            <form action="{{ route('admin.caixa.salvar') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="nome_cliente" class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Nome do Cliente <span class="text-zinc-600">(Opcional)</span></label>
                    <input type="text" 
                           name="nome_cliente" 
                           id="nome_cliente" 
                           placeholder="Ex: João Silva (Vazio para 'Cliente Balcão')"
                           class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
                    @error('nome_cliente') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="barbeiro_id" class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Profissional / Barbeiro</label>
                        <select name="barbeiro_id" id="barbeiro_id" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200 appearance-none">
                            <option value="">Quem atendeu?</option>
                            @foreach($barbeiros as $barbeiro)
                                <option value="{{ $barbeiro->id }}">{{ $barbeiro->nome }}</option>
                            @endforeach
                        </select>
                        @error('barbeiro_id') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="servico_id" class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Serviço Realizado</label>
                        <select name="servico_id" id="servico_id" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200 appearance-none">
                            <option value="">Selecione o serviço...</option>
                            @foreach($servicos as $servico)
                                <option value="{{ $servico->id }}">{{ $servico->nome }} — R$ {{ number_format($servico->preco, 2, ',', '.') }}</option>
                            @endforeach
                        </select>
                        @error('servico_id') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-[10px] uppercase font-black text-zinc-400 block mb-3">Forma de Pagamento</label>
                    <div class="grid grid-cols-2 gap-2">
                        
                        <label class="border border-zinc-800 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-zinc-700 transition input-pay">
                            <input type="radio" name="forma_pagamento" value="Dinheiro" checked class="accent-amber-500">
                            <span class="text-xs font-bold text-zinc-300 flex items-center gap-1.5"><i class="la la-money-bill text-base text-zinc-500"></i> Dinheiro</span>
                        </label>

                        <label class="border border-zinc-800 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-zinc-700 transition input-pay">
                            <input type="radio" name="forma_pagamento" value="Pix" class="accent-amber-500">
                            <span class="text-xs font-bold text-zinc-300 flex items-center gap-1.5"><i class="la la-qrcode text-base text-zinc-500"></i> Pix</span>
                        </label>

                        <label class="border border-zinc-800 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-zinc-700 transition input-pay">
                            <input type="radio" name="forma_pagamento" value="Cartão de Débito" class="accent-amber-500">
                            <span class="text-xs font-bold text-zinc-300 flex items-center gap-1.5"><i class="la la-credit-card text-base text-zinc-500"></i> Débito</span>
                        </label>

                        <label class="border border-zinc-800 rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:border-zinc-700 transition input-pay">
                            <input type="radio" name="forma_pagamento" value="Cartão de Crédito" class="accent-amber-500">
                            <span class="text-xs font-bold text-zinc-300 flex items-center gap-1.5"><i class="la la-credit-card text-base text-zinc-500"></i> Crédito</span>
                        </label>

                    </div>
                    @error('forma_pagamento') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full gold-bg text-zinc-950 font-black text-xs py-4 rounded-xl hover:opacity-90 transition uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-amber-500/5">
                        <i class="la la-check-circle text-lg"></i> Confirmar e Lançar Venda
                    </button>
                </div>
            </form>
        </section>
    </main>

</body>
</html>