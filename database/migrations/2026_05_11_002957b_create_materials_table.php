<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teks_bab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bab_id')->constrained('bab')->onDelete('cascade');
            $table->text('content')->nullable();
            $table->string('geogebra_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teks_bab');
    }
};
