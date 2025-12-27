
  
            <h1 class="text-3xl font-black text-slate-900 mb-2">Segurança da Conta</h1>
            <p class="text-slate-500 mb-8">Mantenha sua conta segura alterando sua senha regularmente.</p>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-medium">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 font-medium">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <form action="<?= BASE_URL ?>/profile/password/update" method="POST" class="p-8 space-y-6">
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Senha Atual</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nova Senha</label>
                        <input type="password" name="new_password" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <p class="mt-1 text-xs text-slate-400">Mínimo de 6 caracteres.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Confirmar Nova Senha</label>
                        <input type="password" name="confirm_password" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full md:w-auto px-8 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition active:scale-95">
                            Atualizar Senha
                        </button>
                    </div>
                </form>
            </div>

<script>
    lucide.createIcons();
</script>