
<h1 class="text-3xl font-black text-slate-900 mb-8">Minhas Faturas</h1>

<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="p-4 font-bold text-slate-700">Vencimento</th>
                <th class="p-4 font-bold text-slate-700">Valor</th>
                <th class="p-4 font-bold text-slate-700">Status</th>
                <th class="p-4 font-bold text-slate-700 text-right">Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $p): ?>
            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                <td class="p-4 text-slate-600"><?= date('d/m/Y', strtotime($p->data_vencimento)) ?></td>
                <td class="p-4 font-semibold text-slate-900">R$ <?= number_format($p->valor, 2, ',', '.') ?></td>
                <td class="p-4">
                    <?php if ($p->status === 'PENDING'): ?>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">Pendente</span>
                    <?php elseif ($p->status === 'RECEIVED' || $p->status === 'CONFIRMED'): ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Pago</span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Atrasado</span>
                    <?php endif; ?>
                </td>
                <td class="p-4 text-right">
                    <?php if ($p->status === 'PENDING' || $p->status === 'OVERDUE'): ?>
                        <a href="<?= $p->invoice_url ?>" target="_blank" 
                            class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition">
                            Pagar Agora
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
