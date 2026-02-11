<?php
/**
 * @var object|null $document
 * @var object $currentTemplate
 * @var array $availableTemplates
 */
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/editor/canvas-editor.css">

<div class="flex items-center justify-between px-2 py-1 bg-white border-b sticky top-0 z-30">

    <div class="flex items-center flex-grow max-w-2xl">
        <div class="p-2 mr-1">
            <i class="fa-solid fa-file-lines text-blue-600 text-2xl"></i>
        </div>
        
        <div class="flex flex-col flex-grow">
            <input type="text"
                   name="titulo"
                   form="documentForm"
                   required
                   placeholder="Documento sem título"
                   value="<?= htmlspecialchars($document->titulo ?? '', ENT_QUOTES) ?>"
                   class="w-full text-lg font-medium bg-transparent border-none focus:ring-1 focus:ring-blue-300 rounded px-2 py-0 outline-none transition-all">
            
            <div class="flex gap-3 text-xs text-gray-500 px-2 mt-[-4px]">
                <span class="hover:bg-gray-100 cursor-pointer px-1 rounded">Arquivo</span>
                <span class="hover:bg-gray-100 cursor-pointer px-1 rounded">Editar</span>
                <span class="hover:bg-gray-100 cursor-pointer px-1 rounded">Ver</span>
                <span class="text-gray-300">|</span>
                <span class="font-semibold text-blue-600">Modelo: <?= htmlspecialchars($currentTemplate->nome) ?></span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 pr-4">
        <select name="template_id" 
                form="documentForm"
                class="border-none bg-gray-100 hover:bg-gray-200 text-xs rounded-full px-4 py-1.5 focus:ring-0 cursor-pointer">
            <?php foreach ($availableTemplates as $tpl): ?>
                <option value="<?= (int)$tpl->id ?>" <?= $tpl->id == $currentTemplate->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tpl->nome) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"
                form="documentForm"
                class="flex items-center gap-2 px-5 py-1.5 rounded-full bg-blue-600 text-white hover:bg-blue-700 font-semibold text-sm transition-all shadow-sm">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            Salvar
        </button>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/document/save" id="documentForm">
    <?php if ($document): ?>
        <input type="hidden" name="id" value="<?= (int)$document->id ?>">
    <?php endif; ?>

    <input type="hidden" name="conteudo_json" id="conteudo_json">
    <input type="hidden" name="conteudo_html" id="conteudo_html">

    <div class="flex flex-col h-[calc(100vh-64px)] bg-gray-100">

        <div class="bg-[#f9fbfd] border-b py-1 px-2 flex flex-wrap items-center sticky top-0 z-20">
            <button type="button" onclick="editor.command.executeUndo()" class="tool" title="Desfazer (Ctrl+Z)">
                <i class="fa-solid fa-rotate-left"></i>
            </button>
            <button type="button" onclick="editor.command.executeRedo()" class="tool" title="Refazer (Ctrl+Y)">
                <i class="fa-solid fa-rotate-right"></i>
            </button>

            <span class="separator"></span>

            <button type="button" onclick="handleClipboard('copy')" class="tool" title="Copiar (Ctrl+C)">
                <i class="fa-solid fa-copy"></i>
            </button>
            <button type="button" onclick="handleClipboard('cut')" class="tool" title="Recortar (Ctrl+X)">
                <i class="fa-solid fa-scissors"></i>
            </button>
            <button type="button" onclick="handleClipboard('paste')" class="tool" title="Colar (Ctrl+V)">
                <i class="fa-solid fa-paste"></i>
            </button>

            <span class="separator"></span>

            <div class="flex items-center gap-1" title="Cor do Texto">
                <i class="fa-solid fa-font text-[10px] ml-1"></i>
                <input type="color" onchange="editor.command.executeColor(this.value)" class="w-6 h-6 border-none bg-transparent cursor-pointer">
            </div>
            <div class="flex items-center gap-1" title="Realce (Marca-texto)">
                <i class="fa-solid fa-highlighter text-[10px] ml-1"></i>
                <input type="color" value="#ffff00" onchange="editor.command.executeHighlight(this.value)" class="w-6 h-6 border-none bg-transparent cursor-pointer">
            </div>

            <span class="separator"></span>

            <select onchange="editor.command.executeFont(this.value)" class="bg-transparent hover:bg-gray-200 rounded px-2 py-1 text-sm outline-none border-none">
                <option value="Arial">Arial</option>
                <option value="Times New Roman">Times New Roman</option>
                <option value="Verdana">Verdana</option>
                <option value="Calibri">Calibri</option>
            </select>

            <span class="separator"></span>

            <select onchange="editor.command.executeSize(Number(this.value))" class="bg-transparent hover:bg-gray-200 rounded px-2 py-1 text-sm outline-none border-none">
                <option value="12">12</option>
                <option value="14">14</option>
                <option value="16">16</option>
                <option value="18">18</option>
                <option value="24">24</option>
            </select>

            <span class="separator"></span>

            <button type="button" onclick="editor.command.executeBold()" class="tool" title="Negrito">
                <i class="fa-solid fa-bold"></i>
            </button>
            <button type="button" onclick="editor.command.executeItalic()" class="tool" title="Itálico">
                <i class="fa-solid fa-italic"></i>
            </button>
            <button type="button" onclick="editor.command.executeUnderline()" class="tool" title="Sublinhado">
                <i class="fa-solid fa-underline"></i>
            </button>

            <span class="separator"></span>

            <button type="button" onmousedown="event.preventDefault()" onclick="window.changeAlign('left')" class="tool">
                <i class="fa-solid fa-align-left"></i>
            </button>
            <button type="button" onmousedown="event.preventDefault()" onclick="window.changeAlign('center')" class="tool">
                <i class="fa-solid fa-align-center"></i>
            </button>
            <button type="button" onmousedown="event.preventDefault()" onclick="window.changeAlign('right')" class="tool">
                <i class="fa-solid fa-align-right"></i>
            </button>

            <span class="separator"></span>

            <button type="button" onclick="editor.command.executeInsertTable(3,3)" class="tool">
                <i class="fa-solid fa-table"></i>
            </button>
            <button type="button" onclick="window.triggerImageUpload()" class="p-2 hover:bg-gray-100 rounded" title="Inserir Imagem">
                <i class="fa-solid fa-image text-gray-700"></i>
            </button>

            <span class="separator"></span>

            <button type="button" onclick="verificarOrtografia()" class="tool" title="Corretor" id="btnSpellcheck">
                <i class="fa-solid fa-spell-check text-blue-600"></i>
            </button>

            <span class="separator"></span>
            <button type="button" onmousedown="event.preventDefault()" onclick="toggleList('ul')" class="tool" title="Marcadores">
                <i class="fa-solid fa-list-ul"></i>
            </button>
            <button type="button" onmousedown="event.preventDefault()" onclick="toggleList('ol')" class="tool" title="Numeração">
                <i class="fa-solid fa-list-ol"></i>
            </button>

            <span class="separator"></span>

            <div class="flex items-center gap-1">
                <button type="button" class="tool" onclick="setZoom(-0.1)"><i class="fa-solid fa-minus text-[10px]"></i></button>
                <select id="zoomSelect" class="bg-transparent text-xs outline-none border-none" onchange="setZoom(this.value, true)">
                    <option value="0.75">75%</option>
                    <option value="1" selected>100%</option>
                    <option value="1.25">125%</option>
                    <option value="1.5">150%</option>
                </select>
                <button type="button" class="tool" onclick="setZoom(0.1)"><i class="fa-solid fa-plus text-[10px]"></i></button>
            </div>
        </div>

        <div class="flex-grow relative overflow-hidden">
            <div id="scrollContainer" class="custom-scrollbar">
                <div id="editorZoomWrapper">
                    <div id="customRuler"></div>
                    <div id="editorMount"></div>
                </div>
            </div>
        </div>

        <!-- <div class="flex-grow flex overflow-hidden">
            <div id="outlineSidebar" class="w-64 bg-white border-r flex flex-col shadow-inner" style="min-width: 256px;">
                <div class="p-3 border-b bg-gray-50 flex justify-between items-center">
                    <span class="text-xs font-bold uppercase text-gray-600 tracking-wider">Navegação / Seções</span>
                    <button type="button" onclick="addManualMarker()" class="text-blue-600 hover:text-blue-800 text-xs font-bold" title="Marcar posição atual">
                        <i class="fa-solid fa-plus-circle"></i> MARCAR
                    </button>
                </div>
                <div id="outlineContent" class="flex-grow overflow-y-auto p-2 custom-scrollbar">
                    </div>
            </div>

            <div id="scrollContainer" class="flex-grow custom-scrollbar relative bg-gray-100 overflow-y-auto">
                <div id="editorZoomWrapper">
                    <div id="customRuler"></div>
                    <div id="editorMount"></div>
                </div>
            </div>
        </div> -->

        <div class="bg-white border-t p-1 flex justify-between items-center text-[10px] text-gray-500 px-6">
            <div class="flex gap-6">
                <span id="pageInfo"><i class="fa-regular fa-file mr-1"></i>Página: 1 / 1</span>
                <span id="wordCount"><i class="fa-solid fa-pen-nib mr-1"></i>Palavras: 0</span>
            </div>
            <div class="flex items-center gap-4">
                <span id="zoomLabel" class="font-bold uppercase tracking-widest text-blue-600">100%</span>
            </div>
        </div>
    </div>
    <input type="file" id="imageInput" accept="image/*" style="display:none;">
