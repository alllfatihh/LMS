<?php

use App\Models\Kelas;
use App\Models\Bab;
use App\Models\Materi;

// 1. Get the source chapters from Kelas ID 1 with Materi
$sourceBab = Bab::where('kelas_id', 1)->with('materi')->get();

// 2. Get all other classes
$allKelas = Kelas::where('id', '!=', 1)->get();

foreach ($allKelas as $kelas) {
    echo "Syncing Materi for Kelas: {$kelas->name} (ID: {$kelas->id})...\n";
    
    foreach ($sourceBab as $sBab) {
        // Find the corresponding Bab in the target class
        $targetBab = Bab::where('kelas_id', $kelas->id)
            ->where('title', $sBab->title)
            ->first();
            
        if (!$targetBab) {
            echo "  Chapter '{$sBab->title}' not found in target class. Skipping.\n";
            continue;
        }
        
        foreach ($sBab->materi as $sMateri) {
            // Check if materi exists in target Bab
            $existingMateri = Materi::where('bab_id', $targetBab->id)
                ->where('judul', $sMateri->judul)
                ->first();
                
            if (!$existingMateri) {
                // Create Materi
                Materi::create([
                    'kelas_id' => $kelas->id,
                    'bab_id' => $targetBab->id,
                    'judul' => $sMateri->judul,
                    'ringkasan' => $sMateri->ringkasan ?: "Ringkasan materi untuk " . $sMateri->judul,
                    'slug' => $sMateri->slug . '-' . $kelas->id . '-' . rand(100,999), // unique slug
                    'order' => $sMateri->order,
                    'tipe' => $sMateri->tipe,
                    'konten_teks' => $sMateri->konten_teks,
                    'link_video' => $sMateri->link_video,
                    'file_path' => $sMateri->file_path,
                ]);
                echo "    Created Materi: {$sMateri->judul}\n";
            } else {
                // Update ringkasan if empty
                if (empty($existingMateri->ringkasan)) {
                    $existingMateri->update([
                        'ringkasan' => "Ringkasan materi untuk " . $sMateri->judul
                    ]);
                    echo "    Updated empty ringkasan for: {$sMateri->judul}\n";
                }
            }
        }
    }
}

// Also update the source class (ID 1) itself if it has empty ringkasan
foreach ($sourceBab as $sBab) {
    foreach ($sBab->materi as $sMateri) {
        if (empty($sMateri->ringkasan)) {
            $sMateri->update([
                'ringkasan' => "Ringkasan materi untuk " . $sMateri->judul
            ]);
            echo "Updated source ringkasan for: {$sMateri->judul}\n";
        }
    }
}

echo "Materi synchronization and summary update complete!\n";
