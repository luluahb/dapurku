#!/bin/sh
# =====================================================================
#  Dijalankan SETIAP KALI container Dapurku menyala, sebelum Apache.
#  Isinya pekerjaan yang dulu kita lakukan manual / lewat /_pasang di Wasmer.
# =====================================================================
set -e   # berhenti kalau ada perintah yang gagal

cd /var/www/html

# 1. APP_KEY wajib ada (untuk enkripsi sesi & cookie).
#    Kalau belum diisi di compose/.env, buat sementara supaya tetap bisa jalan.
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
    echo "⚠  APP_KEY kosong, memakai kunci sementara (login akan hilang saat container restart)."
fi

# 2. Pastikan folder-folder storage ada (sebagian tidak ikut disalin, lihat .dockerignore)
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
         storage/logs storage/app/public/resep

# 3. Buat/perbarui tabel. --force karena APP_ENV bisa "production".
php artisan migrate --force

# 4. Isi admin + data contoh. Aman diulang: seeder melewati data yang sudah ada.
php artisan db:seed --force

# 5. Sambungkan public/storage → storage/app/public (supaya foto upload bisa dibuka)
php artisan storage:link --force >/dev/null

# 6. Simpan config, route, dan view dalam bentuk cache → aplikasi lebih cepat
php artisan optimize

# 7. Perintah di atas dijalankan sebagai root; kembalikan kepemilikan ke Apache (www-data)
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Dapurku siap di http://localhost:8080"

# 8. Jalankan perintah utama (CMD di Dockerfile) = Apache.
#    "exec" membuat Apache menggantikan skrip ini sebagai proses utama container.
exec "$@"
