<?php

use App\Models\Kelas;
use App\Models\Bab;
use App\Models\Soal;
use App\Models\Materi;

// 1. Get the source chapters from Kelas ID 1
$sourceBab = Bab::where('kelas_id', 1)->with('daftarSoal')->get();

// 2. Get all other classes
$allKelas = Kelas::where('id', '!=', 1)->get();

foreach ($allKelas as $kelas) {
    echo "Processing Kelas: {$kelas->name} (ID: {$kelas->id})...\n";
    
    foreach ($sourceBab as $sBab) {
        // Check if chapter already exists in this class (by title)
        $existingBab = Bab::where('kelas_id', $kelas->id)
            ->where('title', $sBab->title)
            ->first();
            
        if ($existingBab) {
            echo "  Chapter '{$sBab->title}' already exists. Skipping.\n";
            $targetBab = $existingBab;
        } else {
            // Create the chapter
            $targetBab = Bab::create([
                'kelas_id' => $kelas->id,
                'title' => $sBab->title,
                'order' => $sBab->order,
            ]);
            echo "  Created Chapter: {$sBab->title}\n";
        }
        
        // 3. Sync Questions (Soal)
        foreach ($sBab->daftarSoal as $sSoal) {
            // Check if question exists (by template - simple heuristic)
            $existingSoal = Soal::where('bab_id', $targetBab->id)
                ->where('question_template', $sSoal->question_template)
                ->exists();
                
            if (!$existingSoal) {
                Soal::create([
                    'bab_id' => $targetBab->id,
                    'question_template' => $sSoal->question_template,
                    'variable_rules' => $sSoal->variable_rules,
                    'answer_formula' => $sSoal->answer_formula,
                ]);
            }
        }
    }
}

echo "Synchronization complete!\n";
