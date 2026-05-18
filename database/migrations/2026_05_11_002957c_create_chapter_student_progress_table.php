<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kemajuan_bab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bab_id')->constrained('bab')->onDelete('cascade');
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();
            $table->unique(['user_id', 'bab_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kemajuan_bab');
    }
};
