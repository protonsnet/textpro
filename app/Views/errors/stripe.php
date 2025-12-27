<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Erro no Pagamento</title>
</head>
<body>
    <h1>Erro ao iniciar pagamento</h1>
    <p><?= htmlspecialchars($message ?? 'Ocorreu um erro inesperado.') ?></p>

    <a href="<?= BASE_URL ?>/plans">Voltar aos planos</a>
</body>
</html>
