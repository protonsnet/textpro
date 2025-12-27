<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class DocumentPdfService
{
    public static function generate(object $document, object $template): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', $template->fonte_familia);

        $dompdf = new Dompdf($options);

        $html = self::buildHtml($document, $template);

        $dompdf->loadHtml($html);

        $dompdf->setPaper(
            $template->tamanho_papel,
            'portrait'
        );

        $dompdf->render();

        return $dompdf->output();
    }

    private static function buildHtml(object $document, object $template): string
    {
        $pageNumberCss = match ($template->posicao_numeracao) {
            'superior_direita' => 'top: 1cm; right: 2cm;',
            'inferior_direita' => 'bottom: 1cm; right: 2cm;',
            default            => 'display:none;'
        };

        return "
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<style>
@page {
    margin: {$template->margem_superior}cm {$template->margem_direita}cm {$template->margem_inferior}cm {$template->margem_esquerda}cm;
}

body {
    font-family: {$template->fonte_familia};
    font-size: {$template->fonte_tamanho}pt;
    line-height: {$template->entre_linhas};
    text-align: {$template->alinhamento};
}

header {
    position: fixed;
    top: -1.5cm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 10pt;
}

footer {
    position: fixed;
    bottom: -1.5cm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 10pt;
}

.page-number {
    position: fixed;
    {$pageNumberCss}
    font-size: 10pt;
}
</style>
</head>

<body>

<header>
    {$template->cabecalho_html}
</header>

<footer>
    {$template->rodape_html}
</footer>

<div class='page-number'>
    Página {PAGE_NUM}
</div>

<!-- CAPA -->
<div style='page-break-after: always'>
    " . self::renderCover($document, $template) . "
</div>

<!-- PREFÁCIO -->
" . (!empty($document->prefacio)
        ? "<div style='page-break-after: always'>{$document->prefacio}</div>"
        : ""
    ) . "

<!-- CONTEÚDO -->
{$document->conteudo_html}

</body>
</html>";
    }

    private static function renderCover(object $document, object $template): string
    {
        if (!$template->template_capa_html) {
            return "<h1 style='text-align:center;margin-top:10cm'>{$document->titulo}</h1>";
        }

        return str_replace(
            ['{{TITLE}}'],
            [$document->titulo],
            $template->template_capa_html
        );
    }
}