</form>

<div id="spellModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
        <div class="bg-blue-600 p-4 flex justify-between items-center">
            <h3 class="text-white font-bold"><i class="fa-solid fa-spell-check mr-2"></i>Correções Sugeridas</h3>
            <button onclick="closeSpellModal()" class="text-white hover:text-gray-200">&times;</button>
        </div>
        <div id="spellResults" class="p-4 max-h-96 overflow-y-auto">
            </div>
        <div class="p-4 bg-gray-50 border-t text-right">
            <button onclick="closeSpellModal()" class="px-4 py-1 bg-gray-300 rounded-lg text-sm">Fechar</button>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 12px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { 
    background: #c1c1c1; 
    border: 3px solid #f1f1f1; 
    border-radius: 10px; 
}
.custom-scrollbar {
    overscroll-behavior: contain; /* Impede que a rolagem do editor afete o resto da página de forma brusca */
    -webkit-overflow-scrolling: touch;
}

.tool {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: #444;
}
.tool:hover { background: #e8eaed; }
.separator {
    width: 1px;
    height: 20px;
    background: #dadce0;
    margin: 0 4px;
}

#editorMount {
    position: relative;
    background: white;
    box-shadow: 0 1px 3px rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
    margin: 0 auto; /* Reforça a centralização */
}

