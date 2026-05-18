<?php

use App\Models\Materi;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$materi = Materi::find(1);
if ($materi) {
    echo "ID 1 Tipe: ";
    var_export($materi->tipe);
    echo "\n";
    echo "ID 1 Link: ";
    var_export($materi->link_video);
    echo "\n";
} else {
    echo "Materi ID 1 tidak ditemukan.\n";
}
