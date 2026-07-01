<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class OcrService
{
    /**
     * Extract text from an image using Tesseract OCR.
     */
    public function extractText(string $imagePath): string
    {
        if (!file_exists($imagePath)) {
            return '';
        }

        try {
            $process = new Process([
                'tesseract',
                $imagePath,
                'stdout',
                '-l', 'chi_sim+eng',
                '--psm', '3',
            ]);

            $process->setTimeout(30);
            $process->run();

            if ($process->isSuccessful()) {
                $text = trim($process->getOutput());
                $text = preg_replace('/\s+/', ' ', $text);
                return mb_strlen($text) > 5000 ? mb_substr($text, 0, 5000) : $text;
            }

            return '';
        } catch (\Exception $e) {
            Log::error('OCR exception', ['image' => $imagePath, 'error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Run OCR on an image and save the result to the database.
     */
    public function processImage(\App\Models\Image $image): string
    {
        // Build full path from strategy config root + pathname
        $root = $image->strategy?->configs->get('root') ?? config('filesystems.disks.uploads.root');
        $fullPath = rtrim($root, '/') . '/' . ltrim($image->pathname, '/');

        if (!file_exists($fullPath)) {
            Log::warning('OCR: file not found', ['path' => $fullPath]);
            return '';
        }

        $text = $this->extractText($fullPath);

        if ($text) {
            $image->update(['ocr_text' => $text]);
            Log::info('OCR completed', [
                'image_id' => $image->id,
                'text_length' => mb_strlen($text),
            ]);
        }

        return $text;
    }
}
