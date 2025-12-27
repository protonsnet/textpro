<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Visão Geral</h1>
    <p class="text-gray-500">Métricas de desempenho e atividades recentes do sistema.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
        <div class="p-3 bg-blue-100 rounded-lg mr-4">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Clientes Ativos</p>
            <p class="text-2xl font-bold text-gray-800"><?= $total_clientes ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
        <div class="p-3 bg-green-100 rounded-lg mr-4">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Templates</p>
            <p class="text-2xl font-bold text-gray-800"><?= $templates_ativos ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
        <div class="p-3 bg-purple-100 rounded-lg mr-4">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Faturamento (Mês)</p>
            <p class="text-2xl font-bold text-gray-800">R$ <?= number_format($faturamento_real, 2, ',', '.') ?></p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Documentos Recentes</h2>
        <a href="<?= BASE_URL ?>/admin/templates" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Gerenciar Templates &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                <tr>
                    <th class="px-6 py-4">Título</th>
                    <th class="px-6 py-4">Proprietário</th>
                    <th class="px-6 py-4">Criado em</th>
                    <th class="px-6 py-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($documentos as $doc): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800"><?= $doc->titulo ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-sm"><?= $doc->proprietario ?></span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-sm"><?= date('d/m/Y H:i', strtotime($doc->created_at)) ?></td>
                    <td class="px-6 py-4 text-center">
                        <a href="<?= BASE_URL ?>/document/export/pdf/<?= $doc->id ?>" target="_blank" class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"></path></svg>
                            Gerar PDF
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>