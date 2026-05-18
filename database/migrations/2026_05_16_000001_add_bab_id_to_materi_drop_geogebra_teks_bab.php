<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('materi')) {
            if (! Schema::hasColumn('materi', 'bab_id')) {
                Schema::table('materi', function (Blueprint $table) {
                    $table->foreignId('bab_id')->nullable()->after('kelas_id')->constrained('bab')->cascadeOnDelete();
                });
            }

            $babs = DB::table('bab')->select('id', 'kelas_id', 'order')->get();
            foreach ($babs as $bab) {
                DB::table('materi')
                    ->where('kelas_id', $bab->kelas_id)
                    ->where('order', $bab->order)
                    ->whereNull('bab_id')
                    ->update(['bab_id' => $bab->id]);
            }
        }

        if (Schema::hasTable('teks_bab') && Schema::hasColumn('teks_bab', 'geogebra_url')) {
            Schema::table('teks_bab', function (Blueprint $table) {
                $table->dropColumn('geogebra_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teks_bab') && ! Schema::hasColumn('teks_bab', 'geogebra_url')) {
            Schema::table('teks_bab', function (Blueprint $table) {
                $table->string('geogebra_url')->nullable();
            });
        }

        if (Schema::hasTable('materi') && Schema::hasColumn('materi', 'bab_id')) {
            Schema::table('materi', function (Blueprint $table) {
                $table->dropForeign(['bab_id']);
                $table->dropColumn('bab_id');
            });
        }
    }
};
