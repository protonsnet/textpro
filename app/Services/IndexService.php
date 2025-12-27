<?php
namespace App\Services;

class IndexService
{
    public function gerarIndice(string $html): string
    {
        preg_match_all('/<h1>(.*?)<\/h1>/', $html, $matches);

        if (empty($matches[1])) {
            return '';
        }

        $items = '';
        foreach ($matches[1] as $title) {
            $items .= "<li>{$title}</li>";
        }

        return "
        <div class='indice'>
            <h2>Sumário</h2>
            <ol>{$items}</ol>
        </div>
        <div class='page-break'></div>
        ";
    }
}
