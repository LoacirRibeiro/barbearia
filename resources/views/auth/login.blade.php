<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-zinc-900/50 p-8 rounded-xl border border-zinc-800 shadow-2xl">
        <h2 class="text-2xl font-black uppercase text-center mb-6">Acesse sua <span class="text-[#D4AF37]">Conta</span></h2>
        
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">E-mail</label>
                <input type="email" name="email" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <div>
                <div class="flex justify-between mb-2">
                    <label class="text-xs font-bold uppercase text-zinc-400">Senha</label>
                    <a href="{{ route('senha.recuperar') }}" class="text-xs text-[#D4AF37] hover:underline">Esqueceu?</a>
                </div>
                <input type="password" name="password" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-3 rounded uppercase tracking-wider hover:bg-yellow-500 transition text-sm cursor-pointer">Entrar</button>
        </form>
        <p class="text-xs text-zinc-500 text-center mt-6">Não tem conta? <a href="{{ route('cadastro') }}" class="text-[#D4AF37] hover:underline font-bold">Cadastre-se</a></p>
    </div>
</body>
</html>