<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Assinatura - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen flex flex-col justify-between">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur">
        <div class="max-w-4xl mx-auto px-4 h-20 flex items-center justify-between">
            <a href="/" class="text-xl font-black tracking-widest uppercase">Barber<span class="gold-text">Co.</span></a>
            <a href="/" class="text-sm text-zinc-400 hover:text-zinc-100 transition">← Cancelar</a>
        </div>
    </header>

    <main class="max-w-md w-full mx-auto px-4 py-12 flex-grow flex flex-col justify-center">
        <div class="bg-zinc-900/50 p-8 rounded-xl border border-zinc-800 shadow-2xl">
            <h2 class="text-2xl font-black uppercase tracking-wider text-center mb-2">Confirme seu <span class="gold-text">Plano</span></h2>
            <p class="text-zinc-400 text-sm text-center mb-6">Escolha a forma de pagamento para ativar sua assinatura.</p>

            <div class="mb-6 p-4 bg-zinc-950 border border-zinc-900 rounded-xl space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#D4AF37] block">Plano Selecionado</span>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-200 font-bold text-base">💳 {{ $plano->nome }}</span>
                    <span class="text-[#D4AF37] font-mono font-black text-xl">R$ {{ number_format($plano->preco, 2, ',', '.') }}</span>
                </div>
            </div>

            <form action="{{ route('planos.assinar') }}" method="POST" id="form-assinatura" class="space-y-6">
                @csrf
                <input type="hidden" name="plano_id" value="{{ $plano->id }}">

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-zinc-400">Forma de Pagamento</label>
                    <select name="forma_pagamento" id="forma_pagamento" class="w-full bg-zinc-950 border border-zinc-800 rounded-lg p-3 text-sm text-zinc-200 focus:outline-none focus:border-[#D4AF37]">
                        <option value="Dinheiro/Balcão">Pagar no Balcão (Dinheiro/Pix)</option>
                        <option value="Cartão de Crédito">Cartão de Crédito (Online)</option>
                    </select>
                </div>

                <div id="area-cartao" class="hidden space-y-4 p-4 bg-zinc-950 rounded-xl border border-zinc-900 transition-all duration-300">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#D4AF37] block mb-2">Dados do Cartão</span>
                    
                    <div>
                        <label class="text-[11px] text-zinc-400 block mb-1">Número do Cartão</label>
                        <input type="text" id="card_number" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-200 focus:outline-none focus:border-[#D4AF37]" placeholder="0000 0000 0000 0000" maxlength="19">
                    </div>

                    <div>
                        <label class="text-[11px] text-zinc-400 block mb-1">Nome impresso no Cartão</label>
                        <input type="text" id="card_name" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-200 uppercase focus:outline-none focus:border-[#D4AF37]" placeholder="JOSÉ SILVA">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] text-zinc-400 block mb-1">Validade</label>
                            <input type="text" id="card_expiry" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-200 focus:outline-none focus:border-[#D4AF37]" placeholder="MM/AA" maxlength="5">
                        </div>
                        <div>
                            <label class="text-[11px] text-zinc-400 block mb-1">CVV</label>
                            <input type="text" id="card_cvv" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-200 focus:outline-none focus:border-[#D4AF37]" placeholder="123" maxlength="4">
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-zinc-950/40 rounded border border-zinc-900 text-center">
                    <p class="text-xs text-zinc-400">Assinando como: <span class="text-[#D4AF37] font-bold">{{ auth()->user()->name }}</span></p>
                </div>

                <button type="submit" class="w-full bg-[#D4AF37] text-black font-black py-4 rounded uppercase tracking-wider hover:bg-yellow-500 transition text-sm cursor-pointer shadow-lg">
                    Confirmar e Ativar Plano
                </button>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const seletorPagamento = document.getElementById('forma_pagamento');
            const areaCartao = document.getElementById('area-cartao');
            const form = document.getElementById('form-assinatura');

            // Inputs do cartão
            const cardNumber = document.getElementById('card_number');
            const cardName = document.getElementById('card_name');
            const cardExpiry = document.getElementById('card_expiry');
            const cardCvv = document.getElementById('card_cvv');

            // Mostra/Oculta campos do cartão de forma suave
            seletorPagamento.addEventListener('change', function () {
                if (this.value === 'Cartão de Crédito') {
                    areaCartao.classList.remove('hidden');
                } else {
                    areaCartao.classList.add('hidden');
                }
            });

            // Máscara simples para Validade (MM/AA)
            cardExpiry.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
            });

            // Verificação básica antes do envio (Frontend)
            form.addEventListener('submit', function (e) {
                if (seletorPagamento.value === 'Cartão de Crédito') {
                    if (!cardNumber.value || !cardName.value || !cardExpiry.value || !cardCvv.value) {
                        e.preventDefault();
                        alert('Por favor, preencha todos os dados do cartão de crédito.');
                    }
                }
            });
        });
    </script>
</body>
</html>