.separator {
    width: 1px;
    height: 24px;
    background: #d1d5db;
    margin: 0 4px;
}

#editorZoomWrapper {
    padding: 40px; 
    display: flex;
    flex-direction: column;
    align-items: center;
    width: fit-content; /* Importante para o flex do pai funcionar */
    min-width: 100%;    /* Garante que em zooms pequenos ele não cole na esquerda */
}

#scrollContainer {
    height: calc(100vh - 110px); /* Ajuste para descontar header e footer do editor */
    /* overflow: auto; */
    overflow-x: auto;  
    overflow-y: auto; 
    display: flex;
    flex-direction: column;
    /* align-items: center; */
    background-color: #f3f4f6;
    scroll-behavior: auto 
}

#scrollContainer::-webkit-scrollbar {
    height: 12px;
}

#scrollContainer::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 8px;
}

#scrollContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.overflow-y-auto {
    position: relative;
}

/* Estilização básica para o canvas interno do editor */
canvas {
    display: block;
}

.zoom-btn {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: background 0.2s;
}
.zoom-btn:hover {
    background-color: #e2e8f0;
    color: #1e40af;
}
/* Garante que o viewport ocupe o espaço disponível menos as barras */
.flex-grow {
    flex: 1 1 0%;
}

.canvas-editor-ruler {
    position: sticky;
    top: 0;
    z-index: 15;
    background: #f9fafb;
}

