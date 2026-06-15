<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Plano - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .gold-text { color: #D4AF37; }
        .gold-border { border-color: #D4AF37; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen p-4 md:p-8">

    <div class="max-w-2xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center border-b border-zinc-900 pb-4">
            <div>
                <h1 class="text-xl font-black uppercase tracking-widest">Barber<span class="gold-text">Co.</span></h1>
                <p class="text-[10px] text-zinc-500 uppercase tracking-wider">Área do Assinante</p>
            </div>
            <a href="/" class="text-xs bg-zinc-900 border border-zinc-800 hover:border-zinc-700 px-4 py-2 rounded-lg transition">
                ← Voltar para Home
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 bg-zinc-900/40 p-4 rounded-xl border border-zinc-900 text-xs">
            <div>
                <span class="text-zinc-500 block font-bold uppercase tracking-wide text-[10px]">Plano</span>
                <span class="text-[#D4AF37] font-bold">{{ $assinatura->plano->nome }}</span>
            </div>
            <div>
                <span class="text-zinc-500 block font-bold uppercase tracking-wide text-[10px]">Forma de Pagamento</span>
                <span class="text-zinc-300">💳 {{ $assinatura->forma_pagamento }}</span>
            </div>
            <div class="col-span-2 md:col-span-1">
                <span class="text-zinc-500 block font-bold uppercase tracking-wide text-[10px]">Vigência do Contrato</span>
                <span class="text-zinc-300 font-mono">
                    {{ \Carbon\Carbon::parse($assinatura->data_inicio)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($assinatura->data_fim)->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <div class="bg-zinc-900/20 border border-zinc-900 rounded-xl p-6 md:p-8 space-y-6 text-zinc-400 text-xs leading-relaxed shadow-inner h-[450px] overflow-y-auto">
            
            <div class="text-center border-b border-zinc-900 pb-4">
                <h2 class="text-sm font-black text-zinc-200 uppercase tracking-wider">Termo de Adesão e Contrato de Prestação de Serviços</h2>
                <p class="text-[10px] text-zinc-500 mt-1">Contrato Eletrônico de Filiação ao Clube de Benefícios BarberCo.</p>
            </div>

            <p>
                Pelo presente instrumento, de um lado a **BarberCo.**, e de outro lado o contratante **{{ auth()->user()->name }}**, devidamente identificado em nosso banco de dados através do e-mail *{{ auth()->user()->email }}*, aceitam mutuamente as cláusulas descritas a seguir:
            </p>

            <div>
                <h3 class="text-zinc-200 font-bold uppercase text-[11px] mb-1">Cláusula 1ª - Do Objeto</h3>
                <p>
                    Este contrato concede ao CLIENTE o direito de usufruir dos serviços descritos no plano **{{ $assinatura->plano->nome }}** ({{ $assinatura->plano->descricao }}), respeitando os limites mensais estabelecidos na contratação:
                </p>
                <ul class="list-disc list-inside mt-2 space-y-1 text-zinc-300">
                    <li>Cortes de Cabelo: {{ $assinatura->plano->limite_cortes == 0 ? 'Ilimitados' : $assinatura->plano->limite_cortes . ' por mês' }}.</li>
                    <li>Serviços de Barba: {{ $assinatura->plano->limite_barba == 0 ? 'Ilimitados / Conforme regras do plano' : $assinatura->plano->limite_barba . ' por mês' }}.</li>
                </ul>
            </div>

            <div>
                <h3 class="text-zinc-200 font-bold uppercase text-[11px] mb-1">Cláusula 2ª - Da Utilização e Agendamentos</h3>
                <p>
                    A contratação do plano dá direito a agendamentos prioritários. Os horários devem ser reservados através da nossa plataforma online ou balcão, estando sujeitos à disponibilidade da agenda dos profissionais da casa. O não comparecimento sem aviso prévio de 2 horas (No-Show) poderá contabilizar como serviço utilizado.
                </p>
            </div>

            <div>
                <h3 class="text-zinc-200 font-bold uppercase text-[11px] mb-1">Cláusula 3ª - Da Vigência e Cancelamento</h3>
                <p>
                    O presente plano tem validade de 30 (trinta) dias consecutivos a contar da data de início em **{{ \Carbon\Carbon::parse($assinatura->data_inicio)->format('d/m/Y') }}**. Por se tratar de um modelo pré-pago, não haverá reembolso de valores caso o cliente decida interromper o uso antes do término do período vigente.
                </p>
            </div>

            <div class="border-t border-zinc-900 pt-4 text-center text-[10px] text-zinc-500">
                <p>Assinado eletronicamente por meio de autenticação de conta em {{ \Carbon\Carbon::parse($assinatura->data_inicio)->format('d/m/Y H:i') }}.</p>
                <p class="font-mono mt-1 text-zinc-600">ID de Autenticação: {{ $assinatura->gateway_id ?? 'BALCAO_' . $assinatura->id }}</p>
            </div>

        </div>

        <p class="text-center text-[10px] text-zinc-600">
            Dúvidas sobre o seu contrato? Entre em contato com o suporte da BarberCo.
        </p>

    </div>

</body>
</html>