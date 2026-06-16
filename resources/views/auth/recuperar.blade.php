<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Estilização Dark para o SweetAlert combinar com o design da barbearia */
        .swal2-popup-dark {
            background: #09090b !important; /* zinc-950 */
            border: 1px solid #18181b !important; /* zinc-900 */
            color: #f4f4f5 !important; /* zinc-100 */
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-zinc-900/50 p-8 rounded-xl border border-zinc-800 shadow-2xl">
        <h2 class="text-2xl font-black uppercase text-center mb-2">Recuperar <span class="text-[#D4AF37]">Senha</span></h2>
        <p class="text-xs text-zinc-400 text-center mb-6">Insira seu e-mail cadastrado para receber as instruções de recuperação.</p>
        
        <form action="{{ route('senha.recuperar.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">E-mail Cadastrado</label>
                <input type="email" name="email" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-3 rounded uppercase tracking-wider hover:bg-yellow-500 transition text-sm cursor-pointer">Enviar Link</button>
        </form>
        <p class="text-xs text-zinc-500 text-center mt-6"><a href="{{ route('login') }}" class="text-[#D4AF37] hover:underline font-bold">← Voltar para o Login</a></p>
    </div>

    @if(session('erro'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Ops...',
                text: "{{ session('erro') }}",
                showConfirmButton: true,
                confirmButtonColor: '#D4AF37',
                customClass: {
                    popup: 'swal2-popup-dark'
                }
            });
        </script>
    @endif

    @if(session('sucesso'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Link Enviado!',
                text: "{{ session('sucesso') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-popup-dark'
                },
                iconColor: '#10b981'
            });
        </script>
    @endif
</body>
</html>