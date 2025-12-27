<h1 class="text-3xl font-bold mb-6">Permissões do Sistema</h1>

<div class="bg-white shadow rounded">
<table class="w-full text-sm">
<thead class="bg-gray-100">
<tr>
    <th class="p-4">Chave</th>
    <th>Descrição</th>
</tr>
</thead>
<tbody class="divide-y">
<?php foreach ($permissions as $permission): ?>
<tr>
    <td class="p-4 font-mono"><?= $permission->chave ?></td>
    <td><?= htmlspecialchars($permission->descricao) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
