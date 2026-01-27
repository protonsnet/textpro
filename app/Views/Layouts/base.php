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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* .sidebar {
            width: 16rem;
            min-width: auto;
        } */

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

            <main class="flex-1 p-8 overflow-y-auto min-w-0">
                <?= $content ?>
            </main>
        </div>

        <?php include __DIR__ . '/_footer.php'; ?>
    </div>
</body>
</html>
