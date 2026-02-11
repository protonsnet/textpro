<div class="flex justify-between items-center mb-6">
    <h3 class="text-2xl font-semibold text-gray-800">Seus Documentos</h3>
    <div class="flex space-x-2">
         <!-- <button onclick="openModalCreate('excel')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold transition flex items-center shadow-sm">
            <i class="fa-solid fa-file-excel mr-2"></i> Nova Planilha
        </button>
        <button onclick="openModalCreate('word')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-bold transition flex items-center shadow-sm">
            <i class="fa-solid fa-file-word mr-2"></i> Novo Word
        </button> -->
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <ul class="divide-y divide-gray-200">
        <?php if (!empty($documents)): ?>
            <?php foreach ($documents as $doc): 
                // Evita erro de Deprecated no PHP 8.1+ garantindo que seja string
                $filePath = $doc->file_path ?? ''; 
                $isXlsx   = str_ends_with($filePath, '.xlsx');
                $isDocx   = str_ends_with($filePath, '.docx');
                $isPptx   = str_ends_with($filePath, '.pptx');
                
                // Define a URL e Estilo com base no tipo
                if ($isXlsx) {
                    $editUrl = BASE_URL . "/editor-beta/spreadsheet/" . $doc->id;
                    $colorClass = "text-green-600";
                    $label = "Excel";
                    $labelClass = "bg-green-100 text-green-700";
                    $icon = "fa-file-excel";
                } elseif ($isDocx) {
                    $editUrl = BASE_URL . "/editor-beta/" . $doc->id;
                    $colorClass = "text-purple-600";
                    $label = "Word";
                    $labelClass = "bg-purple-100 text-purple-700";
                    $icon = "fa-file-word";
                } elseif ($isPptx) {
                    $editUrl = BASE_URL . "/editor-beta/presentation/" . $doc->id;
                    $colorClass = "text-orange-600";
                    $label = "Apresentação";
                    $labelClass = "bg-orange-100 text-orange-600";
                    $icon = "fa-file-powerpoint";
                } else {
                    $editUrl = BASE_URL . "/editor/" . $doc->id;
                    $colorClass = "text-blue-600";
                    $label = "Canvas";
                    $labelClass = "bg-blue-100 text-blue-700";
                    $icon = "fa-wand-magic-sparkles";
                }
            ?>
                <li class="p-4 hover:bg-gray-50 transition flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="mr-4 text-2xl <?= $colorClass ?>">
                            <i class="fa-solid <?= $icon ?>"></i>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-800 flex items-center">
                                <?= htmlspecialchars($doc->titulo ?? 'Sem título'); ?>
                                <span class="ml-2 text-[10px] <?= $labelClass ?> px-2 py-0.5 rounded uppercase font-bold tracking-wider">
                                    <?= $label ?>
                                </span>
                            </p>
                            <p class="text-sm text-gray-500 italic">
                                Modificado em: <?= date('d/m/Y H:i', strtotime($doc->updated_at)); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="<?= $editUrl; ?>" class="font-bold <?= $colorClass ?> hover:underline" target="_blank">
                            Abrir Editor
                        </a>

                        <button onclick="openModalRename(<?= $doc->id ?>, '<?= htmlspecialchars($doc->titulo) ?>')" class="text-gray-400 hover:text-blue-600 transition" title="Renomear">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        
                        <form action="<?= BASE_URL ?>/document/delete/<?= $doc->id; ?>" method="POST" onsubmit="return confirm('Excluir permanentemente?');" class="inline">
                            <button type="submit" class="text-gray-300 hover:text-red-600 transition" title="Excluir">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="p-12 text-center text-gray-400 italic">
                <i class="fa-solid fa-folder-open text-4xl mb-3 block opacity-20"></i>
                Nenhum arquivo encontrado. Comece criando um novo acima!
            </li>
        <?php endif; ?>
    </ul>
</div>

<div id="modalCreate" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[999] backdrop-blur-sm">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md transform transition-all">
        <h3 id="modalTitle" class="text-2xl font-bold mb-1 text-gray-800">Novo Arquivo</h3>
        <p class="text-gray-500 text-sm mb-6">Dê um nome para começar a editar online.</p>
        
        <form id="formCreate" method="POST">
            <div class="mb-6">
                <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-widest">Título do Documento</label>
                <input type="text" name="titulo" id="inputTitulo" 
                       class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-purple-500 focus:ring-0 outline-none transition-colors bg-gray-50" 
                       placeholder="Ex: Minha Nova Planilha" required>
            </div>
            
            <div class="flex justify-end items-center space-x-3">
                <button type="button" onclick="closeModalCreate()" class="px-5 py-2.5 text-gray-400 hover:text-gray-600 font-semibold transition">Cancelar</button>
                <button type="submit" id="btnSubmitCreate" class="px-6 py-2.5 bg-purple-600 text-white rounded-xl font-bold shadow-lg hover:scale-105 transition-transform">
                    Criar e Editar
                </button>
            </div>
        </form>
    </div>
</div>
