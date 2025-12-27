<?php

namespace App\Services;

class AudioService
{
    /**
     * Gera áudio MP3 a partir do HTML do documento
     */
    public function generateAndStream(string $title, string $html): void
    {
        $text = $this->sanitizeHtml($html);

        /**
         * IMPLEMENTAÇÃO SIMPLES (TTS LOCAL / PLACEHOLDER)
         * No futuro: Google TTS, AWS Polly, Azure
         */
        $audioFile = $this->generateAudio($text);

        header('Content-Type: audio/mpeg');
        header('Content-Disposition: attachment; filename="' . $this->slug($title) . '.mp3"');
        readfile($audioFile);
        unlink($audioFile);
        exit;
    }

    /**
     * Remove HTML e normaliza texto
     */
    private function sanitizeHtml(string $html): string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Placeholder de geração de áudio
     * (mock para integração futura)
     */
    private function generateAudio(string $text): string
    {
        $file = sys_get_temp_dir() . '/' . uniqid('audio_') . '.mp3';

        /**
         * MOCK:
         * Aqui futuramente entra Google TTS / AWS Polly
         */
        file_put_contents($file, "NARRAÇÃO SIMULADA:\n\n" . $text);

        return $file;
    }

    private function slug(string $text): string
    {
        return preg_replace('/[^a-z0-9]+/i', '_', strtolower($text));
    }
}
