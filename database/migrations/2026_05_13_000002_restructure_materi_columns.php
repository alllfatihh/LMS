<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('materi')) {
            return;
        }

        if (Schema::hasColumn('materi', 'title') && ! Schema::hasColumn('materi', 'judul')) {
            Schema::table('materi', function (Blueprint $table) {
                $table->renameColumn('title', 'judul');
            });
        }

        if (Schema::hasColumn('materi', 'content') && ! Schema::hasColumn('materi', 'konten_html')) {
            Schema::table('materi', function (Blueprint $table) {
                $table->renameColumn('content', 'konten_html');
            });
        }

        Schema::table('materi', function (Blueprint $table) {
            if (! Schema::hasColumn('materi', 'ringkasan')) {
                $table->text('ringkasan')->nullable()->after('judul');
            }
            if (! Schema::hasColumn('materi', 'tipe_media')) {
                $table->string('tipe_media', 32)->default('teks')->after('ringkasan');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('materi')) {
            return;
        }

        Schema::table('materi', function (Blueprint $table) {
            if (Schema::hasColumn('materi', 'tipe_media')) {
                $table->dropColumn('tipe_media');
            }
            if (Schema::hasColumn('materi', 'ringkasan')) {
                $table->dropColumn('ringkasan');
            }
        });

        if (Schema::hasColumn('materi', 'konten_html') && ! Schema::hasColumn('materi', 'content')) {
            Schema::table('materi', function (Blueprint $table) {
                $table->renameColumn('konten_html', 'content');
            });
        }

        if (Schema::hasColumn('materi', 'judul') && ! Schema::hasColumn('materi', 'title')) {
            Schema::table('materi', function (Blueprint $table) {
                $table->renameColumn('judul', 'title');
            });
        }
    }
};
