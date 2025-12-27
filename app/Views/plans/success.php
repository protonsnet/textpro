<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Pagamento em Processamento') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-gray-800 flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 rounded-2xl shadow-lg max-w-lg text-center">
        <h1 class="text-3xl font-extrabold text-green-600 mb-4">
            Pagamento Recebido!
        </h1>

        <p class="text-gray-600 mb-6">
            Seu pagamento foi processado com sucesso.  
            A ativação do plano pode levar alguns instantes.
        </p>

        <a href="<?= BASE_URL ?>/dashboard"
            class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                Ir para o Dashboard
        </a>

        <p class="mt-4 text-sm text-gray-400">
            Caso tenha dúvidas, entre em contato com nosso suporte.
        </p>
    </div>

</body>
<script>
    // Opcional: Se quiser que ao clicar no botão a aba se feche 
    // e tente focar na janela principal (depende do navegador)
    /*
    document.querySelector('a').addEventListener('click', function() {
        setTimeout(function() { window.close(); }, 500);
    });
    */
</script>
</html>


