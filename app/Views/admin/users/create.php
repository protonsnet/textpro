<h1 class="text-3xl font-bold mb-6"><?= $title ?></h1>

<?php if (!empty($_GET['error'])): ?>
<div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
    <?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/admin/users/store" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold">Nome Completo</label>
                <input type="text" name="nome" class="w-full border rounded p-2" required placeholder="Ex: João Silva">
            </div>
            <div>
                <label class="block text-sm font-semibold">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2" required placeholder="exemplo@email.com">
            </div>
            <div>
                <label class="block text-sm font-semibold">Senha de Acesso</label>
                <input type="password" name="senha" class="w-full border rounded p-2" required placeholder="Defina uma senha inicial">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold">País (ISO)</label>
                    <input type="text" name="pais" value="BR" class="w-full border rounded p-2" placeholder="Ex: BR, US">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Telefone</label>
                    <input type="text" name="telefone" class="w-full border rounded p-2" placeholder="(00) 00000-0000">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold">CPF/CNPJ (Apenas Brasil)</label>
                <input type="text" name="cpf_cnpj" class="w-full border rounded p-2" placeholder="000.000.000-00">
            </div>
        </div>

        <div class="space-y-4 border-l pl-6">
            <div>
                <label class="block text-sm font-semibold mb-2">Foto de Perfil (Opcional)</label>
                <input type="file" name="foto" class="text-sm">
            </div>

            <div class="p-4 bg-gray-50 rounded border">
                <label class="flex items-center gap-2 font-semibold text-sm">
                    <input type="checkbox" name="ativo" value="1" checked>
                    Usuário Ativo (Acesso ao Sistema)
                </label>
                <p class="text-xs text-gray-500 ml-5 mb-3">Se desmarcado, o usuário não conseguirá logar.</p>

                <label class="flex items-center gap-2 font-semibold text-sm text-red-600">
                    <input type="checkbox" name="suspenso" value="1">
                    Suspender por Inadimplência
                </label>
                <p class="text-xs text-gray-500 ml-5">Bloqueia recursos financeiros manualmente.</p>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t flex gap-3">
        <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold">Criar Usuário</button>
        <a href="<?= BASE_URL ?>/admin/users" class="px-6 py-2 border rounded hover:bg-gray-100">Cancelar</a>
    </div>
</form>