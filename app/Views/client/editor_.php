<?php
/**
 * @var object|null $document
 * @var string $content
 * @var object $currentTemplate
 * @var array $availableTemplates
 */
?>

<h1 class="text-3xl font-bold mb-6">
    <?= $document ? 'Editar Documento' : 'Novo Documento' ?>
</h1>


<form method="POST" action="<?= BASE_URL ?>/document/save" id="documentForm">

    <?php if ($document): ?>
        <input type="hidden" name="id" value="<?= (int)$document->id ?>">
    <?php endif; ?>

    <!-- ================= CAPA ================= -->
    <section class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">Informações do Documento</h2>

        <div class="grid grid-cols-1 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Nome Documento</label>
                <input type="text" name="titulo"
                    value="<?= htmlspecialchars($document->titulo ?? '', ENT_QUOTES) ?>"
                    required class="w-full border rounded p-2">
            </div>

            <!-- <div>
                <label class="block text-sm font-semibold mb-1">Subtítulo</label>
                <input type="text" name="subtitulo"
                    value="<?= htmlspecialchars($document->subtitulo ?? '', ENT_QUOTES) ?>"
                    class="w-full border rounded p-2">
            </div> -->
        </div>

        <!-- <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Autor</label>
                <input type="text" name="autor"
                    value="<?= htmlspecialchars($document->autor ?? '', ENT_QUOTES) ?>"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Instituição</label>
                <input type="text" name="instituicao"
                    value="<?= htmlspecialchars($document->instituicao ?? '', ENT_QUOTES) ?>"
                    class="w-full border rounded p-2">
            </div>
        </div> -->

        <!-- <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Local</label>
                <input type="text" name="local_publicacao"
                    value="<?= htmlspecialchars($document->local_publicacao ?? '', ENT_QUOTES) ?>"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Ano</label>
                <input type="number" name="ano_publicacao"
                    value="<?= htmlspecialchars($document->ano_publicacao ?? date('Y'), ENT_QUOTES) ?>"
                    class="w-full border rounded p-2">
            </div>
        </div> -->
    </section>

    <!-- ================= TEMPLATE ================= -->
    <div class="mb-6">
        <label class="block text-sm font-semibold mb-1">Template</label>

        <div class="flex gap-3">
            <select name="template_id"
                    id="templateSelector"
                    class="border rounded p-2 flex-1">
                <?php foreach ($availableTemplates as $tpl): ?>
                    <option value="<?= (int)$tpl->id ?>"
                        <?= $tpl->id == $currentTemplate->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tpl->nome, ENT_QUOTES) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="button"
                    id="applyTemplateBtn"
                    class="bg-gray-800 text-white px-4 rounded hover:bg-gray-900">
                Aplicar
            </button>
        </div>
    </div>

    <div class="mb-3 flex justify-end">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" id="toggleThumbnails">
            Mostrar miniaturas
        </label>
    </div>

    <!-- ================= EDITOR ================= -->
    <section class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-xl font-semibold mb-4">Conteúdo do Documento</h2>
        <div class="editor-container">
            <style id="template-editor-style">
                <?= $editorCss ?>
            </style>
            <div id="editor-page" class="bg-white shadow mx-auto">
                <!-- <div id="editor"></div> -->
                <textarea id="editor" name="conteudo_html" spellcheck="true"><?= htmlspecialchars($content ?? '', ENT_QUOTES) ?></textarea>
            </div>
            
        </div>
    </section>
    <div id="pageNavigator"
        class="fixed left-4 top-24 w-36 space-y-3 overflow-y-auto max-h-[80vh]">
    </div>

    <!-- ================= AÇÕES ================= -->
    <div class="flex gap-4">
        <button type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Salvar
        </button>

        <?php if ($document): ?>
            <a href="<?= BASE_URL ?>/document/export/pdf/<?= (int)$document->id ?>"
                class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                Exportar PDF
            </a>
        <?php endif; ?>
    </div>

</form>

<!-- ================= CKEDITOR ================= -->
<style>
.editor-container {
    background: #ebebeb;
    padding: 40px 0;
    display: flex;
    justify-content: center;
}

.ck-editor {
    width: 210mm !important;
}

