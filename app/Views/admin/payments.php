<?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 flex items-center shadow-sm">
        <span class="mr-3">✅</span>
        <span class="font-bold"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200 flex items-center shadow-sm">
        <span class="mr-3">❌</span>
        <span class="font-bold"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
    </div>
<?php endif; ?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-black text-slate-900">Gestão de Pagamentos</h1>
    <div class="flex gap-2">
        <a href="<?= BASE_URL ?>/admin/payments" class="px-4 py-2 bg-slate-200 rounded-xl text-sm font-bold hover:bg-slate-300 transition">Todos</a>
        <a href="<?= BASE_URL ?>/admin/payments?status=OVERDUE" class="px-4 py-2 bg-red-100 text-red-700 rounded-xl text-sm font-bold hover:bg-red-200 transition">Atrasados</a>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-900 text-white">
            <tr>
                <th class="p-4">Cliente</th>
                <th class="p-4">Vencimento</th>
                <th class="p-4 text-center">Valor Original</th>
                <th class="p-4 text-center">Status</th>
                <th class="p-4 text-right">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $p): ?>
            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                <td class="p-4">
                    <div class="font-bold text-slate-900"><?= htmlspecialchars($p->user_nome) ?></div>
                    <div class="text-xs text-slate-500"><?= htmlspecialchars($p->user_email) ?></div>
                </td>
                <td class="p-4 text-slate-600 font-medium"><?= date('d/m/Y', strtotime($p->data_vencimento)) ?></td>
                <td class="p-4 text-center font-bold text-slate-900">R$ <?= number_format($p->valor, 2, ',', '.') ?></td>
                <td class="p-4 text-center">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $p->status === 'CONFIRMED' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' ?>">
                        <?= $p->status ?>
                    </span>
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="<?= $p->invoice_url ?>" target="_blank" class="inline-flex p-2 bg-slate-100 rounded-xl hover:bg-slate-200 transition" title="Ver Fatura">
                        📄
                    </a>
                    <?php if ($p->status !== 'RECEIVED' && $p->status !== 'CONFIRMED'): ?>
                        <button onclick="openModalConfirm('<?= $p->id ?>', '<?= $p->valor ?>', '<?= $p->user_nome ?>')" 
                                class="px-4 py-2 bg-green-600 text-white rounded-xl font-bold text-xs hover:bg-green-700 transition shadow-sm">
                            Baixar Manual
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modalConfirm" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-xl font-black text-slate-900">Confirmar Recebimento</h3>
            <p class="text-sm text-slate-500" id="modalUser">Cliente</p>
        </div>
        
        <form id="formConfirm" action="" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Valor Original (R$)</label>
                <input type="text" id="valOriginal" readonly class="w-full bg-slate-50 border-none rounded-xl font-bold text-slate-400">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Desconto (R$)</label>
                <input type="number" step="0.01" name="desconto" id="inputDesconto" oninput="calcFinal()" value="0.00" 
                       class="w-full border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold">
            </div>

            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                <label class="block text-xs font-bold text-blue-600 uppercase mb-1">Valor Final a Baixar</label>
                <div class="text-2xl font-black text-blue-700">R$ <span id="valFinal">0,00</span></div>
                <input type="hidden" name="valor_final" id="inputValFinal">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModalConfirm()" class="flex-1 py-3 font-bold text-slate-500 hover:bg-slate-100 rounded-2xl transition">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-slate-900 text-white font-bold rounded-2xl hover:bg-black transition shadow-lg">Confirmar Baixa</button>
            </div>
        </form>
    </div>
</div>

<script>
let valorBase = 0;

function openModalConfirm(id, valor, nome) {
    valorBase = parseFloat(valor);
    document.getElementById('formConfirm').action = "<?= BASE_URL ?>/admin/payments/confirm/" + id;
    document.getElementById('modalUser').innerText = nome;
    document.getElementById('valOriginal').value = valorBase.toFixed(2);
    document.getElementById('modalConfirm').classList.remove('hidden');
    calcFinal();
}

function closeModalConfirm() {
    document.getElementById('modalConfirm').classList.add('hidden');
}

function calcFinal() {
    const desc = parseFloat(document.getElementById('inputDesconto').value) || 0;
    const total = Math.max(0, valorBase - desc);
    document.getElementById('valFinal').innerText = total.toLocaleString('pt-br', {minimumFractionDigits: 2});
    document.getElementById('inputValFinal').value = total.toFixed(2);
}
</script>