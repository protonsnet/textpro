<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Permissões do Sistema</h1>
    <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        + Nova Permissão
    </button>
</div>

<div class="bg-white shadow rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="p-4 text-left">Chave (Slug)</th>
                <th class="text-left">Descrição</th>
                <th class="p-4 text-center">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($permissions as $p): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-4 font-mono text-blue-700"><?= $p->chave ?></td>
                <td class="text-gray-600"><?= htmlspecialchars($p->descricao) ?></td>
                <td class="p-4 text-center space-x-2">
                    <button onclick='editPermission(<?= json_encode($p) ?>)' class="text-blue-500 hover:underline">Editar</button>
                    <a href="<?= BASE_URL ?>/admin/permissions/delete/<?= $p->id ?>" 
                       onclick="return confirm('Tem certeza? Isso afetará todos os usuários com esta permissão.')" 
                       class="text-red-500 hover:underline">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="permModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Nova Permissão</h2>
        <form action="<?= BASE_URL ?>/admin/permissions/store" method="POST">
            <input type="hidden" name="id" id="perm_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Chave (ex: modulo.acao)</label>
                <input type="text" name="chave" id="perm_chave" class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Descrição</label>
                <input type="text" name="descricao" id="perm_desc" class="w-full border rounded px-3 py-2">
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('perm_id').value = '';
    document.getElementById('perm_chave').value = '';
    document.getElementById('perm_desc').value = '';
    document.getElementById('modalTitle').innerText = 'Nova Permissão';
    document.getElementById('permModal').classList.remove('hidden');
}

function editPermission(data) {
    document.getElementById('perm_id').value = data.id;
    document.getElementById('perm_chave').value = data.chave;
    document.getElementById('perm_desc').value = data.descricao;
    document.getElementById('modalTitle').innerText = 'Editar Permissão';
    document.getElementById('permModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('permModal').classList.add('hidden');
}
</script>