.ck-editor__editable_inline {
    position: relative;
    overflow: visible 
}

.ck-editor__top {
    position: sticky;
    top: 0;
    z-index: 100;
    border-bottom: 1px solid #ccced1;
}

/* Indicador de página (overlay) */
.page-indicator {
    position: fixed;
    right: 20px;
    bottom: 20px;
    background: #1f2937;
    color: #fff;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0,0,0,.25);
    z-index: 9999;
}
#pageNavigator button {
    display: block;
    width: 100%;
    padding: 6px 10px;
    border-radius: 6px;
    background: #f3f4f6;
    cursor: pointer;
    font-weight: 500;
}

#pageNavigator button:hover {
    background: #2563eb;
    color: white;
}

.ck-content .ck-page-break {
    display: block;
    border-top: 2px dashed #ef4444;
    margin: 40px 0;
    page-break-before: always;
    break-before: page;
}

.page-thumb {
    border: 1px solid #d1d5db;
    background: white;
    height: 140px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
}

.page-thumb-inner {
    transform: scale(0.18);
    transform-origin: top left;
    width: 210mm;
    min-height: 297mm;
    pointer-events: none;
}

.page-cut-line {
    position: absolute;
    left: 0;
    right: 0;
    height: 2px;
    background: repeating-linear-gradient(
        to right,
        #ef4444,
        #ef4444 6px,
        transparent 6px,
        transparent 12px
    );
    pointer-events: none;
    z-index: 10;
}

.ck-editor__main {
    position: relative;
}

</style>

<div id="pageIndicator" class="page-indicator">
    Página 1
</div>

<script src="<?= BASE_URL ?>/assets/ckeditor/ckeditor.js"></script>

<script>
let paginateTimeout = null;
let currentTemplateConfig = null;
let currentPageHeightPx = null;
let autoPaginateEnabled = false;
let thumbnailsEnabled = false;
let pageTopOffsetPx = 0; 
let ckEditorInstance = null;

function cmToPx(cm) {
    return cm * (96 / 2.54);
}

