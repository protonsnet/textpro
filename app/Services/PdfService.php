<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function generateAbntPdf(
        string $contentHTML,
        object $template,
        string $documentTitle,
        object $document
    ): void {

        // =============================
        // DOMPDF CONFIG
        // =============================
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', $template->fonte_familia ?? 'Times New Roman');

        $dompdf = new Dompdf($options);

        // =============================
        // LIMPEZA DO CONTEÚDO (CRÍTICO)
        // =============================
        $contentHTML = $this->sanitizeContent($contentHTML);

        // =============================
        // CSS
        // =============================
        $css = $this->buildAbntStyles($template);

        // =============================
        // HTML FINAL (ÚNICO BODY)
        // =============================
        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<style>
{$css}
</style>
</head>
<body>
{$contentHTML}
</body>
</html>
HTML;

        // =============================
        // PDF
        // =============================
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($this->resolvePaperSize($template));
        $dompdf->render();

        $filename = preg_replace('/[^a-zA-Z0-9]/', '_', $documentTitle) . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }

    /**
     * REMOVE html/head/body duplicados
     */
    private function sanitizeContent(string $html): string
    {
        $html = preg_replace('/<\/*(html|head|body)[^>]*>/i', '', $html);
        return trim($html);
    }

    private function resolvePaperSize(object $template): array|string
    {
        if (!empty($template->largura_papel) && !empty($template->altura_papel)) {
            return [
                0,
                0,
                $this->cmToPoints((float)$template->largura_papel),
                $this->cmToPoints((float)$template->altura_papel)
            ];
        }

        return $template->tamanho_papel ?? 'A4';
    }

    private function cmToPoints(float $cm): float
    {
        return round($cm * 28.3465, 2);
    }

    private function buildAbntStyles(object $template): string
    {
        return <<<CSS
@page {
    margin: {$template->margem_superior}cm {$template->margem_direita}cm
            {$template->margem_inferior}cm {$template->margem_esquerda}cm;
}

body {
    font-family: '{$template->fonte_familia}', serif;
    font-size: {$template->fonte_tamanho}pt;
    line-height: {$template->entre_linhas};
    text-align: {$template->alinhamento};
}

p {
    text-indent: 1.25cm;
    margin: 0;
}

h1, h2, h3 {
    font-weight: bold;
    text-indent: 0;
    margin-top: 1cm;
    margin-bottom: 0.5cm;
}
CSS;
    }
}
