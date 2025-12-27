<h1 class="text-3xl font-bold text-gray-800 mb-6">
    <?php echo $title; ?>
</h1>

<?php if (isset($success)): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="flex justify-end mb-4">
    <a href="<?= BASE_URL ?>/admin/plans/create"
       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
        Criar Novo Plano
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Plano
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Preço
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Stripe Price ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ações
                </th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">

        <?php if (!empty($plans)): ?>
            <?php foreach ($plans as $plan): ?>
                <tr>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo $plan->id; ?>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-700">
                        <div class="font-semibold">
                            <?php echo htmlspecialchars($plan->nome); ?>
                        </div>
                        <div class="text-xs text-gray-500">
                            <?php echo nl2br(htmlspecialchars($plan->descricao)); ?>
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                        R$ <?php echo number_format($plan->preco, 2, ',', '.'); ?>
                    </td>

                    <td class="px-6 py-4 text-xs text-gray-500 break-all">
                        <?php echo htmlspecialchars($plan->stripe_price_id); ?>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php if ($plan->status === 'ativo'): ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                Ativo
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                Inativo
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                        <a href="<?= BASE_URL ?>/admin/plans/edit/<?php echo $plan->id; ?>"
                           class="text-indigo-600 hover:text-indigo-900 mr-4">
                            Editar
                        </a>

                        <?php if ($plan->status === 'ativo'): ?>
                            <form action="<?= BASE_URL ?>/admin/plans/deactivate/<?php echo $plan->id; ?>"
                                  method="POST"
                                  onsubmit="return confirm('Deseja realmente desativar o plano &quot;<?php echo htmlspecialchars($plan->nome); ?>&quot;?');"
                                  class="inline">
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900">
                                    Desativar
                                </button>
                            </form>
                        <?php endif; ?>

                    </td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Nenhum plano cadastrado.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>
