<?php

function fallback_image_url(string $seed = ''): string
{
    $images = [
        'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1503428593586-e225b39bddfe?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1511988617509-a57c8a288659?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&w=1400&q=80',
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

