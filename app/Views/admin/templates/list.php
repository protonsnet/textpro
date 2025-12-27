<h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo $title; ?></h1>

<?php if (isset($success)): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="flex justify-end mb-4">
    <a href="<?= BASE_URL ?>/admin/templates/create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
        Criar Novo Template
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Template</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonte</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margens (E/D)</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (!empty($templates)): ?>
                <?php foreach ($templates as $template): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $template->id; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($template->nome); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $template->fonte_familia; ?> (<?php echo $template->fonte_tamanho; ?>pt)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $template->margem_esquerda; ?>cm / <?php echo $template->margem_direita; ?>cm</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= BASE_URL ?>/admin/templates/edit/<?php echo $template->id; ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</a>
                            
                            <form action="<?= BASE_URL ?>/admin/templates/delete/<?php echo $template->id; ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o template \'<?php echo htmlspecialchars($template->nome); ?>\'?');" class="inline">
                                <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum template cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>