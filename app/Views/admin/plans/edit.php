<div class="container mx-auto p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <?= $title ?>
    </h1>

    <a href="<?= BASE_URL ?>/admin/plans"
       class="text-blue-600 hover:underline mb-4 inline-block">
        &larr; Voltar para Planos
    </a>

    <?php if (!empty($error)): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php $recursos = $plan->recursos ?? []; ?>


    <form method="POST"
          class="bg-white p-6 rounded-lg shadow-md space-y-6">

        <!-- INFORMAÇÕES -->
        <h2 class="text-xl font-semibold border-b pb-2 text-blue-600">
            Informações do Plano
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Nome *
                </label>
                <input name="nome"
                       value="<?= htmlspecialchars($plan->nome) ?>" required
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Preço (R$)
                </label>
                <input name="preco" type="number" step="0.01"
                       value="<?= $plan->preco ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">
                Descrição
            </label>
            <textarea name="descricao" rows="3"
                      class="mt-1 block w-full border border-gray-300 rounded-md p-2"><?= htmlspecialchars($plan->descricao) ?></textarea>
        </div>

        <!-- CONFIGURAÇÕES -->
        <h2 class="text-xl font-semibold border-b pb-2 text-blue-600">
            Configurações
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Duração (meses)
                </label>
                <input name="duracao_meses" type="number"
                       value="<?= $plan->duracao_meses ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Stripe Price ID
                </label>
                <input name="stripe_price_id"
                       value="<?= htmlspecialchars($plan->stripe_price_id) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md p-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Status
                </label>
                <select name="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                    <option value="ativo" <?= $plan->status === 'ativo' ? 'selected' : '' ?>>
                        Ativo
                    </option>
                    <option value="inativo" <?= $plan->status === 'inativo' ? 'selected' : '' ?>>
                        Inativo
                    </option>
                </select>
            </div>
        </div>

        <!-- RECURSOS -->
        <h2 class="text-xl font-semibold border-b pb-2 text-blue-600">
            Recursos do Plano
        </h2>

        <div class="space-y-2">
            <?php foreach (['Editor ABNT', 'Exportação PDF', 'Templates Premium'] as $r): ?>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="recursos[]" value="<?= $r ?>"
                        <?= in_array($r, $recursos, true) ? 'checked' : '' ?>>
                    <span><?= $r ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- AÇÕES -->
        <div class="pt-6">
            <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition">
                Atualizar Plano
            </button>
        </div>
    </form>
</div>
