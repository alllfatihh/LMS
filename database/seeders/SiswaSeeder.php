<?php

namespace Database\Seeders;

use App\Models\Bab;
use App\Models\Jawaban;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\SesiUjian;
use App\Models\Soal;
use App\Models\User;
use App\Services\EvaluasiSoalService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mendaftarkan 30 akun siswa (siswa@example1.com s/d siswa@example30.com),
     * memasukkan ke seluruh kelas, dan membuat riwayat ujian acak (realistis)
     * dengan variabel acak (seed) valid sehingga audit ujian dapat merekonstruksi soal & kunci jawaban.
     */
    public function run(): void
    {
        $evaluasiService = app(EvaluasiSoalService::class);

        $daftarNama = [
            1 => 'Ahmad Fauzi',
            2 => 'Budi Santoso',
            3 => 'Citra Dewi',
            4 => 'Dimas Pratama',
            5 => 'Eka Rahmawati',
            6 => 'Fajar Nugraha',
            7 => 'Gita Permata',
            8 => 'Hadi Wijaya',
            9 => 'Indah Lestari',
            10 => 'Joko Susilo',
            11 => 'Kartika Sari',
            12 => 'Luthfi Hidayat',
            13 => 'Maya Anggraini',
            14 => 'Naufal Rizky',
            15 => 'Olivia Putri',
            16 => 'Panji Saputra',
            17 => 'Qori Aulia',
            18 => 'Rian Setiawan',
            19 => 'Siti Nurhaliza',
            20 => 'Taufik Hidayat',
            21 => 'Utami Ningsih',
            22 => 'Vina Panduwinata',
            23 => 'Wahyu Pratama',
            24 => 'Xena Zhafira',
            25 => 'Yoga Pratama',
            26 => 'Zahra Amelia',
            27 => 'Alif Ramadhan',
            28 => 'Bella Safitri',
            29 => 'Candra Kurniawan',
            30 => 'Dinda Kirana',
        ];

        $siswaModels = [];
        $passwordHash = Hash::make('password');

        for ($i = 1; $i <= 30; $i++) {
            $email = "siswa@example{$i}.com";
            $nama = $daftarNama[$i] ?? "Siswa {$i}";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => $passwordHash,
                    'role' => 'siswa',
                    'email_verified_at' => now(),
                ]
            );

            $siswaModels[] = $user;
        }

        $siswaDemo = User::where('email', 'siswa@example.com')->first();
        if ($siswaDemo) {
            $siswaModels[] = $siswaDemo;
        }

        $siswaIds = array_map(fn($s) => $s->id, $siswaModels);

        // Hapus sesi ujian dan jawaban lama siswa seeder agar tidak ada seed rusak
        $oldSesiIds = SesiUjian::whereIn('user_id', $siswaIds)->pluck('id')->toArray();
        if (!empty($oldSesiIds)) {
            Jawaban::whereIn('sesi_ujian_id', $oldSesiIds)->delete();
            SesiUjian::whereIn('id', $oldSesiIds)->delete();
        }

        // Bersihkan juga setiap jawaban dengan generated_variables null/kosong
        $badJwbIds = Jawaban::whereNull('generated_variables')
            ->orWhere('generated_variables', '[]')
            ->orWhere('generated_variables', '')
            ->pluck('id')
            ->toArray();
        if (!empty($badJwbIds)) {
            Jawaban::whereIn('id', $badJwbIds)->delete();
        }

        // Ambil semua kelas yang ada beserta bab dan soal
        $semuaKelas = Kelas::with(['bab.daftarSoal'])->get();

        foreach ($semuaKelas as $kelas) {
            // Sinkronkan seluruh siswa ke kelas
            $kelas->siswa()->syncWithoutDetaching($siswaIds);

            $daftarBab = $kelas->bab;
            if ($daftarBab->isEmpty()) {
                continue;
            }

            $totalBab = $daftarBab->count();

            foreach ($siswaModels as $index => $siswaUser) {
                $nomorUrut = $index + 1;

                // Tentukan profil performa siswa agar distribusi nilai dan rata-rata kelas realistis:
                // Profil A (Juara Kelas): 85 - 100
                // Profil B (Baik): 70 - 88
                // Profil C (Cukup): 55 - 75
                // Profil D (Perlu Bimbingan): 35 - 60
                if ($nomorUrut <= 8) {
                    $targetBenarPersenMin = 85;
                    $targetBenarPersenMax = 100;
                    $babSelesaiCount = rand(max(1, $totalBab - 2), $totalBab);
                } elseif ($nomorUrut <= 18) {
                    $targetBenarPersenMin = 70;
                    $targetBenarPersenMax = 88;
                    $babSelesaiCount = rand(max(1, (int) round($totalBab * 0.6)), $totalBab - 1);
                } elseif ($nomorUrut <= 26) {
                    $targetBenarPersenMin = 55;
                    $targetBenarPersenMax = 75;
                    $babSelesaiCount = rand(max(1, (int) round($totalBab * 0.4)), max(1, (int) round($totalBab * 0.7)));
                } else {
                    $targetBenarPersenMin = 40;
                    $targetBenarPersenMax = 65;
                    $babSelesaiCount = rand(1, max(1, (int) round($totalBab * 0.5)));
                }

                // Ambil sejumlah bab yang dikerjakan siswa
                $babDikerjakan = $daftarBab->take($babSelesaiCount);

                // Sinkronkan status bab dibaca untuk progress bar
                $babIdsDibaca = $babDikerjakan->pluck('id')->toArray();
                if (!empty($babIdsDibaca)) {
                    $siswaUser->babYangSudahDibaca()->syncWithoutDetaching($babIdsDibaca);
                }

                // Buat sesi ujian dan jawaban untuk setiap bab yang dikerjakan
                foreach ($babDikerjakan as $bIdx => $bab) {
                    $daftarSoal = $bab->daftarSoal;
                    if ($daftarSoal->isEmpty()) {
                        continue;
                    }

                    // Waktu pengerjaan mundur antara 1 jam sampai 7 hari yang lalu
                    $menitMundur = ($bIdx * 300) + rand(10, 10000);
                    $durasiPengerjaanMenit = rand(12, 38);

                    $startedAt = now()->subMinutes($menitMundur + $durasiPengerjaanMenit);
                    $finishedAt = (clone $startedAt)->addMinutes($durasiPengerjaanMenit);

                    $sesi = SesiUjian::create([
                        'user_id' => $siswaUser->id,
                        'bab_id' => $bab->id,
                        'started_at' => $startedAt,
                        'finished_at' => $finishedAt,
                    ]);

                    $totalSoalCount = $daftarSoal->count();
                    $targetPersen = rand($targetBenarPersenMin, $targetBenarPersenMax) / 100;
                    $jumlahBenarTarget = (int) round($totalSoalCount * $targetPersen);
                    $jumlahBenarTarget = max(1, min($totalSoalCount, $jumlahBenarTarget));

                    // Acak urutan soal mana saja yang dijawab benar
                    $soalIndices = range(0, $totalSoalCount - 1);
                    shuffle($soalIndices);
                    $soalBenarIndices = array_flip(array_slice($soalIndices, 0, $jumlahBenarTarget));

                    foreach ($daftarSoal as $sIdx => $soal) {
                        $isCorrect = isset($soalBenarIndices[$sIdx]);

                        // Hasilkan variabel acak (seed) valid dari aturan variabel
                        $rules = $soal->variable_rules ?? $soal->variable_ranges ?? [];
                        $generatedVars = [];
                        if (!empty($rules) && is_array($rules)) {
                            try {
                                $generatedVars = $evaluasiService->hasilkanVariabel($rules);
                            } catch (\Throwable $e) {
                                $generatedVars = [];
                            }
                        }

                        // Hitung kunci jawaban matematis yang valid
                        $kunci = null;
                        if (!empty($soal->answer_formula)) {
                            try {
                                $kunci = $evaluasiService->hitungRumus($soal->answer_formula, $generatedVars);
                            } catch (\Throwable $e) {
                                $kunci = null;
                            }
                        }

                        // Tentukan jawaban siswa yang realistis
                        if ($kunci !== null) {
                            if ($isCorrect) {
                                $studentAnswer = (string) $kunci;
                            } else {
                                if (is_numeric($kunci)) {
                                    $delta = rand(1, 3);
                                    $studentAnswer = (string) ($kunci + (rand(0, 1) ? $delta : -$delta));
                                } else {
                                    $studentAnswer = 'Jawaban Lain';
                                }
                            }
                        } else {
                            $studentAnswer = $isCorrect ? '10' : '0';
                        }

                        Jawaban::create([
                            'sesi_ujian_id' => $sesi->id,
                            'soal_id' => $soal->id,
                            'generated_variables' => $generatedVars,
                            'student_answer' => $studentAnswer,
                            'is_correct' => $isCorrect,
                        ]);
                    }
                }
            }
        }
    }
}
