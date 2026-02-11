<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <base href="<?= BASE_URL ?>/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'NexoWriter - Soluções ABNT') ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .flex-container { min-height: 100vh; }
        
        /* Estilo para quando o editor está aberto: removemos limites e paddings */
        .editor-fullscreen {
            padding: 0 !important;
            overflow: hidden !important;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex flex-col flex-container">
        
        <?php if (!isset($no_sidebar)): ?>
            <?php include __DIR__ . '/_header.php'; ?>
        <?php endif; ?>

        <div class="flex flex-1">
            
            <?php
            /**
             * AJUSTE CRUCIAL:
             * Só inclui a sidebar se:
             * 1. O usuário estiver logado
             * 2. NÃO for uma página marcada com $no_sidebar (como o seu editor)
             */
            if (isset($_SESSION['user_id']) && !isset($no_sidebar)):
                include __DIR__ . '/_sidebar.php';
            endif;
            ?>

            <main class="flex-1 <?= isset($no_sidebar) ? 'editor-fullscreen' : 'p-8 overflow-y-auto' ?> min-w-0">
                <?= $content ?>
            </main>
        </div>

        <?php if (!isset($no_sidebar)): ?>
            <?php include __DIR__ . '/_footer.php'; ?>
        <?php endif; ?>
    </div>

    <div id="docModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-bold mb-4 text-gray-800">Novo Documento</h3>
            <form id="docForm" method="POST" action="">
                <input type="hidden" name="id" id="docId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título do Arquivo</label>
                    <input type="text" name="titulo" id="docInputTitle" required 
                        class="w-full border border-gray-300 px-3 py-2 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancelar</button>
                    <button type="submit" id="modalSubmitBtn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

<script>
const modal = document.getElementById('docModal');
const docForm = document.getElementById('docForm');
const modalTitle = document.getElementById('modalTitle');
const docInputTitle = document.getElementById('docInputTitle');
const docId = document.getElementById('docId');

function openModalCreate(type = 'word') {
    const modal = document.getElementById('docModal'); // Usando o ID correto do seu HTML
    const form = document.getElementById('docForm');
    const title = document.getElementById('modalTitle');
    
    if (type === 'excel') {
        title.innerText = "Nova Planilha Excel";
        form.action = "<?= BASE_URL ?>/editor-beta/create-xlsx";
    } else if (type === 'slide') {
        title.innerText = "Nova Apresentação (Slides)";
        form.action = "<?= BASE_URL ?>/editor-beta/create-pptx";
    } else {
        title.innerText = "Novo Documento Word";
        form.action = "<?= BASE_URL ?>/editor-beta/create";
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function openModalRename(id, titulo) {
    modalTitle.innerText = "Renomear Documento";
    docForm.action = "<?= BASE_URL ?>/document/rename";
    docInputTitle.value = titulo;
    docId.value = id;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    docInputTitle.focus();
}

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Fechar modal ao clicar fora dele
window.onclick = function(event) {
    if (event.target == modal) closeModal();
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openModalWord') === '1') openModalCreate('word');
    if (urlParams.get('openModalExcel') === '1') openModalCreate('excel');
    if (urlParams.get('openModalSlide') === '1') openModalCreate('slide');
});



function closeModalCreate() {
    document.getElementById('modalCreate').classList.add('hidden');
}


</script>

</body>
</html>