<h1 class="text-3xl font-bold mb-6">Roles do Sistema</h1>

<div class="bg-white shadow rounded">
<table class="w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="p-4">Nome</th>
    <th>Descrição</th>
    <th class="p-4 text-right">Ações</th>
</tr>
</thead>
<tbody class="divide-y">
<?php foreach ($roles as $role): ?>
<tr>
    <td class="p-4 font-medium"><?= htmlspecialchars($role->nome) ?></td>
    <td><?= htmlspecialchars($role->descricao) ?></td>
    <td class="p-4 text-right">
        <a href="<?= BASE_URL ?>/admin/roles/edit/<?= $role->id ?>"
           class="text-blue-600 hover:underline">Editar</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
