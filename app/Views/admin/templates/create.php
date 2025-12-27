<?php
/**
 * View: Criar Template
 * Compatível com PdfService e AdminTemplateController
 */
?>

<h1 class="text-3xl font-bold mb-6">Criar Novo Template ABNT</h1>

<form method="POST" action="<?= BASE_URL ?>/admin/templates/create" class="space-y-6">

    <!-- =========================
         DADOS BÁSICOS
    ========================== -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Informações Básicas</h2>

        <div>
            <label class="block font-semibold mb-1">Nome do Template *</label>
            <input type="text" name="nome" required
                   class="w-full border rounded p-2">
        </div>
    </div>

    <!-- =========================
         CONFIGURAÇÕES DE PÁGINA
    ========================== -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Configuração da Página</h2>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block font-semibold mb-1">Formato do Papel</label>
                <select name="tamanho_papel" class="w-full border rounded p-2">
                    <option value="A4">A4 (21 × 29,7 cm)</option>
                    <option value="A3">A3 (29,7 × 42 cm)</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>

            <div class="text-sm text-gray-600 flex items-end">
                Use "Personalizado" para definir dimensões exatas
            </div>
        </div>

        <!-- TAMANHO PERSONALIZADO -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold">
                    Largura do Papel (cm)
                </label>
                <input type="number" step="0.1" name="largura_papel"
                       placeholder="Ex: 21"
                       class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">
                    Altura do Papel (cm)
                </label>
                <input type="number" step="0.1" name="altura_papel"
                       placeholder="Ex: 29.7"
                       class="w-full border rounded p-2">
            </div>
        </div>

        <!-- MARGENS -->
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold">Margem Superior (cm)</label>
                <input type="number" step="0.1" name="margem_superior" value="3.0"
                       class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">Margem Inferior (cm)</label>
                <input type="number" step="0.1" name="margem_inferior" value="2.0"
                       class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">Margem Esquerda (cm)</label>
                <input type="number" step="0.1" name="margem_esquerda" value="3.0"
                       class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">Margem Direita (cm)</label>
                <input type="number" step="0.1" name="margem_direita" value="2.0"
                       class="w-full border rounded p-2">
            </div>
        </div>
    </div>

    <!-- =========================
         CONFIGURAÇÕES DE TEXTO
    ========================== -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Configurações de Texto</h2>

        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold">Fonte</label>
                <select name="fonte_familia" class="w-full border rounded p-2">
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Arial">Arial</option>
                    <option value="Calibri">Calibri</option>
                    <option value="Georgia">Georgia</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold">Tamanho da Fonte (pt)</label>
                <input type="number" name="fonte_tamanho" value="12"
                       class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">Entre Linhas</label>
                <input type="number" step="0.1" name="entre_linhas" value="1.5"
                       class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">Alinhamento</label>
                <select name="alinhamento" class="w-full border rounded p-2">
                    <option value="justify">Justificado</option>
                    <option value="left">Esquerda</option>
                    <option value="center">Centro</option>
                </select>
            </div>
        </div>
    </div>

    <!-- =========================
         LAYOUT / NUMERAÇÃO
    ========================== -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Layout e Numeração</h2>

        <label class="block font-semibold mb-1">Posição da Numeração</label>
        <select name="posicao_numeracao" class="w-full border rounded p-2">
            <option value="superior_direita">Superior Direita</option>
            <option value="inferior_direita">Inferior Direita</option>
            <option value="oculto">Oculto</option>
        </select>
    </div>

    <!-- =========================
         CAPA
    ========================== -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Template da Capa</h2>

        <p class="text-sm text-gray-600 mb-2">
            Variáveis disponíveis:
            <code>{{TITLE}}</code>,
            <code>{{AUTHOR}}</code>,
            <code>{{YEAR}}</code>
        </p>

        <textarea name="template_capa_html"
                  rows="6"
                  class="w-full border rounded p-2"
                  placeholder="<h1>{{TITLE}}</h1>">
        </textarea>
    </div>

    <!-- =========================
         AÇÕES
    ========================== -->
    <div class="flex gap-4">
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Salvar Template
        </button>

        <a href="<?= BASE_URL ?>/admin/templates"
           class="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400">
            Cancelar
        </a>
    </div>

</form>
