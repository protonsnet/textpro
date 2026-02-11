<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight"><?= $title ?? 'Meus Arquivos' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Gerencie seus documentos, planilhas e apresentações em tempo real.</p>
        </div>
        
        <div class="flex-1 max-w-md">
            <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="GET" class="relative">
                <input type="text" 
                       name="search" 
                       value="<?= htmlspecialchars($searchTerm ?? '') ?>"
                       placeholder="Buscar arquivo pelo nome..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all outline-none">
                <div class="absolute left-3 top-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <?php if(!empty($searchTerm)): ?>
                    <a href="<?= strtok($_SERVER['REQUEST_URI'], '?') ?>" class="absolute right-3 top-3 text-gray-400 hover:text-red-500">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-12 bg-gray-50 border-b border-gray-200 px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            <div class="col-span-6 md:col-span-7">Nome do Arquivo</div>
            <div class="col-span-4 md:col-span-3 text-center md:text-left">Modificado em</div>
            <div class="col-span-2 md:col-span-2 text-right">Ações</div>
        </div>

        <ul class="divide-y divide-gray-100">
            <?php if (!empty($documents)): ?>
                <?php foreach ($documents as $doc): 
                    $filePath = strtolower($doc->file_path ?? '');
                    
                    // Lógica de Identificação de Tipo e Rota
                    if (str_contains($filePath, '.xlsx') || str_contains($filePath, '.xls')) {
                        $icon = 'fa-file-excel';
                        $colorClass = 'bg-green-100 text-green-600';
                        $label = 'Planilha';
                        $extensionLabel = '.XLSX';
                        $editRoute = BASE_URL . '/editor-beta/spreadsheet/' . $doc->id;
                    } elseif (str_contains($filePath, '.pptx') || str_contains($filePath, '.ppt')) {
                        $icon = 'fa-file-powerpoint';
                        $colorClass = 'bg-orange-100 text-orange-600'; // Cor padrão PowerPoint
                        $label = 'Apresentação';
                        $extensionLabel = '.PPTX';
                        $editRoute = BASE_URL . '/editor-beta/presentation/' . $doc->id;
                    } else {
                        $icon = 'fa-file-word';
                        $colorClass = 'bg-blue-100 text-blue-600';
                        $label = 'Documento';
                        $extensionLabel = '.DOCX';
                        $editRoute = BASE_URL . '/editor-beta/' . $doc->id;
                    }
                ?>
                    <li class="grid grid-cols-12 items-center px-6 py-4 hover:bg-gray-50 transition-colors group">
                        
                        <div class="col-span-6 md:col-span-7 flex items-center gap-4">
                            <div class="w-10 h-10 flex-shrink-0 <?= $colorClass ?> rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fa-solid <?= $icon ?> text-xl"></i>
                            </div>
                            <div class="truncate">
                                <a href="<?= $editRoute ?>" target="_blank" class="text-sm font-semibold text-gray-800 hover:text-blue-600 truncate block">
                                    <?= htmlspecialchars($doc->titulo) ?>
                                </a>
                                <span class="text-[10px] text-gray-400 uppercase font-medium">
                                    <?= $label ?> <span class="opacity-70"><?= $extensionLabel ?></span>
                                </span>
                            </div>
                        </div>

                        <div class="col-span-4 md:col-span-3 text-xs text-gray-500 italic">
                            <?= date('d/m/Y', strtotime($doc->updated_at)) ?>
                            <span class="hidden md:inline ml-1 text-gray-400">às <?= date('H:i', strtotime($doc->updated_at)) ?></span>
                        </div>

                        <div class="col-span-2 md:col-span-2 flex justify-end items-center space-x-2">
                            <button onclick="openModalRename(<?= $doc->id ?>, '<?= addslashes($doc->titulo) ?>')" 
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all" 
                                    title="Renomear">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            
                            <a href="<?= $editRoute ?>" target="_blank" 
                               class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-full transition-all" 
                               title="Abrir no Editor Online">
                                <i class="fa-solid fa-external-link"></i>
                            </a>

                            <form action="<?= BASE_URL ?>/document/delete/<?= $doc->id ?>" method="POST" onsubmit="return confirm('Excluir permanentemente este arquivo?');" class="inline">
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="py-20 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fa-solid fa-folder-open text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Nenhum arquivo encontrado.</p>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>