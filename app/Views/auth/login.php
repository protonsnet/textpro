<html lang="pt-br"><head><meta charset="UTF-8"><title><?php echo $title ?? 'Login'; ?></title><script src="https://cdn.tailwindcss.com"></script></head><body>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <h2 class="text-3xl font-bold text-center text-gray-900">Login TextPro</h2>
        
        <?php if (isset($error)): ?>
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="p-3 text-sm text-green-700 bg-green-100 rounded-lg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form class="mt-8 space-y-4" action="<?= BASE_URL ?>/login" method="POST">
            <div>
                <input id="email" name="email" type="email" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Email">
            </div>
            <div>
                <input id="senha" name="senha" type="password" required class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Senha">
            </div>

            <div class="flex items-center justify-end">
                <div class="text-sm">
                    <a href="<?= BASE_URL ?>/forgot-password" class="font-medium text-blue-600 hover:text-blue-500">
                        Esqueceu sua senha?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Entrar
                </button>
            </div>
        </form>
        <div class="text-center text-sm">
            Não tem conta? <a href="<?= BASE_URL ?>/register" class="font-medium text-blue-600 hover:text-blue-500">Cadastre-se</a>
        </div>
    </div>
</div>
</body></html>