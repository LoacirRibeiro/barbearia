<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - BarberCo.</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-zinc-900/50 p-8 rounded-xl border border-zinc-800 shadow-2xl">
        <h2 class="text-2xl font-black uppercase text-center mb-6">Crie sua <span class="text-[#D4AF37]">Conta</span></h2>
        
        <form action="{{ route('cadastro.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Nome Completo</label>
                <input type="text" name="name" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">E-mail</label>
                <input type="email" name="email" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Telefone / WhatsApp</label>
                <input type="text" name="telefone" required placeholder="(11) 99999-9999" class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Senha</label>
                <input type="password" name="password" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Confirme a Senha</label>
                <input type="password" name="password_confirmation" required class="w-full bg-zinc-950 border border-zinc-800 rounded px-4 py-3 text-sm focus:outline-none focus:border-[#D4AF37]">
            </div>
            <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-3 rounded uppercase tracking-wider hover:bg-yellow-500 transition text-sm cursor-pointer">Criar Minha Conta</button>
        </form>
        <p class="text-xs text-zinc-500 text-center mt-6">Já tem conta? <a href="{{ route('login') }}" class="text-[#D4AF37] hover:underline font-bold">Fazer Login</a></p>
    </div>
</body>
</html>