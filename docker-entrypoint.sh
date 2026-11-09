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

# Jalankan migrasi tabel database
echo ">> Menjalankan migrasi database..."
php artisan migrate --force

# Pasang data awal (guru, siswa, kelas, bab, soal)
echo ">> Menjalankan seeding data awal..."
php artisan db:seed --force || true

# Cache konfigurasi dan view untuk kecepatan optimal
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

PORT_TO_USE="${PORT:-8000}"
echo ">> LMS Matematika online di port ${PORT_TO_USE}!"

exec php artisan serve --host=0.0.0.0 --port="${PORT_TO_USE}"
