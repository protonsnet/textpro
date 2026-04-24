<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight"><?= $title ?? 'Meus Documentos' ?></h2>
            <p class="text-sm text-gray-500 mt-1">Gerencie seus arquivos do editor clássico e documentos Office.</p>
        </div>
        
        <!-- <div class="flex space-x-2">
             <button onclick="openModalCreate('excel')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center shadow-sm">
                <i class="fa-solid fa-file-excel mr-2"></i> Planilha
            </button>
            <button onclick="openModalCreate('word')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition flex items-center shadow-sm">
                <i class="fa-solid fa-file-word mr-2"></i> Word
            </button>
        </div> -->
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-12 bg-gray-50 border-b border-gray-200 px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            <div class="col-span-6 md:col-span-7">Nome</div>
            <div class="col-span-3 md:col-span-2">Modificado</div>
            <div class="col-span-3 md:col-span-3 text-right">Ações</div>
        </div>

        <ul class="divide-y divide-gray-100">
            <?php if (!empty($documents)): ?>
                <?php foreach ($documents as $doc): 
                    $filePath = strtolower($doc->file_path ?? '');
                    
                    // Lógica de Identificação de Tipo e Estilo
                    if (str_contains($filePath, '.xlsx') || str_contains($filePath, '.xls')) {
                        $icon = 'fa-file-excel'; $colorClass = 'text-green-600'; $bgClass = 'bg-green-50'; $label = 'Planilha';
                        $editUrl = BASE_URL . "/editor-beta/spreadsheet/" . $doc->id;
                    } elseif (str_contains($filePath, '.pptx') || str_contains($filePath, '.ppt')) {
                        $icon = 'fa-file-powerpoint'; $colorClass = 'text-orange-600'; $bgClass = 'bg-orange-50'; $label = 'Apresentação';
                        $editUrl = BASE_URL . "/editor-beta/presentation/" . $doc->id;
                    } elseif (str_contains($filePath, '.docx') || str_contains($filePath, '.doc')) {
                        $icon = 'fa-file-word'; $colorClass = 'text-purple-600'; $bgClass = 'bg-purple-50'; $label = 'Documento';
                        $editUrl = BASE_URL . "/editor-beta/" . $doc->id;
                    } else {
                        // Formato Antigo (Canvas / Editor Interno)
                        $icon = 'fa-wand-magic-sparkles'; $colorClass = 'text-blue-600'; $bgClass = 'bg-blue-50'; $label = 'Canvas';
                        $editUrl = BASE_URL . "/editor/" . $doc->id;
                    }
                ?>
                    <li class="grid grid-cols-12 items-center px-6 py-3 hover:bg-blue-50/40 transition-colors border-b border-gray-100">
                        <div class="col-span-6 md:col-span-7">
                            <a href="<?= $editUrl ?>" target="_blank" class="flex items-center gap-3 group w-full">
                                <div class="w-9 h-9 flex-shrink-0 <?= $bgClass ?> <?= $colorClass ?> rounded flex items-center justify-center">
                                    <i class="fa-solid <?= $icon ?> text-lg"></i>
                                </div>
                                <div class="truncate">
                                    <span class="text-sm font-semibold text-gray-700 group-hover:text-blue-600 group-hover:underline truncate block">
                                        <?= htmlspecialchars($doc->titulo ?? 'Sem título') ?>
                                    </span>
                                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider"><?= $label ?></span>
                                </div>
                            </a>
                        </div>

                        <div class="col-span-3 md:col-span-2 text-xs text-gray-500 font-medium">
                            <?= date('d/m/Y', strtotime($doc->updated_at)) ?>
                        </div>

                        <div class="col-span-3 md:col-span-3 flex justify-end items-center gap-2">
                            <button onclick="uiShare(<?= $doc->id ?>, '<?= addslashes($doc->titulo) ?>')" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-full" title="Compartilhar">
                                <i class="fa-solid fa-share-nodes"></i>
                            </button>
                            
                            <button onclick="openModalRename(<?= $doc->id ?>, '<?= addslashes($doc->titulo) ?>')" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full" title="Renomear">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <form action="<?= BASE_URL ?>/document/delete/<?= $doc->id ?>" method="POST" onsubmit="return confirm('Excluir?');" class="inline">
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="py-20 text-center text-gray-500 italic">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 block opacity-20"></i>
                    Nenhum arquivo encontrado.
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div id="modalCreate" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-1">Novo Arquivo</h3>
            <p class="text-gray-500 text-sm mb-6">Dê um nome para o seu novo documento.</p>
            
            <form id="formCreate" method="POST">
                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-widest">Título do Documento</label>
                    <input type="text" name="titulo" id="inputTitulo" 
                           class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-purple-500 outline-none transition-all bg-gray-50 text-sm" 
                           placeholder="Ex: Relatório Mensal" required>
                </div>
                
                <div class="flex justify-end items-center gap-3">
                    <button type="button" onclick="closeModalCreate()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancelar</button>
                    <button type="submit" id="btnSubmitCreate" class="px-6 py-2 bg-purple-600 text-white rounded-lg font-bold shadow-md hover:bg-purple-700 transition-all text-sm">
                        Criar e Editar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="shareModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Compartilhar Arquivo</h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <p id="shareDocTitle" class="text-sm text-gray-500 mb-6 truncate font-medium"></p>
            
            <input type="hidden" id="shareDocId">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">E-mail do Usuário</label>
                    <input type="email" id="shareEmail" placeholder="exemplo@email.com" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="shareCanEdit" checked class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="shareCanEdit" class="text-sm text-gray-700">Permitir edição</label>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
            <button onclick="closeShareModal()" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancelar</button>
            <button onclick="submitShare()" id="btnSubmitShare" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg text-sm font-bold transition-all">
                Compartilhar
            </button>
        </div>
    </div>
