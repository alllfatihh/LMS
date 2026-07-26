<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Struktur tabel untuk menampung hasil pemrosesan AI Llama 3
        Schema::create('soal', function (Blueprint $table) {
            $table->id();

            // Relasi ke bab materi (Kunci Tamu / Foreign Key)
            $table->foreignId('bab_id')->nullable()->constrained('bab')->onDelete('cascade');

            // Teks soal mentah yang mengandung variabel, contoh: "Berapa hasil {x} + {y}?"
            $table->text('question_template');

            // Menyimpan aturan batas acak AI dalam format JSON murni
            // Contoh isi: {"x": {"min": 1, "max": 10}, "y": {"min": 5, "max": 15}}
            $table->json('variable_rules');

            // Menyimpan rumus matematika dari AI untuk dikalkulasi MathExecutor
            // Contoh isi: "{x} + {y}"
            $table->string('answer_formula');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
