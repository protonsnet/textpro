<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private bool $debugImages = true;

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
        $options->set('isPhpEnabled', false);

        $options->setChroot(
            realpath(__DIR__ . '/../../public')
        );


        $options->set(
            'fontDir',
            realpath(__DIR__ . '/../../public/assets/fonts')
        );

        $options->set(
            'fontCache',
            realpath(__DIR__ . '/../../storage/fonts')
        );

        // Fonte padrão segura
        $options->set(
            'defaultFont',
            $template->fonte_familia ?? 'TimesNewRoman'
        );

        $dompdf = new Dompdf($options);

        // =============================
        // LIMPEZA DO CONTEÚDO
        // =============================
        $contentHTML = $this->sanitizeContent($contentHTML);
        $contentHTML = $this->normalizeImageUrls($contentHTML);

        // =============================
        // CSS
        // =============================
        $css = $this->buildAbntStyles($template);

        // =============================
        // CAPA
        // =============================
        $capaHtml = '';
        if (!empty($template->template_capa_html)) {
            $capaHtml = $this->renderCover(
                $template->template_capa_html,
                $document
            );
        }

        // =============================
        // HTML FINAL
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

{$capaHtml}

<header>
{$this->parseTemplateHtml($template->cabecalho_html ?? '', $documentTitle)}
</header>

<footer>
{$this->parseTemplateHtml($template->rodape_html ?? '', $documentTitle)}
</footer>

<main>
{$contentHTML}
</main>

