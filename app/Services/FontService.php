<?php

namespace App\Services;

class FontService
{
    private string $fontPath;

    public function __construct()
    {
        $this->fontPath = realpath(__DIR__ . '/../../public/assets/fonts');
    }

    /**
     * Retorna fontes disponíveis com seus arquivos
     */
    public function getAvailableFonts(): array
    {
        $fonts = [];

        foreach (glob($this->fontPath . '/*', GLOB_ONLYDIR) as $dir) {
            $fontName = basename($dir);

            $files = glob($dir . '/*.{ttf,otf,TTF,OTF}', GLOB_BRACE);

            if (!$files) {
                continue;
            }

            $fonts[$fontName] = array_map('basename', $files);
        }

        return $fonts;
    }

    /**
     * Retorna apenas os nomes (para selects / CKEditor)
     */
    public function getFontNames(): array
    {
        return array_keys($this->getAvailableFonts());
    }

    public function getFontPath(): string
    {
        return $this->fontPath;
    }
}
