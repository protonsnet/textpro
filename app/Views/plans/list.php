<h1 class="text-4xl font-extrabold mb-4 text-center">
    Escolha o plano ideal para você
</h1>

<p class="text-xl text-gray-500 max-w-2xl mx-auto text-center mb-12">
    Produza documentos acadêmicos nas normas ABNT, exporte PDFs profissionais
    e transforme textos em áudio com qualidade premium.
</p>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

    <?php if (!empty($planos)): ?>
        <?php foreach ($planos as $plano): ?>
            <div class="bg-white rounded-2xl shadow-md p-8 hover:shadow-xl transition flex flex-col">

                <h2 class="text-2xl font-bold mb-2">
                    <?= htmlspecialchars($plano->nome) ?>
                </h2>

                <p class="text-gray-500 mb-6">
                    <?= nl2br(htmlspecialchars($plano->descricao)) ?>
                </p>

                <div class="text-4xl font-black text-blue-600 mb-6">
                    R$ <?= number_format($plano->preco, 2, ',', '.') ?>
                    <span class="text-sm text-gray-400 font-normal">/mês</span>
                </div>

                <?php if (!empty($plano->recursos)): ?>
                    <ul class="mb-6 space-y-2 text-sm text-gray-600">
                        <?php foreach (json_decode($plano->recursos, true) as $recurso => $valor): ?>
                            <li>✔ <?= htmlspecialchars($recurso) ?>: <?= htmlspecialchars($valor) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/plans/checkout/<?= $plano->id ?>"
                    target="_blank"
                    class="mt-auto block text-center py-3 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black transition">
                        Contratar Plano
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="col-span-3 text-center text-gray-500">
            Nenhum plano disponível no momento.
        </p>
    <?php endif; ?>

</div>
