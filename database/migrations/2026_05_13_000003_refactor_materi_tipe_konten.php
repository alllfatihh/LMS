<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('materi')) {
            return;
        }

        Schema::table('materi', function (Blueprint $table) {
            if (! Schema::hasColumn('materi', 'tipe')) {
                $table->string('tipe', 16)->default('teks');
            }
            if (! Schema::hasColumn('materi', 'konten_teks')) {
                $table->longText('konten_teks')->nullable();
            }
            if (! Schema::hasColumn('materi', 'link_video')) {
                $table->string('link_video', 2048)->nullable();
            }
            if (! Schema::hasColumn('materi', 'file_path')) {
                $table->string('file_path', 2048)->nullable();
            }
        });

        if (Schema::hasColumn('materi', 'tipe_media') && Schema::hasColumn('materi', 'konten_html')) {
            $rows = DB::table('materi')->select('id', 'tipe_media', 'konten_html')->get();
            foreach ($rows as $row) {
                $media = (string) ($row->tipe_media ?? 'teks');
                $html = $row->konten_html;

                if ($media === 'video') {
                    $url = self::extractYoutubeFromHtml((string) $html);
                    DB::table('materi')->where('id', $row->id)->update([
                        'tipe' => 'video',
                        'konten_teks' => null,
                        'link_video' => $url,
                        'file_path' => null,
                    ]);
                } elseif ($media === 'geogebra' || $media === 'teks') {
                    DB::table('materi')->where('id', $row->id)->update([
                        'tipe' => 'teks',
                        'konten_teks' => $html,
                        'link_video' => null,
                        'file_path' => null,
                    ]);
                } else {
                    DB::table('materi')->where('id', $row->id)->update([
                        'tipe' => 'teks',
                        'konten_teks' => $html,
                        'link_video' => null,
                        'file_path' => null,
                    ]);
                }
            }
        }

        foreach (['konten_html', 'tipe_media'] as $kolom) {
            if (Schema::hasColumn('materi', $kolom)) {
                Schema::table('materi', function (Blueprint $table) use ($kolom) {
                    $table->dropColumn($kolom);
                });
            }
        }
    }

    private static function extractYoutubeFromHtml(string $html): ?string
    {
        if (preg_match('%(?:youtube\\.com/embed/|youtu\\.be/)([a-zA-Z0-9_-]{11})%', $html, $m)) {
            return 'https://www.youtube.com/watch?v='.$m[1];
        }
        if (preg_match('%youtube\\.com/watch\\?v=([a-zA-Z0-9_-]{11})%', $html, $m)) {
            return 'https://www.youtube.com/watch?v='.$m[1];
        }

        return null;
    }

    public function down(): void
    {
        if (! Schema::hasTable('materi')) {
            return;
        }

        Schema::table('materi', function (Blueprint $table) {
            if (Schema::hasColumn('materi', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('materi', 'link_video')) {
                $table->dropColumn('link_video');
            }
            if (Schema::hasColumn('materi', 'konten_teks')) {
                $table->dropColumn('konten_teks');
            }
            if (Schema::hasColumn('materi', 'tipe')) {
                $table->dropColumn('tipe');
            }
            if (! Schema::hasColumn('materi', 'konten_html')) {
                $table->longText('konten_html')->nullable();
            }
            if (! Schema::hasColumn('materi', 'tipe_media')) {
                $table->string('tipe_media', 32)->default('teks');
            }
        });
    }
};