#customRuler {
    height: 20px;
    width: 100%;
    max-width: <?= (float)($currentTemplate->largura_papel ?? 21) ?>cm; /* Opcional: limita ao tamanho da folha */
    background: repeating-linear-gradient(
        to right,
        #fcfcfcff,
        #4d4d4dff 1px,
        transparent 1px,
        transparent 37.795px /* 1cm */
    );
    margin-bottom: 4px;
    z-index: 30;
    pointer-events: none;
}


#outlineSidebar {
    flex-shrink: 0; /* Impede que a sidebar mude de largura e force o canvas a relayout */
    user-select: none; /* Evita que cliques na sidebar iniciem seleção de texto no editor */
}

.outline-item {
    display: block;
    width: 100%;
    text-align: left;
    padding: 8px 12px;
    margin-bottom: 4px;
    border-radius: 6px;
    font-size: 13px;
    color: #4b5563;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.outline-item:hover {
    background-color: #eff6ff;
    color: #2563eb;
    border-color: #dbeafe;
}
.outline-item i {
    margin-right: 8px;
    width: 14px;
    text-align: center;
}

</style>

<script type="module">
import { Editor } from "<?= BASE_URL ?>/assets/editor/canvas-editor.es.js";

// --- VARIÁVEIS GLOBAIS ---
let editor;
let currentZoom = 1;
let manualMarkers = []; // Mantido globalmente para persistir durante a sessão

// --- CONFIGURAÇÃO INICIAL ---
const cmToPx = (cm) => Math.round(cm * 37.795);
const templateConfig = {
    width: cmToPx(<?= (float)($currentTemplate->largura_papel ?? 21) ?>),
    height: cmToPx(<?= (float)($currentTemplate->altura_papel ?? 29.7) ?>),
    margins: [
        cmToPx(<?= (float)($currentTemplate->margem_superior ?? 3) ?>),
        cmToPx(<?= (float)($currentTemplate->margem_direita ?? 2) ?>),
        cmToPx(<?= (float)($currentTemplate->margem_inferior ?? 2) ?>),
        cmToPx(<?= (float)($currentTemplate->margem_esquerda ?? 3) ?>)
    ],
    fontFamily: '<?= !empty($currentTemplate->fonte_familia) ? $currentTemplate->fonte_familia : "Arial" ?>',
    fontSize: <?= (int)($currentTemplate->fonte_tamanho ?? 12) ?>,
};

