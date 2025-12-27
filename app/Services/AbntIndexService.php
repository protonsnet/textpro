<?php

namespace App\Services;

class AbntIndexService
{
    public function generate(string $html): string
    {
        preg_match_all('/<(h[1-3])>(.*?)<\/\1>/i', $html, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return $html;
        }

        $numbers = [1 => 0, 2 => 0, 3 => 0];
        $indexItems = '';

        foreach ($matches as $m) {
            $level = (int) substr($m[1], 1);
            $numbers[$level]++;
            for ($i = $level + 1; $i <= 3; $i++) {
                $numbers[$i] = 0;
            }

            $prefix = array_slice($numbers, 1, $level);
            $num = implode('.', array_filter($prefix));

            $title = strip_tags($m[2]);

            $indexItems .= "<li class='nivel-{$level}'>{$num} {$title}</li>";
        }

        $sumario = "
        <div class='sumario'>
            <h2>SUMÁRIO</h2>
            <ol>{$indexItems}</ol>
        </div>
        <div class='page-break'></div>
        ";

        return $sumario . $html;
    }
}
