<html lang="pt-br"><head><meta charset="UTF-8"><title>Recuperar Senha</title><script src="https://cdn.tailwindcss.com"></script></head><body>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-900">Recuperar Senha</h2>
        
        <?php if (isset($error)): ?>
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="p-3 text-sm text-green-700 bg-green-100 rounded-lg">
                <?= $success ?>
            </div>
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/login" class="text-blue-600 hover:underline">Voltar para o Login</a>
            </div>
        <?php else: ?>
            <p class="text-gray-600 text-sm text-center">
                Informe seu e-mail cadastrado e enviaremos um link seguro para você criar uma nova senha.
            </p>

            <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/forgot-password" method="POST">
                <div>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="E-mail cadastrado">
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                        Enviar Link de Recuperação
                    </button>
                </div>
            </form>
            <div class="text-center text-sm">
                <a href="<?= BASE_URL ?>/login" class="font-medium text-blue-600 hover:text-blue-500">Voltar para o login</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body></html>