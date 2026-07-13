<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrService
{
    /**
     * Get AI config from database (cached).
     */
    private function getConfig(): array
    {
        $provider = Cache::rememberForever('configs', fn() => \App\Utils::config()->toArray());
        return [
            'enabled'  => $provider['ai_ocr_enabled'] ?? true,
            'provider' => $provider['ai_provider'] ?? 'tesseract',
            'api_url'  => $provider['ai_api_url'] ?? '',
            'api_key'  => $provider['ai_api_key'] ?? '',
            'model'    => $provider['ai_model'] ?? '',
        ];
    }

    /**
     * Process an image — extract text via configured provider.
     */
    public function processImage($image): string
    {
        $config = $this->getConfig();

        if (!$config['enabled'] || $config['provider'] === 'none') {
            return '';
        }

        $fullPath = $this->getFullPath($image);
        if (!$fullPath || !file_exists($fullPath)) {
            return '';
        }

        $text = match ($config['provider']) {
            'openai'  => $this->ocrViaOpenAI($fullPath, $config),
            default   => $this->ocrViaTesseract($fullPath),
        };

        $text = trim($text);
        if ($text !== '') {
            $image->update(['ocr_text' => $text]);
            Log::info("OCR done for image {$image->id}: " . mb_substr($text, 0, 100));
        }

        return $text;
    }

    /**
     * Build full disk path from image record.
     */
    private function getFullPath($image): ?string
    {
        $filesystem = $image->strategy->filesystem ?? null;
        $strategyConfig = $image->strategy->configs ?? null;
        if (!$filesystem || !$strategyConfig) return null;

        $root = $strategyConfig->get('root');
        if (!$root) return null;

        return rtrim($root, '/') . '/' . ltrim($image->pathname, '/');
    }

    /**
     * Local Tesseract OCR.
     */
    private function ocrViaTesseract(string $filePath): string
    {
        $process = new \Symfony\Component\Process\Process([
            'tesseract', $filePath, 'stdout',
            '-l', 'chi_sim+eng',
            '--psm', '3',
        ]);
        $process->setTimeout(30);
        $process->run();

        return $process->isSuccessful() ? $process->getOutput() : '';
    }

    /**
     * OpenAI-compatible Vision API.
     */
    private function ocrViaOpenAI(string $filePath, array $config): string
    {
        $apiKey = $config['api_key'];
        $apiUrl = $config['api_url'] ?: 'https://api.openai.com/v1/chat/completions';
        $model  = $config['model'] ?: 'gpt-4o-mini';

        if (empty($apiKey)) {
            Log::warning('OCR: OpenAI API key not configured');
            return '';
        }

        $imageData = base64_encode(file_get_contents($filePath));
        $mimeType  = $this->getMimeType($filePath);
        $dataUrl   = "data:{$mimeType};base64,{$imageData}";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->timeout(60)->post($apiUrl, [
                'model'    => $model,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => [
                            [
                                'type'      => 'text',
                                'text'      => '请识别这张图片中的所有文字内容，直接输出文字，不要添加任何说明。',
                            ],
                            [
                                'type'      => 'image_url',
                                'image_url' => ['url' => $dataUrl],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 4096,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                return $body['choices'][0]['message']['content'] ?? '';
            }

            Log::error('OCR API error: ' . $response->body());
            return '';
        } catch (\Exception $e) {
            Log::error('OCR API exception: ' . $e->getMessage());
            return '';
        }
    }

    private function getMimeType(string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            'bmp'         => 'image/bmp',
            default       => 'image/png',
        };
    }
}
