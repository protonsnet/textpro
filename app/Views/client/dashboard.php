<h2 class="text-3xl font-bold text-gray-800 mb-6">Olá, <?php echo htmlspecialchars($user_name); ?>!</h2>
            
<?php
$subscriptionStatus = strtolower((string)($subscription->status ?? ''));
$isActive = in_array($subscriptionStatus, ['active', 'trialing'], true);
// Define a data de expiração priorizando o próximo vencimento
$expiracao = !empty($subscription->proximo_vencimento) ? $subscription->proximo_vencimento : ($subscription->data_fim ?? null);
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8 border-l-4 
    <?php echo $isActive ? 'border-green-500' : 'border-red-500'; ?>">
    <h3 class="text-xl font-semibold mb-2">Status da sua Assinatura</h3>
    
    <?php if ($isActive): ?>
        <div class="mt-2 p-3 rounded <?php echo ($subscriptionStatus === 'trialing') ? 'bg-yellow-50 text-yellow-800' : 'bg-green-50 text-green-800'; ?>">
            <p class="text-sm">
                <strong>Status:</strong> 
                <span class="uppercase font-bold">
                    <?php echo ($subscriptionStatus === 'trialing') ? 'PERÍODO DE EXPERIÊNCIA' : 'PLANO ATIVO'; ?>
                </span> 
                <?php if ($expiracao): ?>
                    <span class="mx-2">|</span> 
                    <strong>Expira em:</strong> 
                    <span class="font-semibold"><?php echo date('d/m/Y', strtotime($expiracao)); ?></span>
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        <p class="text-red-500 font-semibold">Sua assinatura expirou ou não foi encontrada.</p>
        <p class="text-sm mt-1">Para continuar criando e exportando documentos, regularize seu acesso.</p>
        <a href="<?= BASE_URL ?>/plans" class="mt-3 inline-block bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold">Ver Planos</a>
    <?php endif; ?>
</div>

<div class="flex justify-between items-center mb-4">
    <h3 class="text-2xl font-semibold text-gray-800">Seus Documentos</h3>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <ul class="divide-y divide-gray-200">
        <?php if (!empty($documents)): ?>
            <?php foreach ($documents as $doc): ?>
                <li class="p-4 hover:bg-gray-50 transition flex justify-between items-center">
                    <div>
                        <p class="text-lg font-semibold text-gray-800">
                            <?php echo htmlspecialchars($doc->titulo ?? 'Documento sem título'); ?>
                        </p>
                        <p class="text-sm text-gray-500">Última edição: <?php echo date('d/m/Y H:i', strtotime($doc->updated_at)); ?></p>
                    </div>
                    <div class="space-x-3">
                        <a href="<?= BASE_URL ?>/editor/<?php echo $doc->id; ?>" class="text-blue-600 hover:underline">Editar</a>
                        <a href="<?= BASE_URL ?>/document/export/pdf/<?php echo $doc->id; ?>" target="_blank" class="text-red-600 hover:underline">Exportar PDF</a>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="p-6 text-center text-gray-500">
                Nenhum documento encontrado. Clique em "Novo Documento" para começar!
            </li>
        <?php endif; ?>
    </ul>
</div>