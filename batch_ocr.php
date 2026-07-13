<?php
$service = new \App\Services\OcrService();
$images = \App\Models\Image::whereNull('ocr_text')->get();
$total = $images->count();
$processed = 0;
$success = 0;

echo "Starting batch OCR for {$total} images...\n";

foreach ($images as $img) {
    $root = $img->strategy->configs->get('root');
    $fullPath = rtrim($root, '/') . '/' . ltrim($img->pathname, '/');
    if (!file_exists($fullPath)) {
        continue;
    }
    $text = $service->processImage($img);
    $processed++;
    if ($text) $success++;
    if ($processed % 50 == 0) {
        echo "Processed: {$processed}/{$total}, with text: {$success}\n";
    }
}

echo "Done! Processed: {$processed}, with text: {$success}\n";
