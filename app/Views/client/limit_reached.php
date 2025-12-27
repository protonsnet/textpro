<div class="max-w-2xl mx-auto mt-20 text-center">
    <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-200">
        <div class="text-6xl mb-6">🚀</div>
        <h1 class="text-3xl font-black text-slate-900 mb-4">Limite de Documentos Atingido</h1>
        
        <p class="text-slate-600 mb-4 text-lg">
            Você atingiu o limite de <strong><?= $_SESSION['plan_limit'] ?? 0 ?></strong> documentos do seu plano atual.
        </p>
        
        <p class="text-slate-500 mb-8">
            Para continuar criando, você pode excluir documentos que não utiliza mais na sua lista ou fazer um upgrade para um plano superior.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>/plans" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition flex items-center justify-center">
                <span>⭐ Ver Planos / Upgrade</span>
            </a>
            <a href="<?= BASE_URL ?>/dashboard" class="px-8 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition flex items-center justify-center">
                Voltar aos Meus Documentos
            </a>
        </div>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400">
                    ID do Usuário: <?= $_SESSION['user_id'] ?> | 
                    Limite atual em sessão: <?= $_SESSION['plan_limit'] ?? 'Não definido' ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>