<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">

    <!-- Base URL para corrigir paths relativos (dashboard, editor, assets, etc.) -->
    <base href="<?= BASE_URL ?>/">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'TextPro - Soluções ABNT') ?></title>

    <!-- Tailwind (dev) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .sidebar {
            min-width: 250px;
        }

        /* Garante que o footer fique sempre no final */
        .flex-container {
            min-height: 100vh;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex flex-col flex-container">
        
        <?php include __DIR__ . '/_header.php'; ?>

        <div class="flex flex-1">
            
            <?php
            // Inclui o sidebar apenas se o usuário estiver autenticado
            if (isset($_SESSION['user_id'])):
                include __DIR__ . '/_sidebar.php';
            endif;
            ?>

            <main class="flex-1 p-8 overflow-y-auto">
                <?= $content ?>
            </main>
        </div>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</body>
</html>
