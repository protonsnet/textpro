<h1 class="text-3xl font-bold mb-6"><?= $title ?></h1>

<form method="POST" action="<?= BASE_URL ?>/admin/users/update/<?= $user->id ?>" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold">Nome Completo</label>
                <input type="text" name="nome" value="<?= $user->nome ?>" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-sm font-semibold">Email</label>
                <input type="email" name="email" value="<?= $user->email ?>" class="w-full border rounded p-2" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold">País (ISO)</label>
                    <input type="text" name="pais" value="<?= $user->pais ?>" class="w-full border rounded p-2" placeholder="Ex: BR, US, PT">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Telefone</label>
                    <input type="text" name="telefone" value="<?= $user->telefone ?>" class="w-full border rounded p-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold">CPF/CNPJ (Apenas Brasil)</label>
                <input type="text" name="cpf_cnpj" value="<?= $user->cpf_cnpj ?>" class="w-full border rounded p-2">
            </div>
        </div>

        <div class="space-y-4 border-l pl-6">
            <div>
                <label class="block text-sm font-semibold mb-2">Foto de Perfil</label>
                <div class="flex items-center gap-4">
                    <img src="<?= $user->foto ? BASE_URL.'/uploads/profiles/'.$user->foto : 'https://ui-avatars.com/api/?name='.urlencode($user->nome) ?>" 
                         class="w-16 h-16 rounded-full border">
                    <input type="file" name="foto" class="text-sm">
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded border">
                <label class="flex items-center gap-2 font-semibold text-sm">
                    <input type="checkbox" name="ativo" value="1" <?= $user->ativo ? 'checked' : '' ?>>
                    Usuário Ativo (Acesso ao Sistema)
                </label>
                <p class="text-xs text-gray-500 ml-5 mb-3">Se desmarcado, o usuário não consegue logar.</p>

                <label class="flex items-center gap-2 font-semibold text-sm text-red-600">
                    <input type="checkbox" name="suspenso" value="1" <?= $user->suspenso ? 'checked' : '' ?>>
                    Suspender por Inadimplência
                </label>
                <p class="text-xs text-gray-500 ml-5">Bloqueia recursos financeiros/assinatura manualmente.</p>
            </div>

            <div class="text-xs text-gray-400">
                <p>ID Asaas: <?= $user->asaas_customer_id ?? 'N/A' ?></p>
                <p>ID Stripe: <?= $user->stripe_customer_id ?? 'N/A' ?></p>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t flex gap-3">
        <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Atualizar Usuário</button>
        <a href="<?= BASE_URL ?>/admin/users" class="px-6 py-2 border rounded hover:bg-gray-100">Voltar</a>
    </div>
</form>