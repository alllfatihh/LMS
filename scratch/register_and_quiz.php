<?php

use App\Models\User;
use App\Models\Kelas;
use App\Models\Bab;
use App\Models\SesiUjian;
use App\Models\Jawaban;
use App\Services\EvaluasiSoalService;
use Illuminate\Support\Facades\Hash;

$evaluasi = app(EvaluasiSoalService::class);

for ($i = 1; $i <= 5; $i++) {
    $email = "siswa{$i}@example.com";
    $name = "Siswa {$i}";
    
    // 1. Register User
    $user = User::firstOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => Hash::make('password'),
            'role' => 'siswa'
        ]
    );
    
    echo "Processing $name ($email)...\n";
    
    // 2. Join all classes
    $allKelas = Kelas::all();
    foreach ($allKelas as $kelas) {
        if (!$user->kelasYangDiikuti()->where('kelas_id', $kelas->id)->exists()) {
            $user->kelasYangDiikuti()->attach($kelas->id);
            echo "  Joined class: {$kelas->name}\n";
        }
    }
    
    // 3. Complete all quizzes
    foreach ($allKelas as $kelas) {
        foreach ($kelas->bab as $bab) {
            if ($bab->daftarSoal()->count() == 0) continue;
            
            // Check if already finished this quiz
            $finished = SesiUjian::where('user_id', $user->id)
                ->where('bab_id', $bab->id)
                ->whereNotNull('finished_at')
                ->exists();
                
            if ($finished) {
                echo "  Already finished quiz: {$bab->title} in {$kelas->name}\n";
                continue;
            }
            
            echo "  Starting quiz: {$bab->title} in {$kelas->name}...\n";
            
            // Create session
            $sesi = SesiUjian::create([
                'user_id' => $user->id,
                'bab_id' => $bab->id,
                'started_at' => now(),
            ]);
            
            foreach ($bab->daftarSoal as $soal) {
                // Generate variables
                $variabel = $evaluasi->hasilkanVariabel($soal->variable_rules);
                
                // Calculate correct answer
                $correctAnswer = $evaluasi->hitungRumus($soal->answer_formula, $variabel);
                
                // Save answer (we simulate the student answering correctly)
                Jawaban::create([
                    'sesi_ujian_id' => $sesi->id,
                    'soal_id' => $soal->id,
                    'generated_variables' => $variabel,
                    'student_answer' => $correctAnswer,
                    'is_correct' => true,
                ]);
            }
            
            $sesi->update(['finished_at' => now()]);
            echo "    Quiz finished.\n";
        }
    }
}

echo "Done!\n";