</body>
</html>
HTML;

        // =============================
        // PDF
        // =============================
        $dompdf->setBasePath(
            realpath(__DIR__ . '/../../public')
        );
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($this->resolvePaperSize($template));
        $dompdf->render();

        $filename = preg_replace('/[^a-zA-Z0-9]/', '_', $documentTitle) . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }

    private function logPdfDebug(string $stage, array $extra = []): void
    {
        $logDir = realpath(__DIR__ . '/../../storage/logs');

        if (!$logDir) {
            return;
        }

        $data = array_merge([
            'time'   => date('Y-m-d H:i:s'),
            'stage'  => $stage,
            'memory' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
            'peak'   => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB',
        ], $extra);

        file_put_contents(
            $logDir . '/pdf_debug.log',
            json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND
        );
    }


    /**
     * Remove html/head/body duplicados
     */
    private function sanitizeContent(string $html): string
    {
        return trim(
            preg_replace('/<\/*(html|head|body)[^>]*>/i', '', $html)
        );
    }

    /**
     * Descobre a URL base da aplicação dinamicamente
     * Compatível com localhost, subpastas e produção
     */
    private function getBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            ? 'https'
            : 'http';

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Caminho real do public
        $publicPath = realpath(__DIR__ . '/../../public');

        // Caminho do script em execução
        $scriptFilename = $_SERVER['SCRIPT_FILENAME'] ?? '';

        // Descobre a subpasta correta (ex: /textpro)
        $basePath = '';

        if ($publicPath && str_starts_with($scriptFilename, $publicPath)) {
            $relative = str_replace($publicPath, '', dirname($scriptFilename));
            $basePath = rtrim(str_replace('\\', '/', $relative), '/');
        }

        return rtrim($scheme . '://' . $host . $basePath, '/');
    }

    /**
     * Converte src relativos em URLs absolutas
     */
    private function normalizeImageUrls(string $html): string
    {
        $baseUrl = rtrim($this->getBaseUrl(), '/');

        return preg_replace_callback(
            '/<img[^>]+src=["\']([^"\']+)["\']/i',
            function ($matches) use ($baseUrl) {
                $src = trim($matches[1]);

                // Já é base64
                if (str_starts_with($src, 'data:image')) {
                    return $matches[0];
                }

                // Já é URL absoluta
                if (filter_var($src, FILTER_VALIDATE_URL)) {
                    return $matches[0];
                }

                // Remove /public se vier errado
                $src = preg_replace('#^/public#', '', $src);

                // Garante barra inicial
                if ($src[0] !== '/') {
                    $src = '/' . $src;
                }

                $absoluteUrl = $baseUrl . $src;

                return str_replace($matches[1], $absoluteUrl, $matches[0]);
            },
            $html
        );
    }

    private function logImageDebug(string $message): void
    {
        $logDir = realpath(__DIR__ . '/../../storage/logs');

        if (!$logDir) {
            return;
        }

        file_put_contents(
            $logDir . '/pdf_images.log',
            '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * Papel dinâmico (A4 ou customizado em cm)
     */
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

    /**
     * CSS ABNT + Header/Footer + Numeração
     */
    private function buildAbntStyles(object $template): string
    {
        $fontDir = realpath(__DIR__ . '/../../public/assets/fonts');
        $fontCss = '';
        $fontFamily = $template->fonte_familia ?? 'TimesNewRoman';

        $fontPath = $fontDir . '/' . $fontFamily;

        if (is_dir($fontPath)) {
            $files = glob($fontPath . '/*.{ttf,otf,TTF,OTF}', GLOB_BRACE);

            foreach ($files as $file) {
                $fileName = basename($file);
                $weight   = stripos($fileName, 'bold') !== false ? 'bold' : 'normal';
                $style    = stripos($fileName, 'italic') !== false ? 'italic' : 'normal';

                $fontCss .= "
@font-face {
    font-family: '{$fontFamily}';
    src: url('file://{$file}');
    font-weight: {$weight};
    font-style: {$style};
}
";
            }
        }

return <<<CSS
{$fontCss}

@page {
    margin: {$template->margem_superior}cm
            {$template->margem_direita}cm
            {$template->margem_inferior}cm
            {$template->margem_esquerda}cm;

    {$this->buildPageNumberCss($template)}
}

@page capa {
    @bottom-center {
        content: none;
    }
}

body {
    margin: 0;
    padding: 0;
    font-family: '{$fontFamily}', serif;
    font-size: {$template->fonte_tamanho}pt;
    line-height: {$template->entre_linhas};
    text-align: {$template->alinhamento};
}

main {
    margin: 0;
    padding: 0;
}

header {
    position: fixed;
    top: -{$template->margem_superior}cm;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 10pt;
}

footer {
    position: fixed;
    bottom: -{$template->margem_inferior}cm;
    left: 0;
    right: 0;
    text-align: right;
    font-size: 10pt;
}

.capa {
    page: capa;
    text-align: center;
}

.page-break {
    page-break-after: always;
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

img {
    max-width: 100%;
    height: auto;
}

CSS;
    }

    /**
     * Renderiza a capa
     */
    private function renderCover(string $html, object $document): string
    {
        $html = $this->parseDocumentTemplateHtml($html, $document);

        return <<<HTML
<section class="capa">
{$html}
</section>
<div class="page-break"></div>
HTML;
    }

    /**
     * Header / Footer
     */
    private function parseTemplateHtml(string $html, string $title): string
    {
        return str_replace(
            ['{{TITLE}}', '{{DATE}}'],
            [$title, date('d/m/Y')],
            $html
        );
    }

    /**
     * Variáveis da capa
     */
    private function parseDocumentTemplateHtml(string $html, object $document): string
    {
        $map = [
            '{{TITULO}}'      => $document->titulo ?? '',
            '{{SUBTITULO}}'   => $document->subtitulo ?? '',
            '{{AUTOR}}'       => $document->autor ?? '',
            '{{INSTITUICAO}}' => $document->instituicao ?? '',
            '{{LOCAL}}'       => $document->local_publicacao ?? '',
            '{{ANO}}'         => $document->ano_publicacao ?? date('Y'),
        ];

        return str_replace(
            array_keys($map),
            array_values($map),
            $html
        );
    }

    private function buildPageNumberCss(object $template): string
    {
        return match ($template->posicao_numeracao ?? 'inferior_direita') {
            'superior_direita' => '
                @top-right {
                    content: counter(page);
                    font-size: 10pt;
                }
            ',
            'oculto' => '',
            default => '
                @bottom-right {
                    content: counter(page);
                    font-size: 10pt;
                }
            ',
        };
    }

}
