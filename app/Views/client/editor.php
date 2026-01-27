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
            <button type="button" onclick="editor.command.executeImage()" class="tool">
                <i class="fa-solid fa-image"></i>
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
    background: white;
    box-shadow: 0 1px 3px rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
    margin: 0 auto; /* Garante que o canvas fique no centro do Wrapper */
}

.separator {
    width: 1px;
    height: 24px;
    background: #d1d5db;
    margin: 0 4px;
}

#editorZoomWrapper {
    display: block; 
    margin: 0 auto; /* Centraliza horizontalmente */
    padding: 0; /* Reduzi o padding para aproveitar mais a área */
    width: fit-content;
    transform-origin: top center;
    /* Remova a largura dinâmica via JS para evitar saltos */
}

#scrollContainer {
    height: calc(100vh - 120px); /* Ajuste preciso para caber entre header e footer */
    overflow-y: overlay !important; /* Overlay evita que a barra de scroll "empurre" o conteúdo */
    overflow-x: auto;
    position: relative;
    display: block;
    background-color: #f3f4f6;
    scroll-behavior: auto !important;
}

.overflow-y-auto {
    position: relative;
    scroll-behavior: auto !important; /* Desativa scroll suave que causa saltos no cursor */
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
.flex-grow.overflow-y-auto {
    position: relative;
    /* Forçamos o tamanho total para evitar que o flexbox tente recalcular */
    height: 100%; 
    width: 100%;
    overflow: visible; /* O pai não rola, quem rola é o container interno */
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
    background: repeating-linear-gradient(
        to right,
        #fcfcfcff,
        #4d4d4dff 1px,
        transparent 1px,
        transparent 37.795px /* 1cm */
    );
    position: sticky;
    top: 0;
    z-index: 30;
    pointer-events: none;
}


</style>

<script type="module">
import { Editor } from "<?= BASE_URL ?>/assets/editor/canvas-editor.es.js";

let editor;
let currentZoom = 1;

// Conversão de CM para PX (Fator 37.795)
const cmToPx = (cm) => Math.round(cm * 37.795);

const templateConfig = {
    // Dimensões da folha
    width: cmToPx(<?= (float)($currentTemplate->largura_papel ?? 21) ?>),
    height: cmToPx(<?= (float)($currentTemplate->altura_papel ?? 29.7) ?>),
    
    // Margens [superior, direita, inferior, esquerda]
    margins: [
        cmToPx(<?= (float)($currentTemplate->margem_superior ?? 3) ?>),
        cmToPx(<?= (float)($currentTemplate->margem_direita ?? 2) ?>),
        cmToPx(<?= (float)($currentTemplate->margem_inferior ?? 2) ?>),
        cmToPx(<?= (float)($currentTemplate->margem_esquerda ?? 3) ?>)
    ],
    
    // Configurações de texto inicial
    fontFamily: '<?= $currentTemplate->fonte_familia ?? "Arial" ?>',
    fontSize: <?= (int)($currentTemplate->fonte_tamanho ?? 12) ?>
};

document.addEventListener('DOMContentLoaded', () => {
    const mount = document.getElementById('editorMount');
    if (!mount) return;

    const rawSavedData = <?php echo !empty($document->conteudo_json) ? $document->conteudo_json : 'null'; ?>;
    
    let initialData;
    if (rawSavedData) {
        initialData = rawSavedData.data ? rawSavedData.data : rawSavedData;
    } else {
        initialData = {
            "main": [
                {
                    "value": "Documento pronto para edição.\n",
                    "size": 12,
                    "font": "Arial"
                }
            ]
        };
    }

    const options = {
        pageMode: 'page',
        renderMode: 'edit',
        paperDirection: 'vertical',
        width: templateConfig.width,   // Aplicando largura do banco
        height: templateConfig.height, // Aplicando altura do banco
        margins: templateConfig.margins, // Aplicando as 4 margens
        ruler: true,
        readonly: false,
        defaultFont: templateConfig.fontFamily,
        defaultSize: templateConfig.fontSize
    };

    try {
        editor = new Editor(mount, initialData, options);
        window.editor = editor;

        // --- FUNÇÃO PARA ATUALIZAR A BARRA DE STATUS ---
        const updateStatus = () => {
        if (!window.editor || !editor.draw || !editor.draw.getPageNo) return;

        requestAnimationFrame(() => {
            try {
                const pageNo = editor.draw.getPageNo(); 
                const pageCount = editor.draw.getPageContainer().length;
                
                const pageInfoEl = document.getElementById('pageInfo');
                if (pageInfoEl) pageInfoEl.innerText = `Página: ${pageNo + 1} / ${pageCount}`;

                // Contagem de palavras simplificada para não travar a UI
                const data = editor.command.getValue();
                const mainContent = data.data ? data.data.main : data.main;
                const text = mainContent.map(i => i.value).join('');
                const wordCount = text.trim().split(/\s+/).length;
                
                const wordCountEl = document.getElementById('wordCount');
                if (wordCountEl) wordCountEl.innerText = `Palavras: ${wordCount}`;
            } catch (e) {}
        });
    };

        // --- REGISTRAR OS LISTENERS ---
        
        let statusTimeout;
        const debouncedUpdate = () => {
            clearTimeout(statusTimeout);
            statusTimeout = setTimeout(updateStatus, 150); // Aguarda o usuário parar de digitar por 150ms
        };

        let saveTimeout;
        editor.listener.contentChange = () => {
            // 1. Limpa o cronômetro anterior
            clearTimeout(saveTimeout);
            
            // 2. Só atualiza o campo oculto após 500ms de silêncio (parada de digitação)
            saveTimeout = setTimeout(() => {
                const fullData = editor.command.getValue();
                document.getElementById('conteudo_json').value = JSON.stringify(fullData);
                
                // Chama o status de forma leve
                updateStatus();
            }, 500);
        };

        editor.listener.pageNoChange = updateStatus;
        editor.listener.intersectionPageNoChange = updateStatus;

        setTimeout(updateStatus, 500);

    } catch (e) {
        console.error('Erro ao instanciar Editor:', e);
    }

    setTimeout(() => {
        window.setZoom(1, true);
    }, 200);


    // Zoom com Ctrl + Scroll
    const viewport = document.querySelector('.overflow-auto');
    if (viewport) {
        viewport.addEventListener('wheel', (e) => {
            if (e.ctrlKey) {
                e.preventDefault();

                if (e.deltaY < 0) {
                    window.setZoom(0.1);
                } else {
                    window.setZoom(-0.1);
                }
            }
        }, { passive: false });
    }

});

// window.setZoom = function (value, absolute = false) {
//     if (!window.editor) return;

//     if (absolute) {
//         currentZoom = parseFloat(value);
//     } else {
//         currentZoom += value;
//     }

//     currentZoom = Math.min(Math.max(currentZoom, 0.5), 2.5);

//     // 1. Tenta usar o Zoom nativo do motor (isso evita 99% dos pulos)
//     try {
//         window.editor.command.executePageScale(currentZoom);
//     } catch (e) {
//         // 2. Fallback: Se o comando acima não existir, usamos CSS mas sem alterar o Width
//         const zoomWrapper = document.getElementById('editorZoomWrapper');
//         if (zoomWrapper) {
//             zoomWrapper.style.transform = `scale(${currentZoom})`;
//             // Não altere o zoomWrapper.style.width aqui, isso causa o erro da margem direita
//         }
//     }

//     // Atualiza UI
//     const zoomPercent = Math.round(currentZoom * 100) + '%';
//     if (document.getElementById('zoomLabel')) document.getElementById('zoomLabel').innerText = zoomPercent;
//     if (document.getElementById('zoomSelect')) document.getElementById('zoomSelect').value = currentZoom.toFixed(1);
// };
window.setZoom = function (value, absolute = false) {
    if (!window.editor) return;

    if (absolute) {
        currentZoom = parseFloat(value);
    } else {
        currentZoom += value;
    }

    currentZoom = Math.min(Math.max(currentZoom, 0.5), 2.5);

    // Zoom nativo do editor
    try {
        editor.command.executePageScale(currentZoom);
    } catch (e) {}

    // 🔴 SINCRONIZA A RÉGUA CUSTOMIZADA
    const ruler = document.getElementById('customRuler');
    if (ruler) {
        ruler.style.transform = `scaleX(${currentZoom})`;
        ruler.style.transformOrigin = 'left top';
    }

    // UI
    const zoomPercent = Math.round(currentZoom * 100) + '%';
    document.getElementById('zoomLabel').innerText = zoomPercent;
    document.getElementById('zoomSelect').value = currentZoom.toFixed(2);
};


window.changeAlign = function (type) {
    if (!window.editor) return;

    try {
        // O comando RowFlex é o mais estável para alinhar parágrafos nesta versão
        window.editor.command.executeRowFlex(type);
        
        // Se após alinhar ele ainda não "desenhar" na tela, force o update:
        if (window.editor.command.forceUpdate) {
            window.editor.command.forceUpdate();
        }
    } catch (e) {
        console.error("Erro ao aplicar alinhamento:", e);
    }
};

window.changeZoom = function (value) {
    const zoomValue = parseFloat(value);
    if (isNaN(zoomValue)) return;

    window.setZoom(zoomValue, true);
};

const zoomSelect = document.getElementById('zoomSelect');
if (zoomSelect) {
    zoomSelect.addEventListener('change', function () {
        window.changeZoom(this.value);
    });
}
const select = document.getElementById('zoomSelect');
if (select && document.activeElement !== select) {
    select.value = currentZoom.toFixed(1);
}

function ensureSelection() {
    editor.command.executeFocus();

    const range = editor.listener.range;
    if (!range) return;

    // se não houver seleção, seleciona o parágrafo atual
    if (range.startOffset === range.endOffset) {
        editor.command.executeSelectAll();
    }
}

window.changeSize = function(val) {
    editor.command.executeFocus();
    editor.command.executeSize(Number(val));
};

document.getElementById('documentForm').addEventListener('submit', function(e) {
    if (!window.editor) return;

    // 1. Salva o JSON (Estado estruturado)
    const fullValue = editor.command.getValue();
    document.getElementById('conteudo_json').value = JSON.stringify(fullValue);

    // 2. Salva o HTML (Layout processado)
    // O canvas-editor pode retornar um objeto. Precisamos garantir que pegamos a string.
    const htmlResult = editor.command.getHTML();
    console.log("Resultado do HTML:", htmlResult);
    
    let htmlString = "";
    
    // Verifica se o resultado é um objeto (que causa o [object Object])
    if (typeof htmlResult === 'object' && htmlResult !== null) {
        // Na maioria das versões do canvas-editor, o HTML final 
        // fica dentro de uma propriedade específica ou precisa ser mapeado
        htmlString = htmlResult.data || ""; 
    } else {
        htmlString = htmlResult;
    }

    document.getElementById('conteudo_html').value = htmlString;
});

// Listener para troca de template (opcional - requer recarregamento ou atualização de comando)
document.querySelector('select[name="template_id"]').addEventListener('change', function() {
 
    if (window.editor) {
        alert("O modelo foi alterado. Salve o documento para aplicar as novas margens de impressão.");
    }
});

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
</script>