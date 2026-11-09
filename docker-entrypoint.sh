#!/usr/bin/env bash
set -e

# Pastikan direktori storage dan bootstrap writable
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Generate APP_KEY jika belum terdefinisi di environment
if [ -z "$APP_KEY" ]; then
    echo ">> APP_KEY belum disetel. Men-generate APP_KEY otomatis..."
    php artisan key:generate --force
fi

# Tautkan public storage
php artisan storage:link || true

# Bersihkan cache lama agar variabel environment terbaru dari Railway terbaca segar
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Jalankan migrasi tabel database
echo ">> Menjalankan migrasi database..."
php artisan migrate --force || echo ">> Catatan: Migrasi belum selesai. Pastikan MySQL di Railway sudah aktif."

# Pasang data awal (guru, siswa, kelas, bab, soal)
echo ">> Menjalankan seeding data awal..."
php artisan db:seed --force || true

PORT_TO_USE="${PORT:-8000}"
echo ">> LMS Matematika online di port ${PORT_TO_USE}!"

exec php artisan serve --host=0.0.0.0 --port="${PORT_TO_USE}"
