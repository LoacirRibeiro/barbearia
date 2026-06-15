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
                <div class="text-2xl font-black text-emerald-400 font-mono">
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
                    {{ $totalContratados }} <span class="text-xs font-normal text-zinc-500">assinaturas</span>
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
                    {{ $totalCancelados }} <span class="text-xs font-normal text-zinc-500">inativos</span>
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Histórico total de quebras de contrato ou expirações.</p>
            </div>

        </div>

        {{-- 🕒 TABELA DE LOGS E MOVIMENTAÇÕES --}}
        <section>
            <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-zinc-500"></span>
                Fluxo Recente de Contratações, Cancelamentos e Reativações
            </h2>
            
            <div class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-400">
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
                            <tr class="hover:bg-zinc-950/20 transition">
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
                            <tr>
                                <td colspan="6" class="p-8 text-center text-zinc-600 italic">Nenhuma movimentação registrada no sistema.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

</body>
</html>