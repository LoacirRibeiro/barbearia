<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evolução Comparativa Avançada - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        .tab-active { border-color: #D4AF37; color: #D4AF37; background-color: #18181b; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black uppercase tracking-widest">Análise de <span class="gold-text">Desempenho</span></h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Métricas Gerenciais, Históricos e Resultados do Balcão</p>
            </div>
            <a href="{{ route('admin.painel') }}" class="text-xs bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-lg hover:border-zinc-600 transition flex items-center gap-2">
                <i class="la la-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 mt-8 space-y-6">

        <div class="flex border-b border-zinc-800 gap-2">
            <button onclick="alternarVisaoPainel('mensal')" id="btn-tab-mensal" class="px-6 py-3 text-sm font-black uppercase tracking-wider border-b-2 border-transparent text-zinc-400 hover:text-zinc-200 transition cursor-pointer">
                <i class="la la-calendar"></i> Visão Diária (Deste Mês)
            </button>
            <button onclick="alternarVisaoPainel('anual')" id="btn-tab-anual" class="px-6 py-3 text-sm font-black uppercase tracking-wider border-b-2 border-transparent text-zinc-400 hover:text-zinc-200 transition cursor-pointer">
                <i class="la la-chart-bar"></i> Visão Mensal (Deste Ano)
            </button>
        </div>

        <div id="conteudo-painel-mensal" class="space-y-6 hidden">
            <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
                <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <input type="hidden" name="tipo_visao" value="mensal">
                    <div>
                        <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Escolha o Mês de Análise</label>
                        <input type="month" id="mes_ano_input" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
                        <input type="hidden" name="mes_ano_filtro" id="mes_ano_filtro" value="{{ request('mes_ano_filtro', date('m/Y')) }}">
                    </div>
                    <div>
                        <p class="text-[11px] text-zinc-500 pb-3">Toda a árvore de dados abaixo se alinhará detalhadamente **dia a dia** dentro do mês selecionado.</p>
                    </div>
                    <button type="submit" class="w-full gold-bg text-zinc-950 font-bold text-sm py-3.5 rounded-xl hover:opacity-90 transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="la la-filter"></i> Atualizar Painel Diário
                    </button>
                </form>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-users text-pink-500"></i> Atendimentos Diários Totais
                        </h3>
                        <p class="text-[11px] text-zinc-500">Volume absoluto de clientes atendidos na barbearia a cada dia.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartAtendimentosMensais"></canvas>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-wallet text-emerald-500"></i> Faturamento Diário com Serviços
                        </h3>
                        <p class="text-[11px] text-zinc-500">Valor arrecadado bruto com a prestação de serviços no respectivo dia.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartFaturamentoMensal"></canvas>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-box text-blue-500"></i> Quantidade de Produtos Vendidos
                        </h3>
                        <p class="text-[11px] text-zinc-500">Volume total de unidades de produtos comercializadas por dia.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartProdutosQuantidade"></canvas>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-tags text-cyan-500"></i> Faturamento Diário com Produtos
                        </h3>
                        <p class="text-[11px] text-zinc-500">Valor bruto arrecadado com balcão de varejo dia a dia.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartProdutosFaturamento"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div id="conteudo-painel-anual" class="space-y-6 hidden">
            <section class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
                <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <input type="hidden" name="tipo_visao" value="anual">
                    <div>
                        <label class="text-[10px] uppercase font-black text-zinc-400 block mb-2">Filtrar Ano de Referência</label>
                        <select name="ano_filtro" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
                            @php $anoAtual = date('Y'); $anoSel = request('ano_filtro', $anoAtual); @endphp
                            @for($i = $anoAtual; $i >= $anoAtual - 2; $i--)
                                <option value="{{ $i }}" {{ $anoSel == $i ? 'selected' : '' }}>Ano de {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <p class="text-[11px] text-zinc-500 pb-3">Toda a árvore de dados abaixo se consolidará acumulada **mês a mês** do ano vigente.</p>
                    </div>
                    <button type="submit" class="w-full gold-bg text-zinc-950 font-bold text-sm py-3.5 rounded-xl hover:opacity-90 transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="la la-filter"></i> Atualizar Painel Anual
                    </button>
                </form>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-users text-pink-500"></i> Atendimentos Mensais Totais
                        </h3>
                        <p class="text-[11px] text-zinc-500">Volume consolidado de clientes atendidos na barbearia em cada mês.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartAtendimentosAnuais"></canvas>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-wallet text-emerald-500"></i> Faturamento Mensal com Serviços
                        </h3>
                        <p class="text-[11px] text-zinc-500">Valor arrecadado consolidado com serviços no respectivo mês de referência.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartFaturamentoAnual"></canvas>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-box text-blue-500"></i> Produtos Vendidos no Ano
                        </h3>
                        <p class="text-[11px] text-zinc-500">Volume total acumulado de unidades de produtos vendidas a cada mês.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartProdutosQuantidadeAnual"></canvas>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                    <div>
                        <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="la la-tags text-cyan-500"></i> Faturamento Mensal com Produtos
                        </h3>
                        <p class="text-[11px] text-zinc-500">Receita bruta gerada pelas vendas da vitrine mensalmente.</p>
                    </div>
                    <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                        <canvas id="chartProdutosFaturamentoAnual"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                <div>
                    <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="la la-bar-chart text-[#D4AF37]"></i> Ranking de Atendimentos no Período
                    </h3>
                    <p class="text-[11px] text-zinc-500">Quantidade acumulada de clientes atendidos por cada profissional.</p>
                </div>
                <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                    <canvas id="chartGeralServicos"></canvas>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 space-y-3">
                <div>
                    <h3 class="text-sm font-black text-zinc-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="la la-line-chart text-pink-500"></i> Fluxo Cronológico por Profissional
                    </h3>
                    <p class="text-[11px] text-zinc-500">Histórico comparativo do ritmo de atendimentos entre profissionais.</p>
                </div>
                <div class="w-full bg-zinc-950/40 rounded-xl border border-zinc-800/50 p-2 h-[260px]">
                    <canvas id="chartGeralEvolucao"></canvas>
                </div>
            </div>
        </div>

    </main>

    <script>
        const barbeirosRelatorioData = @json($barbeirosRelatorioData);
        const produtosGerais = @json($produtosGerais ?? []);
        const nomesMeses = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"];
        const chartInstances = {};

        // Configurações de Estado da View (Filtros Ativos)
        const visaoInicial = "{{ request('tipo_visao', 'mensal') }}";
        const stringFiltro = "{{ request('mes_ano_filtro', date('m/Y')) }}";
        const [mesAtivo, anoAtivo] = stringFiltro.split('/').map(Number);
        const anoFiltro = "{{ request('ano_filtro', date('Y')) }}";

        const totalDiasNoMes = new Date(anoAtivo, mesAtivo, 0).getDate();
        const labelsDias = Array.from({ length: totalDiasNoMes }, (_, i) => (i + 1).toString());

        // 🎨 Configuração Base Reutilizável (Estilo Premium da Foto 1 - Força a exibição de todas as linhas e marcas)
        const opcoesGraficoBase = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { 
                    grid: { display: false }, 
                    ticks: { 
                        color: '#71717a', 
                        font: { size: 9, weight: 'bold' },
                        autoSkip: false, // 🔴 Corrige comportamento da Foto 2 (Força exibir 1, 2, 3...)
                        maxRotation: 0,
                        minRotation: 0
                    } 
                },
                y: { 
                    grid: { color: '#27272a' }, 
                    ticks: { color: '#71717a', font: { size: 10 } }, 
                    beginAtZero: true 
                }
            },
            plugins: { legend: { display: false } }
        };

        document.addEventListener("DOMContentLoaded", function() {
            // Sincronização dos Inputs de Formulários
            const inputMonth = document.getElementById('mes_ano_input');
            inputMonth.value = `${anoAtivo}-${mesAtivo.toString().padStart(2, '0')}`;
            inputMonth.addEventListener('change', function() {
                const [year, month] = this.value.split('-');
                document.getElementById('mes_ano_filtro').value = `${month}/${year}`;
            });

            // Ativa a Aba Inicial Solicitada
            alternarVisaoPainel(visaoInicial);

            // Executa as Renderizações Separadas
            renderizarGraficosDiarios();
            renderizarGraficosAnuais();
            renderizarGraficosGlobaisProfissionais();
        });

        // Lógica de Chaveamento Visual das Abas
        function alternarVisaoPainel(tipo) {
            document.getElementById('conteudo-painel-mensal').classList.add('hidden');
            document.getElementById('conteudo-painel-anual').classList.add('hidden');
            document.getElementById('btn-tab-mensal').classList.remove('tab-active');
            document.getElementById('btn-tab-anual').classList.remove('tab-active');

            if (tipo === 'mensal') {
                document.getElementById('conteudo-painel-mensal').classList.remove('hidden');
                document.getElementById('btn-tab-mensal').classList.add('tab-active');
            } else {
                document.getElementById('conteudo-painel-anual').classList.remove('hidden');
                document.getElementById('btn-tab-anual').classList.add('tab-active');
            }
        }

        // =========================================================
        // 📅 PROCESSAMENTO E PROCESSAMENTO DE DADOS DIÁRIOS
        // =========================================================
        function renderizarGraficosDiarios() {
            const atendimentosPorDia = Array(totalDiasNoMes).fill(0);
            const faturamentoPorDia = Array(totalDiasNoMes).fill(0);
            const qtdProdutosPorDia = Array(totalDiasNoMes).fill(0);
            const fatProdutosPorDia = Array(totalDiasNoMes).fill(0);

            barbeirosRelatorioData.forEach(b => {
                (b.detalhes_servicos || []).forEach(s => {
                    if (s.created_at) {
                        const d = new Date(s.created_at);
                        if (d.getMonth() + 1 === mesAtivo && d.getFullYear() === anoAtivo) {
                            atendimentosPorDia[d.getDate() - 1]++;
                            faturamentoPorDia[d.getDate() - 1] += Number(s.preco || 0);
                        }
                    }
                });
            });

            produtosGerais.forEach(v => {
                if (v.created_at) {
                    const d = new Date(v.created_at);
                    if (d.getMonth() + 1 === mesAtivo && d.getFullYear() === anoAtivo && v.itens) {
                        v.itens.forEach(i => {
                            if (i.tipo === 'produto') {
                                qtdProdutosPorDia[d.getDate() - 1] += Number(i.quantidade || 0);
                                fatProdutosPorDia[d.getDate() - 1] += Number(i.subtotal || 0);
                            }
                        });
                    }
                }
            });

            // Grafico 1: Atendimentos Diários
            new Chart(document.getElementById('chartAtendimentosMensais'), {
                type: 'bar',
                data: { labels: labelsDias, datasets: [{ data: atendimentosPorDia, backgroundColor: 'rgba(236, 72, 153, 0.2)', borderColor: '#ec4899', borderWidth: 2, borderRadius: 4 }] },
                options: opcoesGraficoBase
            });

            // Grafico 2: Faturamento Diário Serviços (Suave com Curvas da Foto 1)
            new Chart(document.getElementById('chartFaturamentoMensal'), {
                type: 'line',
                data: { labels: labelsDias, datasets: [{ data: faturamentoPorDia, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.06)', borderWidth: 3, tension: 0.3, fill: true, pointRadius: 3, pointBackgroundColor: '#10b981' }] },
                options: {
                    ...opcoesGraficoBase,
                    plugins: {
                        tooltip: { callbacks: { label: c => ' Receita: ' + c.parsed.y.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) } }
                    }
                }
            });

            // Grafico 3: Quantidade Vendas Produtos
            new Chart(document.getElementById('chartProdutosQuantidade'), {
                type: 'bar',
                data: { labels: labelsDias, datasets: [{ data: qtdProdutosPorDia, backgroundColor: 'rgba(59, 130, 246, 0.2)', borderColor: '#3b82f6', borderWidth: 2, borderRadius: 4 }] },
                options: opcoesGraficoBase
            });

            // Grafico 4: Faturamento Diário Produtos
            new Chart(document.getElementById('chartProdutosFaturamento'), {
                type: 'line',
                data: { labels: labelsDias, datasets: [{ data: fatProdutosPorDia, borderColor: '#06b6d4', backgroundColor: 'rgba(6, 182, 212, 0.06)', borderWidth: 3, tension: 0.3, fill: true, pointRadius: 3, pointBackgroundColor: '#06b6d4' }] },
                options: opcoesGraficoBase
            });
        }

        // =========================================================
        // 📊 PROCESSAMENTO E PROCESSAMENTO DE DADOS ANUAIS (MESES)
        // =========================================================
        function renderizarGraficosAnuais() {
            const atendimentosPorMes = Array(12).fill(0);
            const faturamentoPorMes = Array(12).fill(0);
            const qtdProdutosPorMes = Array(12).fill(0);
            const fatProdutosPorMes = Array(12).fill(0);

            barbeirosRelatorioData.forEach(b => {
                (b.detalhes_servicos || []).forEach(s => {
                    if (s.created_at) {
                        const d = new Date(s.created_at);
                        if (d.getFullYear().toString() === anoFiltro) {
                            atendimentosPorMes[d.getMonth()]++;
                            faturamentoPorMes[d.getMonth()] += Number(s.preco || 0);
                        }
                    }
                });
            });

            produtosGerais.forEach(v => {
                if (v.created_at) {
                    const d = new Date(v.created_at);
                    if (d.getFullYear().toString() === anoFiltro && v.itens) {
                        v.itens.forEach(i => {
                            if (i.tipo === 'produto') {
                                qtdProdutosPorMes[d.getMonth()] += Number(i.quantidade || 0);
                                fatProdutosPorMes[d.getMonth()] += Number(i.subtotal || 0);
                            }
                        });
                    }
                }
            });

            const opcoesAnuais = JSON.parse(JSON.stringify(opcoesGraficoBase));
            opcoesAnuais.scales.x.ticks.autoSkip = true; // Para 12 itens o ChartJS distribui sem quebrar

            new Chart(document.getElementById('chartAtendimentosAnuais'), {
                type: 'bar',
                data: { labels: nomesMeses, datasets: [{ data: atendimentosPorMes, backgroundColor: 'rgba(236, 72, 153, 0.2)', borderColor: '#ec4899', borderWidth: 2, borderRadius: 4 }] },
                options: opcoesAnuais
            });

            new Chart(document.getElementById('chartFaturamentoAnual'), {
                type: 'line',
                data: { labels: nomesMeses, datasets: [{ data: faturamentoPorMes, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.06)', borderWidth: 3, tension: 0.3, fill: true, pointRadius: 4, pointBackgroundColor: '#10b981' }] },
                options: opcoesAnuais
            });

            new Chart(document.getElementById('chartProdutosQuantidadeAnual'), {
                type: 'bar',
                data: { labels: nomesMeses, datasets: [{ data: qtdProdutosPorMes, backgroundColor: 'rgba(59, 130, 246, 0.2)', borderColor: '#3b82f6', borderWidth: 2, borderRadius: 4 }] },
                options: opcoesAnuais
            });

            new Chart(document.getElementById('chartProdutosFaturamentoAnual'), {
                type: 'line',
                data: { labels: nomesMeses, datasets: [{ data: fatProdutosPorMes, borderColor: '#06b6d4', backgroundColor: 'rgba(6, 182, 212, 0.06)', borderWidth: 3, tension: 0.3, fill: true, pointRadius: 4, pointBackgroundColor: '#06b6d4' }] },
                options: opcoesAnuais
            });
        }

        // =========================================================
        // 👥 RANKING ADAPTÁVEL DE COLABORADORES
        // =========================================================
        function renderizarGraficosGlobaisProfissionais() {
            const labelsBarbeiros = barbeirosRelatorioData.map(b => b.nome);
            const totalAtendimentosFiltrados = barbeirosRelatorioData.map(b => {
                return (b.detalhes_servicos || []).filter(s => {
                    if (!s.created_at) return false;
                    const d = new Date(s.created_at);
                    if (visaoInicial === 'mensal') {
                        return (d.getMonth() + 1 === mesAtivo && d.getFullYear() === anoAtivo);
                    } else {
                        return d.getFullYear().toString() === anoFiltro;
                    }
                }).length;
            });

            new Chart(document.getElementById('chartGeralServicos'), {
                type: 'bar',
                data: { labels: labelsBarbeiros, datasets: [{ data: totalAtendimentosFiltrados, backgroundColor: 'rgba(212, 175, 55, 0.2)', borderColor: '#D4AF37', borderWidth: 2, borderRadius: 5 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { x: { grid: { display: false }, ticks: { color: '#71717a' } }, y: { grid: { color: '#27272a' }, ticks: { color: '#71717a' }, beginAtZero: true } }, plugins: { legend: false } }
            });

            const paletaCores = ['#ec4899', '#3b82f6', '#10b981', '#f59e0b', '#6366f1'];
            const eixeXLabels = visaoInicial === 'mensal' ? labelsDias : nomesMeses;

            const datasetsEvolucao = barbeirosRelatorioData.map((b, idx) => {
                const dadosDistribuidos = Array(eixeXLabels.length).fill(0);
                (b.detalhes_servicos || []).forEach(s => {
                    if (s.created_at) {
                        const d = new Date(s.created_at);
                        if (visaoInicial === 'mensal') {
                            if (d.getMonth() + 1 === mesAtivo && d.getFullYear() === anoAtivo) dadosDistribuidos[d.getDate() - 1]++;
                        } else {
                            if (d.getFullYear().toString() === anoFiltro) dadosDistribuidos[d.getMonth()]++;
                        }
                    }
                });
                return { label: b.nome, data: dadosDistribuidos, borderColor: paletaCores[idx % paletaCores.length], tension: 0.3, fill: false, pointRadius: 3 };
            });

            const opcoesEvolucao = JSON.parse(JSON.stringify(opcoesGraficoBase));
            opcoesEvolucao.plugins.legend = { display: true, position: 'top', labels: { color: '#71717a', font: { size: 10 } } };
            if (visaoInicial === 'anual') opcoesEvolucao.scales.x.ticks.autoSkip = true;

            new Chart(document.getElementById('chartGeralEvolucao'), {
                type: 'line',
                data: { labels: eixeXLabels, datasets: datasetsEvolucao },
                options: opcoesEvolucao
            });
        }
    </script>
</body>
</html>