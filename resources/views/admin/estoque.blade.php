<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Estoque - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        .gold-text { color: #D4AF37; }
        .gold-bg { background-color: #D4AF37; }
        
        /* Customização Dark elegante para o SweetAlert2 */
        .swal2-popup-dark {
            background: #18181b !important; /* zinc-900 */
            border: 1px solid #27272a !important; /* zinc-800 */
            border-radius: 1rem !important;
            color: #f4f4f5 !important; /* zinc-100 */
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 font-sans min-h-screen pb-12">

    <header class="border-b border-zinc-900 bg-zinc-900/50 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black uppercase tracking-widest">Controle de <span class="gold-text">Estoque</span></h1>
                <p class="text-[10px] text-zinc-500 font-bold uppercase">Gestão de Insumos e Produtos Físicos</p>
            </div>
            <a href="{{ route('admin.painel') }}" class="text-xs bg-zinc-900 border border-zinc-800 px-4 py-2 rounded-lg hover:border-zinc-600 transition">Voltar ao Painel</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 mt-8 space-y-6">
        
        @if(session('sucesso'))
            <div class="bg-emerald-950/50 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm font-semibold">
                🎉 {{ session('sucesso') }}
            </div>
        @endif
        
        @if($errors->has('senha_erro'))
            <div class="bg-red-950/50 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm font-semibold">
                {{ $errors->first('senha_erro') }}
            </div>
        @endif

        <section class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden">
            <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400">Produtos Cadastrados</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-400">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-500 uppercase text-[9px] font-black bg-zinc-950/30">
                            <th class="p-4">Produto</th>
                            <th class="p-4 text-right">Preço de Venda</th>
                            <th class="p-4 text-center">Qtd Atual</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @forelse($produtos as $prod)
                        <tr class="hover:bg-zinc-900/50 transition">
                            <td class="p-4 font-bold text-zinc-200 text-sm">{{ $prod->nome }}</td>
                            <td class="p-4 text-right font-semibold text-zinc-300">R$ {{ number_format($prod->preco_venda ?? $prod->preco, 2, ',', '.') }}</td>
                            <td class="p-4 text-center font-mono text-sm {{ $prod->estoque <= ($prod->estoque_minimo ?? 5) ? 'text-amber-500 font-bold' : 'text-zinc-100' }}">
                                {{ $prod->estoque }} un
                            </td>
                            <td class="p-4 text-center">
                                @if($prod->estoque <= 0)
                                    <span class="text-[9px] bg-red-950 text-red-400 px-2 py-0.5 rounded-md font-black uppercase border border-red-900/50">Esgotado</span>
                                @elseif($prod->estoque <= ($prod->estoque_minimo ?? 5))
                                    <span class="text-[9px] bg-amber-950 text-amber-400 px-2 py-0.5 rounded-md font-black uppercase border border-amber-900/50">Baixo</span>
                                @else
                                    <span class="text-[9px] bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded-md font-black uppercase border border-emerald-900/50">Disponível</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button onclick="abrirModalRepor('{{ $prod->id }}', '{{ $prod->nome }}', '/admin/estoque/repor/{{ $prod->id }}')" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition cursor-pointer">
                                    <i class="la la-plus"></i> Repor
                                </button>
                                <button onclick="abrirModalBaixa('{{ $prod->id }}', '{{ $prod->nome }}', '/admin/estoque/baixa/{{ $prod->id }}')" class="bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-900/50 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition cursor-pointer">
                                    <i class="la la-trash"></i> Dar Baixa
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center italic text-zinc-600">Nenhum produto cadastrado no banco de dados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-zinc-900 rounded-2xl border border-zinc-800 overflow-hidden mt-8">
            <div class="p-6 border-b border-zinc-800 flex items-center gap-2">
                <i class="la la-history text-amber-500 text-lg"></i>
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400">Linha do Tempo / Auditoria de Estoque</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-400">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-500 uppercase text-[9px] font-black bg-zinc-950/30">
                            <th class="p-4">Data / Hora</th>
                            <th class="p-4">Produto</th>
                            <th class="p-4 text-center">Tipo</th>
                            <th class="p-4 text-center">Qtd</th>
                            <th class="p-4">Motivo / Operador</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @forelse($historico as $mov)
                        <tr class="hover:bg-zinc-900/50 transition border-l-4 {{ $mov->tipo === 'entrada' ? 'border-l-emerald-500' : 'border-l-red-500' }}">
                            <td class="p-4 font-mono text-zinc-400">
                                {{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 font-bold text-zinc-200">
                                {{ $mov->produto->nome ?? 'Produto Removido' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($mov->tipo === 'entrada')
                                    <span class="text-[9px] bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded font-black uppercase border border-emerald-900/30">
                                        <i class="la la-arrow-up"></i> Entrada
                                    </span>
                                @else
                                    <span class="text-[9px] bg-red-950 text-red-400 px-2 py-0.5 rounded font-black uppercase border border-red-900/30">
                                        <i class="la la-arrow-down"></i> Saída
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center font-mono font-bold text-sm {{ $mov->tipo === 'entrada' ? 'text-emerald-400' : 'text-red-400' }}">
                                {{ $mov->tipo === 'entrada' ? '+' : '-' }}{{ $mov->quantidade }}
                            </td>
                            <td class="p-4 text-zinc-400">
                                <span class="text-zinc-200 font-medium">{{ $mov->motivo }}</span>
                                <div class="text-[10px] text-zinc-500 font-bold uppercase mt-0.5">
                                    <i class="la la-user"></i> Responsável: {{ $mov->usuario->name ?? 'Sistema' }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center italic text-zinc-600">Nenhuma movimentação registrada no estoque ainda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        // 1. FUNÇÃO PARA REPOR ESTOQUE
        function abrirModalRepor(id, nome, url) {
            Swal.fire({
                title: 'Entrada de Estoque',
                html: `
                    <div class="text-left space-y-4 p-1">
                        <p class="text-xs gold-text font-bold uppercase tracking-wide text-center mb-4">${nome}</p>
                        
                        <div>
                            <label class="text-[10px] uppercase font-black text-zinc-400 block mb-1">Quantidade a Adicionar</label>
                            <input type="number" id="swal_quantidade" min="1" placeholder="Ex: 10" 
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-black text-zinc-400 block mb-1">Sua Senha de Acesso</label>
                            <input type="password" id="swal_senha" placeholder="Digite sua senha para confirmar" 
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-500 text-zinc-200">
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="la la-shield-alt"></i> Confirmar Entrada',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-popup-dark',
                    confirmButton: 'bg-amber-500 hover:bg-amber-600 text-zinc-950 font-black px-5 py-3 rounded-xl text-xs uppercase tracking-wider cursor-pointer ml-2 w-full sm:w-auto',
                    cancelButton: 'bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider cursor-pointer w-full sm:w-auto'
                },
                buttonsStyling: false,
                focusConfirm: false,
                preConfirm: () => {
                    const quantidade = Swal.getPopup().querySelector('#swal_quantidade').value;
                    const senha = Swal.getPopup().querySelector('#swal_senha').value;
                    
                    if (!quantidade || quantidade < 1 || !senha) {
                        Swal.showValidationMessage('Por favor, preencha todos os campos corretamente.');
                        return false;
                    }
                    return { quantidade: quantidade, senha_admin: senha };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const dados = result.value;

                    Swal.fire({
                        title: 'Processando...',
                        text: 'Validando credenciais e atualizando estoque.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: { popup: 'swal2-popup-dark' }
                    });

                    const formData = new FormData();
                    formData.append('quantidade', dados.quantidade);
                    formData.append('senha_admin', dados.senha_admin);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) return Promise.reject(data);
                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.sucesso || 'Estoque atualizado com sucesso.',
                            icon: 'success',
                            customClass: { popup: 'swal2-popup-dark', confirmButton: 'bg-emerald-500 text-zinc-950 font-bold px-4 py-2 rounded-lg text-xs uppercase tracking-wider cursor-pointer' },
                            buttonsStyling: false
                        }).then(() => { window.location.reload(); });
                    })
                    .catch(error => {
                        console.error('Erro detalhado:', error);
                        let msgErro = 'Senha incorreta ou inválida.';
                        if (error && error.senha_erro) msgErro = error.senha_erro;
                        else if (error && error.errors) msgErro = error.errors[Object.keys(error.errors)[0]][0];

                        Swal.fire({
                            title: 'Erro na Operação',
                            text: msgErro,
                            icon: 'error',
                            customClass: { popup: 'swal2-popup-dark', confirmButton: 'bg-rose-500 text-zinc-100 font-bold px-4 py-2 rounded-lg text-xs uppercase tracking-wider cursor-pointer' },
                            buttonsStyling: false
                        });
                    });
                }
            });
        }

        // 2. FUNÇÃO PARA DAR BAIXA EM PRODUTO (QUEBRA/AVARIA)
        function abrirModalBaixa(id, nome, url) {
            Swal.fire({
                title: 'Dar Baixa em Produto (Avaria/Quebra)',
                html: `
                    <div class="text-left space-y-4 p-1">
                        <p class="text-xs text-red-400 font-bold uppercase tracking-wide text-center mb-4">${nome}</p>
                        
                        <div>
                            <label class="text-[10px] uppercase font-black text-zinc-400 block mb-1">Quantidade a Remover</label>
                            <input type="number" id="swal_quantidade_baixa" min="1" placeholder="Ex: 1" 
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 text-zinc-200">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-black text-zinc-400 block mb-1">Motivo da Baixa</label>
                            <input type="text" id="swal_motivo_baixa" placeholder="Ex: Quebrou ao cair da prateleira" 
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 text-zinc-200">
                        </div>

                        <div>
                            <label class="text-[10px] uppercase font-black text-zinc-400 block mb-1">Sua Senha de Acesso</label>
                            <input type="password" id="swal_senha_baixa" placeholder="Digite sua senha para confirmar" 
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 text-zinc-200">
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="la la-shield-alt"></i> Confirmar Baixa',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-popup-dark',
                    confirmButton: 'bg-rose-600 hover:bg-rose-700 text-zinc-100 font-black px-5 py-3 rounded-xl text-xs uppercase tracking-wider cursor-pointer ml-2 w-full sm:w-auto',
                    cancelButton: 'bg-zinc-800 hover:bg-zinc-700 text-zinc-200 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider cursor-pointer w-full sm:w-auto'
                },
                buttonsStyling: false,
                focusConfirm: false,
                preConfirm: () => {
                    const quantidade = Swal.getPopup().querySelector('#swal_quantidade_baixa').value;
                    const motivo = Swal.getPopup().querySelector('#swal_motivo_baixa').value;
                    const senha = Swal.getPopup().querySelector('#swal_senha_baixa').value;
                    
                    if (!quantidade || quantidade < 1 || !motivo || !senha) {
                        Swal.showValidationMessage('Por favor, preencha todos os campos corretamente.');
                        return false;
                    }
                    return { quantidade: quantidade, motivo: motivo, senha_admin: senha };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const dados = result.value;

                    Swal.fire({
                        title: 'Processando...',
                        text: 'Validando e aplicando baixa no estoque.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: { popup: 'swal2-popup-dark' }
                    });

                    const formData = new FormData();
                    formData.append('quantidade', dados.quantidade); 
                    formData.append('motivo', dados.motivo);
                    formData.append('senha_admin', dados.senha_admin);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) return Promise.reject(data);
                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Baixa Concluída!',
                            text: data.sucesso || 'O estoque foi reduzido.',
                            icon: 'success',
                            customClass: { popup: 'swal2-popup-dark', confirmButton: 'bg-emerald-500 text-zinc-950 font-bold px-4 py-2 rounded-lg text-xs uppercase tracking-wider cursor-pointer' },
                            buttonsStyling: false
                        }).then(() => { window.location.reload(); });
                    })
                    .catch(error => {
                        console.error('Erro detalhado:', error);
                        let msgErro = 'Ocorreu um erro inesperado.';
                        
                        if (error && error.senha_erro) {
                            msgErro = error.senha_erro;
                        } else if (error && error.errors) {
                            msgErro = error.errors[Object.keys(error.errors)[0]][0];
                        } else if (error && error.message) {
                            msgErro = error.message;
                        }

                        Swal.fire({
                            title: 'Erro na Operação',
                            text: msgErro,
                            icon: 'error',
                            customClass: { popup: 'swal2-popup-dark', confirmButton: 'bg-rose-500 text-zinc-100 font-bold px-4 py-2 rounded-lg text-xs uppercase tracking-wider cursor-pointer' },
                            buttonsStyling: false
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>