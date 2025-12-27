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

    <!-- =========================
         TÍTULO
    ========================== -->
    <div class="mb-4">
        <label class="block text-sm font-semibold mb-1">Título</label>
        <input
            type="text"
            name="titulo"
            value="<?= htmlspecialchars($document->titulo ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
            class="w-full border rounded p-2"
        >
    </div>

    <!-- =========================
         TEMPLATE
    ========================== -->
    <div class="mb-4">
        <label class="block text-sm font-semibold mb-1">Template</label>
        <select
            name="template_id"
            id="templateSelector"
            class="border rounded p-2 w-full"
        >
            <?php foreach ($availableTemplates as $tpl): ?>
                <option
                    value="<?= (int)$tpl->id ?>"
                    <?= $tpl->id == $currentTemplate->id ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($tpl->nome, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- =========================
         EDITOR
    ========================== -->
    <textarea
        id="editor"
        name="conteudo_html"
        spellcheck="true"
    ><?= htmlspecialchars($content ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

    <!-- =========================
         AÇÕES
    ========================== -->
    <div class="mt-6 flex gap-3">
        <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
        >
            Salvar
        </button>

        <?php if ($document): ?>
            <a
                href="<?= BASE_URL ?>/document/export/pdf/<?= (int)$document->id ?>"
                class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700"
            >
                Exportar PDF
            </a>
        <?php endif; ?>
    </div>

</form>

<!-- =========================
     CKEDITOR 5 (GPL – OPEN SOURCE)
========================== -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

<script>
ClassicEditor
    .create(document.querySelector('#editor'), {
        language: 'pt-br',

        toolbar: {
            items: [
                'undo', 'redo',
                '|',
                'heading',
                '|',
                'bold', 'italic', 'underline',
                '|',
                'alignment',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'insertTable',
                '|',
                'link',
                '|',
                'blockQuote',
                '|',
                'removeFormat'
            ]
        },

        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
            ]
        }
    })
    .then(editor => {

        const editable = editor.ui.getEditableElement();

        /* =========================
           ESTILO ABNT (VISUAL)
        ========================== */
        editable.style.fontFamily = '<?= $currentTemplate->fonte_familia ?>';
        editable.style.fontSize   = '<?= (float)$currentTemplate->fonte_tamanho ?>pt';
        editable.style.lineHeight = '<?= (float)$currentTemplate->entre_linhas ?>';
        editable.style.textAlign  = '<?= $currentTemplate->alinhamento ?>';

        editable.style.paddingTop    = '<?= (float)$currentTemplate->margem_superior ?>cm';
        editable.style.paddingBottom = '<?= (float)$currentTemplate->margem_inferior ?>cm';
        editable.style.paddingLeft   = '<?= (float)$currentTemplate->margem_esquerda ?>cm';
        editable.style.paddingRight  = '<?= (float)$currentTemplate->margem_direita ?>cm';

        /* =========================
           RÉGUA ABNT (1,25 cm)
        ========================== */
        editable.style.backgroundImage =
            'linear-gradient(to right, rgba(0,0,0,0.08) 1px, transparent 1px)';
        editable.style.backgroundSize = '1.25cm 100%';
        editable.style.backgroundPositionX =
            '<?= (float)$currentTemplate->margem_esquerda ?>cm';

    })
    .catch(error => {
        console.error('Erro ao iniciar CKEditor:', error);
    });
</script>
