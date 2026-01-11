<?php
/**
 * @var object $template
 * @var string $title
 */
?>

<h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars($title) ?></h1>

<form method="POST"
      action="<?= BASE_URL ?>/admin/templates/edit/<?= $template->id ?>"
      class="space-y-8">

    <!-- =========================
         DADOS BÁSICOS
    ========================== -->
    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Informações Gerais</h2>

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-1">Nome do Template</label>
            <input type="text"
                   name="nome"
                   required
                   value="<?= htmlspecialchars($template->nome) ?>"
                   class="w-full border rounded p-2">
        </div>
    </section>

    <!-- =========================
         PAPEL / DIMENSÕES
    ========================== -->
    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Configuração do Papel</h2>

        <div class="grid grid-cols-3 gap-4">

            <div>
                <label class="block text-sm font-semibold mb-1">Preset</label>
                <select name="tamanho_papel"
                        class="border rounded p-2 w-full">
                    <option value="custom"
                        <?= $template->tamanho_papel === 'custom' ? 'selected' : '' ?>>
                        Personalizado
                    </option>
                    <option value="A4"
                        <?= $template->tamanho_papel === 'A4' ? 'selected' : '' ?>>
                        A4 (21 x 29.7 cm)
                    </option>
                    <option value="A3"
                        <?= $template->tamanho_papel === 'A3' ? 'selected' : '' ?>>
                        A3 (29.7 x 42 cm)
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Largura (cm)</label>
                <input type="number" step="0.01" name="largura_papel"
                    value="<?= htmlspecialchars($template->largura_papel ?? 21) ?>"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Altura (cm)</label>
                <input type="number" step="0.01" name="altura_papel"
                    value="<?= htmlspecialchars($template->altura_papel ?? 29.7) ?>"
                    class="w-full border rounded p-2">
            </div>

        </div>
    </section>

    <!-- =========================
         MARGENS (ABNT)
    ========================== -->
    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Margens (cm)</h2>

        <div class="grid grid-cols-4 gap-4">
            <input type="number" step="0.1" name="margem_superior"
                   value="<?= $template->margem_superior ?>" placeholder="Superior"
                   class="border rounded p-2">
            <input type="number" step="0.1" name="margem_inferior"
                   value="<?= $template->margem_inferior ?>" placeholder="Inferior"
                   class="border rounded p-2">
            <input type="number" step="0.1" name="margem_esquerda"
                   value="<?= $template->margem_esquerda ?>" placeholder="Esquerda"
                   class="border rounded p-2">
            <input type="number" step="0.1" name="margem_direita"
                   value="<?= $template->margem_direita ?>" placeholder="Direita"
                   class="border rounded p-2">
        </div>
    </section>

    <!-- =========================
         TEXTO / ABNT
    ========================== -->
    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Texto</h2>

        <div class="grid grid-cols-4 gap-4">
            <select name="fonte_familia" class="border rounded p-2" required>
                <?php foreach ($availableFonts as $font): ?>
                    <option value="<?= htmlspecialchars($font, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $template->fonte_familia === $font ? 'selected' : '' ?>>
                        <?= htmlspecialchars($font, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>


            <input type="number" name="fonte_tamanho"
                   value="<?= $template->fonte_tamanho ?>"
                   placeholder="Tamanho"
                   class="border rounded p-2">

            <input type="number" step="0.1" name="entre_linhas"
                   value="<?= $template->entre_linhas ?>"
                   placeholder="Entre linhas"
                   class="border rounded p-2">

            <select name="alinhamento"
                    class="border rounded p-2">
                <?php foreach (['justify', 'left', 'center', 'right'] as $opt): ?>
                    <option value="<?= $opt ?>"
                        <?= $template->alinhamento === $opt ? 'selected' : '' ?>>
                        <?= ucfirst($opt) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </section>

    <!-- =========================
         NUMERAÇÃO
    ========================== -->
    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Numeração de Páginas</h2>

        <select name="posicao_numeracao"
                class="border rounded p-2 w-full">
            <option value="superior_direita"
                <?= $template->posicao_numeracao === 'superior_direita' ? 'selected' : '' ?>>
                Superior Direita
            </option>
            <option value="inferior_direita"
                <?= $template->posicao_numeracao === 'inferior_direita' ? 'selected' : '' ?>>
                Inferior Direita
            </option>
            <option value="oculto"
                <?= $template->posicao_numeracao === 'oculto' ? 'selected' : '' ?>>
                Oculto
            </option>
        </select>
    </section>

    <!-- =========================
         CAPA
    ========================== -->
    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Capa (HTML)</h2>

        <textarea name="template_capa_html"
                  rows="8"
                  class="w-full border rounded p-2 font-mono"><?= htmlspecialchars($template->template_capa_html ?? '') ?></textarea>

        <p class="text-xs text-gray-500 mt-2">
            Variáveis disponíveis:
            {{TITULO}}, {{SUBTITULO}}, {{AUTOR}}, {{INSTITUICAO}}, {{LOCAL}}, {{ANO}}
        </p>
    </section>

    <!-- =========================
        CABEÇALHO / RODAPÉ
    ========================== -->
    <section class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Cabeçalho e Rodapé (HTML)</h2>

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-1">Cabeçalho</label>
            <textarea name="cabecalho_html"
                    rows="3"
                    class="w-full border rounded p-2 font-mono"
                    placeholder="Ex: {{TITLE}}">
    <?= htmlspecialchars($template->cabecalho_html ?? '') ?>
            </textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Rodapé</label>
            <textarea name="rodape_html"
                    rows="3"
                    class="w-full border rounded p-2 font-mono"
                    placeholder="Ex: Página {{PAGE}}">
    <?= htmlspecialchars($template->rodape_html ?? '') ?>
            </textarea>
        </div>

        <p class="text-xs text-gray-500 mt-2">
            Variáveis disponíveis: {{TITLE}}, {{DATE}}
        </p>
    </section>

    <!-- =========================
         AÇÕES
    ========================== -->
    <div class="flex gap-4">
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            💾 Salvar Alterações
        </button>

        <a href="<?= BASE_URL ?>/admin/templates"
           class="px-6 py-2 rounded border">
            Cancelar
        </a>
    </div>

</form>
