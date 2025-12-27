<html lang="pt-br"><head><meta charset="UTF-8"><title><?php echo $title ?? 'Cadastro'; ?></title><script src="https://cdn.tailwindcss.com"></script></head><body>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-900">Cadastro TextPro</h2>
        
        <?php if (isset($error)): ?>
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form class="mt-8 space-y-4" action="<?= BASE_URL ?>/register" method="POST">
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
            <span class="text-sm font-medium text-gray-700">Você reside no Brasil?</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="reside_brasil" id="reside_brasil" value="1" checked class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
        </div>

        <div>
            <input id="nome" name="nome" type="text" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:ring-blue-500" placeholder="Seu Nome Completo">
        </div>
        
        <div>
            <input id="email" name="email" type="email" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:ring-blue-500" placeholder="Seu Email">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div id="container_cpf">
                <input id="cpf_cnpj" name="cpf_cnpj" type="text" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:ring-blue-500" placeholder="CPF ou CNPJ">
            </div>
            <div id="container_tel" class="col-span-2 md:col-span-1">
                <input id="telefone" name="telefone" type="text" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:ring-blue-500" placeholder="WhatsApp (com DDI/DDD)">
            </div>
        </div>

        </form>

    <script>
        const toggle = document.getElementById('reside_brasil');
        const containerCpf = document.getElementById('container_cpf');
        const inputCpf = document.getElementById('cpf_cnpj');
        const containerTel = document.getElementById('container_tel');

        toggle.addEventListener('change', function() {
            if (this.checked) {
                containerCpf.style.display = 'block';
                inputCpf.required = true;
                containerTel.classList.remove('col-span-2');
            } else {
                containerCpf.style.display = 'none';
                inputCpf.required = false;
                inputCpf.value = '';
                containerTel.classList.add('col-span-2');
            }
        });
    </script>
        <div class="text-center text-sm">
            Já tem conta? <a href="<?= BASE_URL ?>/login" class="font-medium text-blue-600 hover:text-blue-500">Faça login aqui</a>
        </div>
    </div>
</div>
</body></html>