</div>

<div id="renameModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Renomear Arquivo</h3>
        <input type="hidden" id="renameDocId">
        <input type="text" id="renameTitle" class="w-full px-4 py-2 border rounded-lg mb-4 outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        <div class="flex justify-end gap-3">
            <button onclick="document.getElementById('renameModal').classList.add('hidden')" class="text-gray-500 text-sm">Cancelar</button>
            <button onclick="submitRename()" id="btnSubmitRename" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Salvar</button>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('shareModal');
const inputId = document.getElementById('shareDocId');
const inputEmail = document.getElementById('shareEmail');
const inputEdit = document.getElementById('shareCanEdit');
const textTitle = document.getElementById('shareDocTitle');

// Abre o modal
function uiShare(id, title) {
    inputId.value = id;
    textTitle.innerText = title;
    inputEmail.value = '';
    modal.classList.remove('hidden');
    inputEmail.focus();
}

// Fecha o modal
function closeShareModal() {
    modal.classList.add('hidden');
}

// Envia os dados
async function submitShare() {
    const btn = document.getElementById('btnSubmitShare');
    const docId = inputId.value;
    const email = inputEmail.value.trim();
    const canEdit = inputEdit.checked ? 1 : 0;

    if (!email) {
        alert("Por favor, digite um e-mail válido.");
        return;
    }

    try {
        btn.disabled = true;
        btn.innerText = "Processando...";

        const response = await fetch('<?= BASE_URL ?>/editor-beta/share', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                'document_id': docId,
                'email': email,
                'can_edit': canEdit
            })
        });

        // Captura o texto bruto da resposta
        const text = await response.text();
        
        console.log("Status da Resposta:", response.status);
        console.log("Conteúdo Bruto:", text);

        if (!response.ok) {
            throw new Error(`Erro HTTP ${response.status}: ${text.substring(0, 100)}`);
        }

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            // Se cair aqui, o PHP soltou algum erro de texto antes do JSON
            throw new Error("O servidor retornou texto puro em vez de JSON. Resposta: " + text.substring(0, 200));
        }

        if (data.success) {
            alert("✅ " + data.success);
            closeShareModal();
        } else {
            alert("❌ " + (data.error || "Erro desconhecido informado pelo servidor."));
        }

    } catch (error) {
        console.error("Erro detalhado no Fetch:", error);
        // O alert agora mostrará a mensagem real do erro PHP se houver
        alert("Erro Crítico Detalhado:\n" + error.message);
    } finally {
        btn.disabled = false;
        btn.innerText = "Compartilhar";
    }
}

// Scripts de controle do Modal Create
function openModalCreate(type) {
    const modal = document.getElementById('modalCreate');
    const form = document.getElementById('formCreate');
    const title = document.getElementById('modalTitle');
    const btn = document.getElementById('btnSubmitCreate');

    if (type === 'excel') {
        title.innerText = "Nova Planilha Excel";
        form.action = "<?= BASE_URL ?>/editor-beta/create-xlsx";
        btn.className = "px-6 py-2 bg-green-600 text-white rounded-lg font-bold shadow-md hover:bg-green-700 transition-all text-sm";
    } else {
        title.innerText = "Novo Documento Word";
        form.action = "<?= BASE_URL ?>/editor-beta/create";
        btn.className = "px-6 py-2 bg-purple-600 text-white rounded-lg font-bold shadow-md hover:bg-purple-700 transition-all text-sm";
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('inputTitulo').focus();
}

function closeModalCreate() {
    const modal = document.getElementById('modalCreate');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openModalRename(id, currentTitle) {
    document.getElementById('renameDocId').value = id;
    document.getElementById('renameTitle').value = currentTitle;
    document.getElementById('renameModal').classList.remove('hidden');
}

async function submitRename() {
    const id = document.getElementById('renameDocId').value;
    const newTitle = document.getElementById('renameTitle').value;
    const btn = document.getElementById('btnSubmitRename');

    try {
        btn.disabled = true;
        const response = await fetch('<?= BASE_URL ?>/document/rename', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ 'id': id, 'titulo': newTitle })
        });
        
        const data = await response.json();
        if(data.success) {
            window.location.reload(); // Recarrega para mostrar o novo nome
        } else {
            alert(data.error || "Erro ao renomear");
        }
    } catch (e) {
        alert("Erro técnico ao renomear.");
    } finally {
        btn.disabled = false;
    }
}
</script>