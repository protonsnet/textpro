<?php

namespace App\Services;

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;

class TtsService
{
    public function gerarMp3(string $html, string $filename, float $speed = 1.0): string
    {
        $text = $this->htmlParaTexto($html);

        $client = new TextToSpeechClient([
            'credentials' => BASE_PATH . '/config/google-tts.json'
        ]);

        $response = $client->synthesizeSpeech([
            'input' => ['text' => $text],
            'voice' => [
                'languageCode' => 'pt-BR',
                'name' => 'pt-BR-Neural2-B'
            ],
            'audioConfig' => [
                'audioEncoding' => 'MP3',
                'speakingRate' => $speed
            ]
        ]);

        $path = BASE_PATH . "/storage/audio/{$filename}.mp3";
        file_put_contents($path, $response->getAudioContent());

        return $path;
    }

    /**
     * Remove HTML e cria pausas por parágrafo
     */
    private function htmlParaTexto(string $html): string
    {
        $html = preg_replace('/<\/p>/i', "\n\n", $html);
        $html = strip_tags($html);

        return trim($html);
    }
}
