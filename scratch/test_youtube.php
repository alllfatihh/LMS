<?php

require __DIR__.'/../vendor/autoload.php';
use App\Support\Youtube;

$urls = [
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/shorts/5-9vA_I2Q0I',
    'https://youtube.com/live/5-9vA_I2Q0I',
    'dQw4w9WgXcQ'
];

foreach ($urls as $url) {
    $id = Youtube::extractVideoId($url);
    echo "URL: $url => ID: " . ($id ?? 'NULL') . "\n";
}
