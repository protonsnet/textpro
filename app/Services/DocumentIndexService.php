<?php
namespace App\Services;

class DocumentIndexService
{
    public function gerarIndice(string $html): string
    {
        // Buscar h1, h2, h3
        // Criar lista ordenada
        // Inserir page-break
        return $indiceHtml . $html;
    }
}
