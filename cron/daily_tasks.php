<?php
require_once __DIR__ . '/../vendor/autoload.php';
// Carregar env, db, etc.

$db = \App\Core\Database::getInstance();
$asaas = new \App\Services\AsaasService();

// TAREFA 1: Bloquear usuários inadimplentes (1 dia após vencimento)
$db->query("
    UPDATE system_users u
    JOIN subscriptions s ON u.id = s.user_id
    SET u.suspenso = 1
    WHERE s.proximo_vencimento < CURDATE() 
    AND s.status != 'active'
");

// TAREFA 2: Gerar boletos 10 dias antes do vencimento
$preBilling = $db->query("
    SELECT s.*, u.asaas_customer_id, p.preco, p.nome as plano_nome 
    FROM subscriptions s
    JOIN system_users u ON s.user_id = u.id
    JOIN plans p ON s.plan_id = p.id
    WHERE s.proximo_vencimento = DATE_ADD(CURDATE(), INTERVAL 10 DAY)
")->fetchAll(PDO::FETCH_OBJ);

foreach ($preBilling as $sub) {
    $billing = $asaas->createBilling(
        $sub->asaas_customer_id, 
        $sub->preco, 
        $sub->proximo_vencimento, 
        "Renovação: " . $sub->plano_nome
    );
    // Salvar $billing->id na tabela payments
}