/* =========================
   DOM READY
========================= */
document.addEventListener('DOMContentLoaded', () => {

    const EditorClass =
        window.ClassicEditor ||
        window.Editor ||
        (window.ClassicEditor && window.ClassicEditor.default);

    if (!EditorClass) {
        console.error('CKEditor não encontrado.');
        return;
    }

    const textarea = document.getElementById('editor');
    if (!textarea) return;

    let editorInstance = null;
    let paginateTimeout = null;

    EditorClass.create(textarea, {
        extraPlugins: [
            WordLikeUploadPlugin
            // PageBreakRealPlugin
        ]
    })
    .then(editor => {
        editorInstance = editor;
        window.editorInstance = editor;

        const editable = editor.ui.getEditableElement();
        const container = editable.parentElement;

        <?php if (isset($currentTemplate)): ?>
            applyTemplate(<?= json_encode($currentTemplate) ?>);
        <?php endif; ?>

        autoPaginate(editor);
        renderThumbnails(editor);
        updatePageIndicator(editor);
        
        editable.addEventListener('scroll', () => {
            updatePageIndicator(editor);
        });

        editor.model.document.on('change:data', () => {
            clearTimeout(paginateTimeout);
            paginateTimeout = setTimeout(() => {
                renderPageCut(editor);
                renderThumbnails(editor);
                updatePageIndicator(editor);
            }, 200);
        });

        initPageNavigator(editor);
    })
    .catch(error => {
        console.error('Erro ao iniciar CKEditor:', error);
    });

    /* =========================
       TEMPLATE + DELIMITADOR
    ========================= */
    function applyTemplate(t) {
        if (!editorInstance) return;

        const editable = editorInstance.ui.getEditableElement();
        if (!editable) return;

        currentTemplateConfig = t;

        const paperHeightCm = t.altura_papel || 29.7;
        const marginTopCm   = t.margem_superior || 2.5;
        const marginBotCm   = t.margem_inferior || 2.5;

        const contentHeightCm =
            paperHeightCm - marginTopCm - marginBotCm;

        currentPageHeightPx = cmToPx(contentHeightCm);

        // 🔴 ESSENCIAL: offset real do topo útil
        pageTopOffsetPx = cmToPx(marginTopCm);

        /* === VISUAL === */
        editable.style.width         = (t.largura_papel || 21) + 'cm';
        editable.style.fontFamily    = t.fonte_familia || 'Times New Roman';
        editable.style.fontSize      = (t.fonte_tamanho || 12) + 'pt';
        editable.style.lineHeight    = t.entre_linhas || 1.5;
        editable.style.textAlign     = t.alinhamento || 'justify';

        editable.style.paddingTop    = marginTopCm + 'cm';
        editable.style.paddingBottom = marginBotCm + 'cm';
        editable.style.paddingLeft   = (t.margem_esquerda || 3) + 'cm';
        editable.style.paddingRight  = (t.margem_direita || 2) + 'cm';

        /* === FUNDO === */
        editable.style.backgroundImage = `
            repeating-linear-gradient(
                to bottom,
                transparent,
                transparent ${paperHeightCm}cm,
                #e5e7eb ${paperHeightCm}cm,
                #e5e7eb calc(${paperHeightCm}cm + 1px)
            )
        `;
        editable.style.backgroundSize = `100% ${paperHeightCm}cm`;

        requestAnimationFrame(() => {
            renderPageCut(editorInstance);
            renderThumbnails(editorInstance);
            updatePageIndicator(editorInstance);
        });
    }

    /* =========================
       INDICADOR DE PÁGINA ATUAL
    ========================= */
    function initPageNavigator(editor) {
        const nav = document.getElementById('pageNavigator');
        const editable = editor.ui.getEditableElement();
        if (!nav || !editable) return;

        function updatePages() {
            nav.innerHTML = '';

            const pageHeightPx = currentPageHeightPx;
            const totalHeight = editable.scrollHeight;
            const pages = Math.max(1, Math.ceil(totalHeight / pageHeightPx));

            if (pages <= 1) {
                nav.style.display = 'none';
                return;
            }

            // nav.style.display = 'block';

            for (let i = 0; i < pages; i++) {
                const thumb = document.createElement('div');
                thumb.className = 'page-thumb';

                const inner = document.createElement('div');
                inner.className = 'page-thumb-inner';

                inner.innerHTML = editable.innerHTML;
                inner.style.marginTop = `-${i * pageHeightPx}px`;

                thumb.appendChild(inner);

                thumb.onclick = () => {
                    editable.scrollTo({
                        top: i * pageHeightPx,
                        behavior: 'smooth'
                    });
                };

                nav.appendChild(thumb);
            }
        }

        updatePages();
    }

    /* =========================
       TROCA DE TEMPLATE
    ========================= */

    document.getElementById('applyTemplateBtn')?.addEventListener('click', () => {
        const select = document.getElementById('templateSelector');
        if (!select) return;

        const templateId = select.value;

        fetch(`<?= BASE_URL ?>/template/${templateId}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(tpl => {
            if (!tpl || !tpl.altura_papel) {
                throw new Error('Template inválido');
            }
            applyTemplate(tpl);
        })
        .catch(err => {
            console.error('Erro ao aplicar template:', err);
            alert('Não foi possível aplicar o template.');
        });
    });

});

/* =========================
   UPLOAD ADAPTER
========================= */
class WordLikeUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(file => {
            const data = new FormData();
            data.append('upload', file);

            return fetch('<?= BASE_URL ?>/upload/image', {
                method: 'POST',
                body: data
            })
            .then(r => r.json())
            .then(res => {
                if (!res.url) throw res.error || 'Erro no upload';
                return { default: res.url };
            });
        });
    }

    abort() {}
}

function WordLikeUploadPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter =
        loader => new WordLikeUploadAdapter(loader);
}

function autoPaginate(editor) {
    return; // paginação é 100% visual
}

function renderThumbnails(editor) {
    const nav = document.getElementById('pageNavigator');
    const editable = editor.ui.getEditableElement();
    if (!nav || !editable || !currentPageHeightPx) return;

    const totalHeight = editable.scrollHeight;
    const usableHeight = editable.scrollHeight;


    const totalPages = Math.max(
        1,
        Math.ceil(usableHeight / currentPageHeightPx)
    );

    if (!thumbnailsEnabled || totalPages <= 1) {
        nav.style.display = 'none';
        return;
    }

    // nav.style.display = 'block';
    nav.innerHTML = '';

    for (let i = 0; i < totalPages; i++) {
        const thumb = document.createElement('div');
        thumb.className = 'page-thumb';

        const inner = document.createElement('div');
        inner.className = 'page-thumb-inner';
        inner.innerHTML = editable.innerHTML;

        inner.style.transform = `
            scale(0.18)
            translateY(-${i * currentPageHeightPx}px)
        `;
        inner.style.transformOrigin = 'top left';

        thumb.appendChild(inner);
        thumb.onclick = () => {
            editable.scrollTo({
                top: i * currentPageHeightPx,
                behavior: 'smooth'
            });
        };

        nav.appendChild(thumb);
    }
}

function updatePageIndicator(editor) {
    const indicator = document.getElementById('pageIndicator');
    const editable = editor.ui.getEditableElement();
    if (!indicator || !currentPageHeightPx) return;

    const page =
        Math.floor(editable.scrollTop / currentPageHeightPx) + 1;

    indicator.textContent = `Página ${page}`;
}

const toggle = document.getElementById('toggleThumbnails');
const nav = document.getElementById('pageNavigator');

if (nav) {
    nav.style.display = 'none';
}

if (toggle) {
    thumbnailsEnabled = toggle.checked;

    toggle.addEventListener('change', () => {
        thumbnailsEnabled = toggle.checked;
        nav.style.display = thumbnailsEnabled ? 'block' : 'none';
    });
}

function renderPageCut(editor) {
    const editable = editor.ui.getEditableElement();
    if (!editable || !currentPageHeightPx) return;

    const container = editable.parentElement;
    if (!container) return;

    container.querySelectorAll('.page-cut-line').forEach(l => l.remove());

    const usableHeight = editable.scrollHeight;

    const totalPages = Math.max(
        1,
        Math.ceil(usableHeight / currentPageHeightPx)
    );

    const editableRect = editable.getBoundingClientRect();
    const containerRect = container.getBoundingClientRect();

    const baseOffset =
        editableRect.top - containerRect.top;

    for (let i = 1; i < totalPages; i++) {
        const line = document.createElement('div');
        line.className = 'page-cut-line';

        line.style.top = `${
            baseOffset +
            pageTopOffsetPx +
            i * currentPageHeightPx
        }px`;

        container.appendChild(line);
    }
}

function initEditor(templateId) {
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline',
                    '|',
                    'alignment',
                    '|',
                    'numberedList', 'bulletedList',
                    '|',
                    'undo', 'redo'
                ]
            }
        })
        .then(editor => {
            ckEditorInstance = editor;
            loadTemplateCss(templateId);
        })
        .catch(error => {
            console.error(error);
        });
}

function applyTemplateCssToEditor(editor, cssText) {
    const styleId = 'ckeditor-template-style';

    let styleEl = document.getElementById(styleId);
    if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = styleId;
        document.head.appendChild(styleEl);
    }

    styleEl.innerHTML = cssText;

    // força repaint do view root
    editor.editing.view.change(writer => {
        const root = editor.editing.view.document.getRoot();
        writer.setStyle('font-family', null, root);
    });
}


function loadTemplateCss(templateId) {
    // fetch(`/template/${templateId}/editor-css`)
    //     .then(response => response.text())
    //     .then(css => applyCssToEditor(css));
    fetch(`/template/<?= (int)$currentTemplate->id ?>/editor-css`)
        .then(r => r.text())
        .then(css => applyTemplateCssToEditor(editorInstance, css));

}

function applyCssToEditor(css) {
    if (!ckEditorInstance) return;

    const editable = ckEditorInstance.ui.getEditableElement();
    const doc = editable.ownerDocument;

    // Remove estilo anterior (troca de template)
    const oldStyle = doc.getElementById('template-style');
    if (oldStyle) {
        oldStyle.remove();
    }

    // Injeta novo CSS
    const style = doc.createElement('style');
    style.id = 'template-style';
    style.innerHTML = css;

    doc.head.appendChild(style);
}

</script>