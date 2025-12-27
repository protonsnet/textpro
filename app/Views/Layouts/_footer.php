<footer class="bg-gray-800 text-white p-6 mt-auto">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center text-sm">

        <p class="mb-3 md:mb-0">
            &copy; <?= date('Y') ?> TextPro. Todos os direitos reservados.
        </p>

        <div class="space-x-4">
            <a href="<?= BASE_URL ?>/terms" class="hover:text-blue-400">
                Termos de Serviço
            </a>

            <a href="<?= BASE_URL ?>/privacy" class="hover:text-blue-400">
                Política de Privacidade
            </a>

            <?php if (!empty($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/admin"
                   class="text-red-400 hover:text-red-300">
                    Admin
                </a>
            <?php endif; ?>
        </div>

    </div>
</footer>
