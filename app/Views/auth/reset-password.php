<html lang="pt-br"><head><meta charset="UTF-8"><title>Nova Senha</title><script src="https://cdn.tailwindcss.com"></script></head><body>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-900">Nova Senha</h2>
        
        <p class="text-gray-600 text-sm text-center">
            Crie uma senha forte e segura para sua conta.
        </p>

        <?php if (isset($error)): ?>
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form class="mt-8 space-y-4" action="<?= BASE_URL ?>/reset-password" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                <input name="senha" type="password" required minlength="6"
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Mínimo 6 caracteres">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                <input name="confirmar_senha" type="password" required minlength="6"
                       class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Repita a senha">
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                    Atualizar Minha Senha
                </button>
            </div>
        </form>
    </div>
</div>
</body></html>