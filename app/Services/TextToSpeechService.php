<?php

namespace App\Services;

use Google\Cloud\TextToSpeech\V1\{
    TextToSpeechClient,
    SynthesisInput,
    VoiceSelectionParams,
    AudioConfig,
    AudioEncoding
};

class TextToSpeechService
{
    protected TextToSpeechClient $client;

    public function __construct()
    {
        $this->client = new TextToSpeechClient();
    }

    public function generateMp3(
        string $html,
        string $filename,
        float $speed = 1.0,
        int $pauseMs = 700
    ): string {

        $paragraphs = $this->extractParagraphs($html);
        $audioData  = '';

        foreach ($paragraphs as $paragraph) {

            $input = new SynthesisInput([
                'text' => $paragraph
            ]);

            $voice = new VoiceSelectionParams([
                'language_code' => 'pt-BR',
                'name'          => getenv('GOOGLE_TTS_VOICE')
            ]);

            $audioConfig = new AudioConfig([
                'audio_encoding' => AudioEncoding::MP3,
                'speaking_rate'  => $speed
            ]);

            $response = $this->client->synthesizeSpeech(
                $input,
                $voice,
                $audioConfig
            );

            $audioData .= $response->getAudioContent();

            // pausa artificial entre parágrafos
            usleep($pauseMs * 1000);
        }

        $path = storage_path("tts/{$filename}.mp3");
        file_put_contents($path, $audioData);

        return $path;
    }

    private function extractParagraphs(string $html): array
    {
        $text = strip_tags($html, '<p><br>');
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = strip_tags($text);

        return array_filter(
            array_map('trim', explode("\n", $text))
        );
    }
}
