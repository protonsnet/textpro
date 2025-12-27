<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-black text-slate-900 mb-8">Configurações de Perfil</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/profile/update" method="POST" enctype="multipart/form-data" class="space-y-6">
        
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
            <div class="flex items-center space-x-8 mb-8">
                <div class="relative">
                    <?php 
                        // Tratando foto nula
                        $foto = $user->foto ?? '';
                        $fotoPath = !empty($foto) ? BASE_URL . '/public/uploads/profiles/' . $foto : 'https://ui-avatars.com/api/?name=' . urlencode($user->nome);
                    ?>
                    <img src="<?= $fotoPath ?>" id="preview" class="w-32 h-32 rounded-full object-cover border-4 border-slate-100 shadow-sm">
                    <label for="foto" class="absolute bottom-0 right-0 bg-blue-600 p-2 rounded-full text-white cursor-pointer hover:bg-blue-700 shadow-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </label>
                    <input type="file" id="foto" name="foto" class="hidden" accept="image/*" onchange="previewImage(this)">
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($user->nome ?? '') ?></h2>
                    <p class="text-slate-500"><?= htmlspecialchars($user->email ?? '') ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nome Completo</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($user->nome ?? '') ?>" required 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">E-mail</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">CPF / CNPJ</label>
                    <input type="text" name="cpf_cnpj" value="<?= htmlspecialchars($user->cpf_cnpj ?? '') ?>" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Telefone</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($user->telefone ?? '') ?>" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-black transition shadow-lg">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>