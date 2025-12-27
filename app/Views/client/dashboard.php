<h2 class="text-3xl font-bold text-gray-800 mb-6">Olá, <?php echo htmlspecialchars($user_name); ?>!</h2>
            
<?php
$subscriptionStatus = $subscription->status ?? null;
$isActive = in_array(strtolower((string)$subscriptionStatus), ['active', 'trialing'], true);
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8 border-l-4 
    <?php echo $isActive ? 'border-green-500' : 'border-red-500'; ?>">
    <h3 class="text-xl font-semibold mb-2">Status da sua Assinatura</h3>
    
    <?php if ($currentPlan): ?>
        <p class="text-gray-700">Plano Atual: <span class="font-bold text-blue-600"><?php echo htmlspecialchars($currentPlan->nome); ?></span></p>
        <p class="text-sm text-gray-500">
            Status: <span class="uppercase font-semibold text-<?php echo (strtolower($subscription->status) === 'active') ? 'green' : 'yellow'; ?>-600"><?php echo htmlspecialchars($subscription->status); ?></span> 
            | Fim: <?php echo date('d/m/Y', strtotime($subscription->data_fim)); ?>
        </p>
    <?php else: ?>
        <p class="text-red-500 font-semibold">Nenhuma assinatura ativa encontrada.</p>
        <p class="text-sm">Vá para a seção "Mudar Plano" para começar.</p>
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