<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'Cadastro - TextPro'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full p-8 space-y-6 bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Cadastro TextPro</h2>
            <p class="text-gray-500 mt-2">Crie sua conta para começar</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200 animate-pulse">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-4" action="<?= BASE_URL ?>/register" method="POST">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                <span class="text-sm font-semibold text-blue-800">Você reside no Brasil?</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="reside_brasil" id="reside_brasil" value="1" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1 ml-1">Nome Completo</label>
                <input id="nome" name="nome" type="text" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="Ex: João Silva">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1 ml-1">E-mail</label>
                <input id="email" name="email" type="email" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="seu@email.com">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="container_cpf">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1 ml-1">CPF ou CNPJ</label>
                    <input id="cpf_cnpj" name="cpf_cnpj" type="text" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="000.000.000-00">
                </div>
                <div id="container_tel">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1 ml-1">WhatsApp</label>
                    <input id="telefone" name="telefone" type="text" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="(00) 00000-0000">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1 ml-1">Senha</label>
                    <input id="senha" name="senha" type="password" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="******">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1 ml-1">Confirmar Senha</label>
                    <input id="confirmar_senha" name="confirmar_senha" type="password" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="******">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-all transform hover:scale-[1.02] shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-rocket"></i> Finalizar Cadastro
            </button>
        </form>

        <div class="text-center text-sm text-gray-500 border-t pt-4">
            Já possui uma conta? <a href="<?= BASE_URL ?>/login" class="font-bold text-blue-600 hover:underline">Entrar agora</a>
        </div>
    </div>
</div>

<script>
    const toggle = document.getElementById('reside_brasil');
    const containerCpf = document.getElementById('container_cpf');
    const inputCpf = document.getElementById('cpf_cnpj');
    const containerTel = document.getElementById('container_tel');

    toggle.addEventListener('change', function() {
        if (this.checked) {
            containerCpf.classList.remove('hidden');
            inputCpf.required = true;
            containerTel.classList.remove('col-span-2');
        } else {
            containerCpf.classList.add('hidden');
            inputCpf.required = false;
            inputCpf.value = '';
            containerTel.classList.add('col-span-2');
        }
    });
</script>
</body>
</html>