document.addEventListener('DOMContentLoaded', () => {
    const mount = document.getElementById('editorMount');
    if (!mount) return;

    const rawSavedData = <?php echo !empty($document->conteudo_json) ? $document->conteudo_json : 'null'; ?>;
    let initialData = rawSavedData?.data ? rawSavedData.data : (rawSavedData || { "main": [{ "value": "\n", "size": 12, "font": "Arial" }] });

    const options = {
        pageMode: 'page',
        renderMode: 'edit',
        paperDirection: 'vertical',
        width: templateConfig.width,
        height: templateConfig.height,
        margins: templateConfig.margins,
        ruler: true,
        readonly: false,
        defaultFont: templateConfig.fontFamily,
        defaultSize: templateConfig.fontSize
    };

    try {
        editor = new Editor(mount, initialData, options);
        window.editor = editor;

        // --- LISTENERS DE STATUS E CONTEÚDO ---
        const updateStatus = () => {
            if (!window.editor?.draw?.getPageNo) return;
            requestAnimationFrame(() => {
                try {
                    const pageNo = editor.draw.getPageNo(); 
                    const pageCount = editor.draw.getPageContainer().length;
                    document.getElementById('pageInfo').innerText = `Página: ${pageNo + 1} / ${pageCount}`;

                    const data = editor.command.getValue();
                    const mainContent = data.data ? data.data.main : data.main;
                    const text = mainContent.map(i => i.value).join('');
                    const wordCount = text.trim().split(/\s+/).length;
                    document.getElementById('wordCount').innerText = `Palavras: ${wordCount}`;
                } catch (e) {}
            });
        };

        let saveTimeout;

        editor.listener.contentChange = () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const fullData = editor.command.getValue();
                document.getElementById('conteudo_json').value = JSON.stringify(fullData);
            }, 500);
        };

        editor.listener.pageNoChange = updateStatus;
        editor.listener.intersectionPageNoChange = updateStatus;

        // Inicialização de UI
        window.renderOutline(); // Garante que a barra comece correta
        setTimeout(() => {
            window.setZoom(1, true);
            updateStatus();
        }, 200);

    } catch (e) {
        console.error('Erro ao instanciar Editor:', e);
    }
});

// --- FUNCIONALIDADES DE NAVEGAÇÃO (BARRA LATERAL) ---

window.addManualMarker = function() {
    if (!window.editor) return;
    const range = window.editor.command.getRange();
    const title = prompt("Nome da seção (ex: Capa, Introdução):");
    
    if (title && title.trim() !== "") {
        manualMarkers.push({ title: title, index: range.startIndex });
        window.renderOutline();
    }
    window.editor.command.executeFocus();
};

// Substitua a função renderOutline por esta versão otimizada
window.renderOutline = function() {
    const sidebar = document.getElementById('outlineContent');
    if (!sidebar) return;
    sidebar.innerHTML = '';

    if (manualMarkers.length === 0) {
        sidebar.innerHTML = '<p class="text-[10px] text-gray-400 text-center mt-4">Sem marcadores.</p>';
        return;
    }

    manualMarkers.forEach((marker, i) => {
        const div = document.createElement('div');
        div.className = 'outline-item';
        div.style.cssText = "display: flex; justify-content: space-between; padding: 10px; background: white; border: 1px solid #e2e8f0; margin-bottom: 5px; cursor: pointer; border-radius: 4px; font-size: 13px;";
        
        div.innerHTML = `
            <span><i class="fa-solid fa-location-dot text-blue-500 mr-2"></i> ${marker.title}</span>
            <i class="fa-solid fa-trash text-gray-300 hover:text-red-500" data-index="${i}"></i>
        `;

        // Previne o "pulo" ao manter o foco no canvas
        div.onmousedown = (e) => {
            e.preventDefault(); 
            
            if (e.target.classList.contains('fa-trash')) {
                const idx = e.target.getAttribute('data-index');
                manualMarkers.splice(idx, 1);
                window.renderOutline();
                return;
            }

            // Move o cursor e foca sem rolar a página inteira
            window.editor.command.executeFocus();
            window.editor.command.executeRange(marker.index, marker.index);
        };

        sidebar.appendChild(div);
    });
};

window.removeMarker = function(e, index) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    manualMarkers.splice(index, 1);
    window.renderOutline();
};

// --- TODAS AS FUNCIONALIDADES ORIGINAIS MANTIDAS ---

