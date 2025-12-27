<header class="bg-white shadow-md">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        
        <div class="text-2xl font-bold text-blue-600">
            <a href="<?= BASE_URL ?>/">TextPro</a>
        </div>

        <nav class="space-x-4 flex items-center">
            <?php if (!empty($_SESSION['user_id'])): ?>

                <span class="text-gray-700">
                    Bem-vindo, <?= htmlspecialchars($_SESSION['user_nome'] ?? 'Usuário') ?>
                </span>

                <a href="<?= BASE_URL ?>/dashboard"
                   class="text-gray-600 hover:text-blue-600 transition">
                    Dashboard
                </a>

                <a href="<?= BASE_URL ?>/logout"
                   class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    Sair
                </a>

            <?php else: ?>

                <a href="<?= BASE_URL ?>/plans"
                   class="text-gray-600 hover:text-blue-600 transition">
                    Planos
                </a>

                <a href="<?= BASE_URL ?>/register"
                   class="text-gray-600 hover:text-blue-600 transition">
                    Cadastre-se
                </a>

                <a href="<?= BASE_URL ?>/login"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Login
                </a>

            <?php endif; ?>
        </nav>
    </div>
</header>
