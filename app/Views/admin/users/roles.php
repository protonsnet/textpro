<h1 class="text-2xl font-bold mb-6">
Roles do Usuário: <?= htmlspecialchars($user->nome) ?>
</h1>

<form method="POST">
<div class="bg-white shadow rounded p-6">
    <?php foreach ($roles as $role): ?>
    <label class="flex items-center gap-2 mb-2">
        <input type="checkbox"
               name="roles[]"
               value="<?= $role->id ?>"
               <?= in_array($role->id, $userRoles) ? 'checked' : '' ?>>
        <?= htmlspecialchars($role->nome) ?>
    </label>
    <?php endforeach; ?>

    <button class="mt-4 bg-purple-600 text-white px-4 py-2 rounded">
        Atualizar Roles
    </button>
</div>
</form>
