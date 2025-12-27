<h1 class="text-3xl font-bold mb-6"><?= $title ?></h1>

<?php if (!empty($_GET['error'])): ?>
<div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
    <?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= BASE_URL ?>/admin/users/store"
      class="bg-white p-6 rounded shadow max-w-lg space-y-4">

    <div>
        <label class="block text-sm font-semibold mb-1">Nome</label>
        <input type="text" name="nome" required
               class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Email</label>
        <input type="email" name="email" required
               class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Senha</label>
        <input type="password" name="senha" required
               class="w-full border rounded p-2">
    </div>

    <div class="flex justify-end space-x-2">
        <a href="<?= BASE_URL ?>/admin/users"
           class="px-4 py-2 border rounded">
            Cancelar
        </a>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Salvar
        </button>
    </div>
</form>
