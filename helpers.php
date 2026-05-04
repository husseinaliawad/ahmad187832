<?php

function fallback_image_url(string $seed = ''): string
{
    $images = [
        'assets/img/event1.jpg',
        'assets/img/event2.jpg',
        'assets/img/event3.jpg',
        'assets/img/1759749477_event3.jpg',
        'assets/img/1759749565_event1.jpg',
        'assets/img/1759749709_event3.jpg',
        'assets/img/1759749733_event3.jpg',
        'assets/img/1759749743_event2.jpg',
    ];

    $index = abs(crc32($seed)) % count($images);
    return $images[$index];
}

function event_image_url(?string $imagePath, string $category = '', string $seed = ''): string
{
    $imagePath = trim((string)$imagePath);
    $seedKey = $seed !== '' ? $seed : ($category . '-' . $imagePath);

    if ($imagePath === '') {
        return fallback_image_url($seedKey);
    }

    if (preg_match('/^https?:\/\//i', $imagePath) === 1) {
        return $imagePath;
    }

    $cleanPath = ltrim($imagePath, '/\\');
    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $cleanPath);

    if (is_file($fullPath)) {
        return str_replace('\\', '/', $cleanPath);
    }

    return fallback_image_url($seedKey);
}
