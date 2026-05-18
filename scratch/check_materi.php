<?php

use App\Models\Materi;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$materi = Materi::all();
if ($materi->isEmpty()) {
    echo "Tabel materi kosong.\n";
} else {
    foreach ($materi as $m) {
        echo "ID: {$m->id} | Judul: {$m->judul} | Tipe: '{$m->tipe}' | Link: '{$m->link_video}' | File: '{$m->file_path}'\n";
    }
}