window.setZoom = function (value, absolute = false) {
    if (!window.editor) return;
    currentZoom = absolute ? parseFloat(value) : currentZoom + value;
    currentZoom = Math.min(Math.max(currentZoom, 0.5), 2.5);

    try {
        // editor.command.executePageScale(currentZoom);
        const wrapper = document.getElementById('editorZoomWrapper');
        wrapper.style.transform = `scale(${currentZoom})`;
        wrapper.style.transformOrigin = 'top center';

        const ruler = document.getElementById('customRuler');
        if (ruler) {
            ruler.style.transform = `scaleX(${currentZoom})`;
            ruler.style.transformOrigin = 'left top';
        }
        document.getElementById('zoomLabel').innerText = Math.round(currentZoom * 100) + '%';
        document.getElementById('zoomSelect').value = currentZoom.toFixed(2);
    } catch (e) {}
};

window.changeAlign = (type) => window.editor?.command.executeRowFlex(type);
window.changeSize = (val) => {
    // window.editor.command.executeFocus();
    window.editor.command.executeSize(Number(val));
};
window.toggleList = (type) => {
    window.editor.command.executeFocus();
    window.editor.command.executeList(null, type === 'ul' ? 'unorder' : 'order');
};

window.changeAlign = function (type) {
    if (!window.editor) return;
    window.editor.command.executeRowFlex(type);
};

window.changeSize = function(val) {
    window.editor.command.executeFocus();
    window.editor.command.executeSize(Number(val));
};

window.handleClipboard = function(action) {
    if (!window.editor) return;
    if (action === 'copy') editor.command.executeCopy();
    else if (action === 'cut') editor.command.executeCut();
    else if (action === 'paste') {
        navigator.clipboard.readText().then(text => editor.command.executePaste(text));
    }
};

window.toggleList = function(type) {
    window.editor.command.executeFocus();
    window.editor.command.executeList(null, type === 'ul' ? 'unorder' : 'order');
};

window.verificarOrtografia = async function() {
    const btn = document.getElementById('btnSpellcheck');
    const modal = document.getElementById('spellModal');
    const resultsContainer = document.getElementById('spellResults');
    
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    // 1. Capturar o texto atual
    const data = editor.command.getValue();
    const mainContent = data.data ? data.data.main : data.main;
    const fullText = mainContent.map(i => i.value).join('');

    try {
        const response = await fetch('https://api.languagetool.org/v2/check', {
            method: 'POST',
            body: new URLSearchParams({ 'text': fullText, 'language': 'pt-BR' })
        });
        const result = await response.json();
        
        resultsContainer.innerHTML = ''; // Limpa resultados anteriores

        if (result.matches.length === 0) {
            resultsContainer.innerHTML = '<p class="text-center text-green-600 p-4">Nenhum erro encontrado!</p>';
        } else {
            result.matches.forEach(match => {
                const word = fullText.substring(match.offset, match.offset + match.length);
                const div = document.createElement('div');
                div.className = 'mb-4 p-3 border rounded hover:bg-gray-50';
                
                let suggestions = match.replacements.slice(0, 3).map(s => 
                    `<button onclick="aplicarCorrecao('${word}', '${s.value}')" 
                             class="mr-2 mb-2 px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-xs font-semibold">
                        ${s.value}
                    </button>`
                ).join('');

                div.innerHTML = `
                    <p class="text-sm text-gray-500 mb-1">Palavra: <b class="text-red-600">${word}</b></p>
                    <p class="text-sm font-medium mb-2">${match.message}</p>
                    <div class="flex flex-wrap">${suggestions}</div>
                `;
                resultsContainer.appendChild(div);
            });
        }
        modal.classList.remove('hidden');
    } catch (e) {
        alert("Erro ao conectar ao corretor.");
    } finally {
        btn.innerHTML = '<i class="fa-solid fa-spell-check text-blue-600"></i>';
    }
};

