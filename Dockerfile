# =====================================================================
#  Dockerfile Dapurku = "resep" untuk membuat IMAGE aplikasi ini.
#  Dibaca dari atas ke bawah. Setiap baris = satu langkah (layer).
#
#  Dipakai lewat docker-compose (lihat compose.yaml):
#      docker compose up -d --build
# =====================================================================


# ---------------------------------------------------------------------
#  TAHAP 1: pasang library PHP (folder vendor/)
#  Pakai image resmi "composer" yang sudah berisi Composer.
#  Tahap ini hanya "dapur persiapan"; hasilnya (vendor/) nanti disalin
#  ke tahap 2, dan Composer-nya sendiri TIDAK ikut ke image akhir.
#  Teknik ini disebut "multi-stage build": image akhir jadi lebih kecil.
# ---------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

# Salin daftar paket DULU, baru install.
# Kenapa? Docker menyimpan hasil tiap langkah (cache). Selama composer.json/lock
# tidak berubah, langkah "composer install" yang lama ini tidak diulang.
COPY composer.json composer.lock ./

# --no-dev       : tanpa paket khusus development (phpunit, pint, dll.)
# --no-scripts   : jangan jalankan artisan dulu, kodenya belum disalin
# --no-autoloader: autoloader dibuat nanti setelah semua kode ada
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Sekarang baru salin seluruh kode (kecuali yang ada di .dockerignore)
COPY . .

# Buat autoloader (peta class → file) versi cepat untuk production
RUN composer dump-autoload --optimize --no-dev --no-scripts


# ---------------------------------------------------------------------
#  TAHAP 2: image akhir = PHP 8.3 + web server Apache
#  Versi PHP sengaja sama dengan Laragon di laptop (8.3).
# ---------------------------------------------------------------------
FROM php:8.3-apache

# Ekstensi PHP tambahan:
#  - pdo_mysql : supaya Laravel bisa bicara dengan MySQL
#  - opcache   : menyimpan hasil kompilasi PHP di memori → lebih cepat
RUN docker-php-ext-install pdo_mysql opcache

# mod_rewrite dibutuhkan file public/.htaccess milik Laravel
# (supaya /resep/rendang diarahkan ke index.php)
RUN a2enmod rewrite

# Beri nama server, supaya Apache tidak menampilkan peringatan "ServerName" di log
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf && a2enconf servername

# Bawaan Apache membuka folder /var/www/html.
# Laravel harus dibuka dari folder public/ (sama seperti di Laragon).
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html

# Ambil kode + vendor/ hasil TAHAP 1
COPY --from=vendor /app /var/www/html

# Daftarkan service provider dari paket (Livewire, dll.)
RUN php artisan package:discover --ansi

# Apache berjalan sebagai user "www-data", jadi dia harus boleh menulis
# ke folder storage/ (log, sesi, cache, foto upload) dan bootstrap/cache/
RUN chown -R www-data:www-data storage bootstrap/cache

# Skrip yang dijalankan setiap container menyala (migrate, seed, dll.)
# sed menghapus "\r" (akhir baris gaya Windows) supaya skrip bisa dibaca Linux
COPY docker/start.sh /usr/local/bin/dapurku-start
RUN sed -i 's/\r$//' /usr/local/bin/dapurku-start && chmod +x /usr/local/bin/dapurku-start

# Container membuka pintu (port) 80 untuk web
EXPOSE 80

# ENTRYPOINT = dijalankan duluan, lalu dia menjalankan CMD (Apache)
ENTRYPOINT ["dapurku-start"]
CMD ["apache2-foreground"]
