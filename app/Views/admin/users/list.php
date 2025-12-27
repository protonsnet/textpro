<?php use App\Core\PermissionMiddleware; ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold"><?= $title ?></h1>
    <?php if (PermissionMiddleware::can('users.manage')): ?>
        <a href="<?= BASE_URL ?>/admin/users/create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            + Cadastrar Usuário
        </a>
    <?php endif; ?>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
<table class="w-full text-left text-sm">
<thead class="bg-gray-100 border-b">
<tr>
    <th class="p-4">Usuário</th>
    <th>Email</th>
    <th>País</th>
    <th>Status (Sistema)</th>
    <th>Financeiro</th>
    <th class="p-4 text-right">Ações</th>
</tr>
</thead>
<tbody class="divide-y">
<?php foreach ($users as $user): ?>
<tr>
    <td class="p-4 flex items-center gap-3">
        <img src="<?= $user->foto ? BASE_URL.'/uploads/profiles/'.$user->foto : 'https://ui-avatars.com/api/?name='.urlencode($user->nome) ?>" 
             class="w-8 h-8 rounded-full object-cover border">
        <span class="font-medium"><?= htmlspecialchars($user->nome) ?></span>
    </td>
    <td><?= htmlspecialchars($user->email) ?></td>
    <td><span class="px-2 py-1 bg-gray-200 rounded text-xs"><?= $user->pais ?></span></td>
    <td>
        <?= $user->ativo 
            ? '<span class="text-green-600">● Ativo</span>' 
            : '<span class="text-red-600">● Inativo</span>' ?>
    </td>
    <td>
        <?= $user->suspenso 
            ? '<span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">Bloqueado</span>' 
            : '<span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Regular</span>' ?>
    </td>
    <td class="p-4 text-right space-x-2">
        <a href="<?= BASE_URL ?>/admin/users/edit/<?= $user->id ?>" class="text-indigo-600 hover:underline">Editar</a>
        <a href="<?= BASE_URL ?>/admin/users/roles/<?= $user->id ?>" class="text-purple-600 hover:underline">Roles</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>