// Gerenciador de Área de Transferência
window.handleClipboard = function(action) {
    if (!window.editor) return;
    
    switch(action) {
        case 'copy':
            editor.command.executeCopy();
            break;
        case 'cut':
            editor.command.executeCut();
            break;
        case 'paste':
            // Nota: Por segurança dos navegadores, o comando de colar via botão 
            // pode exigir permissão ou funcionar apenas via Ctrl+V em alguns contextos.
            navigator.clipboard.readText().then(text => {
                editor.command.executePaste(text);
            }).catch(err => {
                console.error('Erro ao colar: ', err);
                editor.command.executePaste(); // Tentativa nativa do motor
            });
            break;
    }
};

window.closeSpellModal = () => document.getElementById('spellModal').classList.add('hidden');

// Função para substituir a palavra no editor
window.aplicarCorrecao = function(antiga, nova) {
    // 1. O editor precisa encontrar e selecionar a palavra antes de substituir
    // Usamos o comando de busca para localizar o termo
    window.editor.command.executeSearch(antiga);
    
    // 2. Com a palavra selecionada pelo search, aplicamos a substituição
    window.editor.command.executeReplace(nova);
    
    console.log(`Sucesso: ${antiga} trocado por ${nova}`);
    closeSpellModal();
};

window.toggleList = function(type) {
    if (!window.editor) return;
    window.editor.command.executeFocus();
    
    if (type === 'ul') {
        // 'unorder' para marcadores (bolinhas)
        window.editor.command.executeList(null, 'unorder');
    } else {
        // 'order' para numeração sequencial
        window.editor.command.executeList(null, 'order');
    }
};

window.aplicarCorrecao = function(antiga, nova) {
    window.editor.command.executeSearch(antiga);
    window.editor.command.executeReplace(nova);
    document.getElementById('spellModal').classList.add('hidden');
};

// --- GESTÃO DE IMAGENS ---

// window.triggerImageUpload = () => document.getElementById('imageInput').click();
window.triggerImageUpload = function () {
    const input = document.getElementById('imageInput');
    input.value = '';
    input.click();
};

document.getElementById('imageInput').addEventListener('change', async e => {
    const file = e.target.files[0];
    if (!file || !window.editor) return;

    // window.editor.command.executeFocus();
    // const range = window.editor.command.getRange(); // Salva posição para evitar pulo

    const formData = new FormData();
    formData.append('image', file);

    try {
        const res = await fetch('<?= BASE_URL ?>/upload/image', { method: 'POST', body: formData });
        const result = await res.json();

        if (result && result.url) {
            const img = new Image();
            img.onload = function() {
                window.editor.command.executeFocus();
                const range = window.editor.command.getRange();
                
                // const maxWidth = 500;
                const pageWidth = templateConfig.width;
                const margins = templateConfig.margins[1] + templateConfig.margins[3];
                const maxWidth = pageWidth - margins;

                let width = img.width;
                let height = img.height;

                if (width > maxWidth) {
                    const ratio = maxWidth / width;
                    width = maxWidth;
                    height = height * ratio;
                }
                window.editor.command.executeImage({ value: result.url, width: width, height: height });
            };
            img.src = result.url;
        }
    } catch (err) { console.error('Erro no upload:', err); }
    e.target.value = '';
});

// Colar imagem (Ctrl+V)
// document.addEventListener('paste', (e) => {
//     const items = (e.clipboardData || e.originalEvent.clipboardData).items;
//     for (const item of items) {
//         if (item.type.indexOf('image') !== -1) {
//             const blob = item.getAsFile();
//             const reader = new FileReader();
//             reader.onload = (event) => window.editor.command.executeImage(event.target.result);
//             reader.readAsDataURL(blob);
//             e.preventDefault();
//         }
//     }
// });
document.addEventListener('paste', () => {

});

// Salvamento final
document.getElementById('documentForm').addEventListener('submit', function() {
    if (!window.editor) return;
    const fullValue = editor.command.getValue();
    document.getElementById('conteudo_json').value = JSON.stringify(fullValue);
    const htmlResult = editor.command.getHTML();
    document.getElementById('conteudo_html').value = typeof htmlResult === 'object' ? htmlResult.data : htmlResult;
});

</script>