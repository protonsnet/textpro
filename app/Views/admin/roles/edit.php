<h1 class="text-2xl font-bold mb-6">
Editar Role: <?= htmlspecialchars($role->nome) ?>
</h1>

<form method="POST">
<div class="bg-white shadow rounded p-6">
    <?php foreach ($permissions as $permission): ?>
    <label class="flex items-center gap-2 mb-2">
        <input type="checkbox"
               name="permissions[]"
               value="<?= $permission->id ?>"
               <?= in_array($permission->id, $assignedPermissions) ? 'checked' : '' ?>>
        <?= htmlspecialchars($permission->descricao) ?>
    </label>
    <?php endforeach; ?>

    <button class="mt-4 bg-purple-600 text-white px-4 py-2 rounded">
        Salvar
    </button>
</div>
</form>
