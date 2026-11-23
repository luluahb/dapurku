<div align="center">

# 🍳 Dapurku

**Website resep masakan rumahan: cari resep berdasarkan bahan yang ada di dapurmu.**

[![Demo](https://img.shields.io/badge/demo-dapurku.wasmer.app-fb5d12?style=for-the-badge)](https://dapurku.wasmer.app)

![Laravel](https://img.shields.io/badge/Laravel_13-FF2D20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire_4-4E56A6?logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS_4-06B6D4?logo=tailwindcss&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?logo=alpinedotjs&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=white)
![Gemini](https://img.shields.io/badge/Google_Gemini-8E75B2?logo=googlegemini&logoColor=white)
![Tests](https://img.shields.io/badge/tests-44_lulus-22a05a)

<img src="docs/screenshots/beranda.png" alt="Halaman beranda Dapurku" width="100%">

</div>

## Tentang proyek

Dapurku adalah website resep yang dibuat dengan **Laravel**, **Livewire**, dan **Tailwind CSS**. Pengunjung bisa mencari resep, termasuk berdasarkan **bahan** yang mereka punya (misalnya "santan" atau "telur"), menyimpan resep favorit, dan memberi ulasan, semuanya **tanpa perlu membuat akun**. Pengelola mengatur resep lewat dashboard admin.

Proyek ini saya buat sebagai proyek portofolio untuk belajar membangun aplikasi web lengkap dengan Laravel: dari desain antarmuka, database, fitur interaktif, keamanan, testing, sampai deploy.

**🔗 Demo:** [dapurku.wasmer.app](https://dapurku.wasmer.app)

## Fitur

### 🧑‍🍳 Chef AI: asisten masak berbasis AI
Ceritakan bahan yang kamu punya ("ada telur dan santan, masak apa?"), dan Chef AI merekomendasikan resep **yang benar-benar ada di Dapurku**, lengkap dengan kartu resepnya.
- **Widget chat di setiap halaman**: tombol 🧑‍🍳 di pojok kanan bawah membuka panel chat (jendela kecil di laptop, layar penuh di HP). Obrolan disimpan di session, jadi tidak hilang saat pindah ke halaman resep.
- **RAG (Retrieval-Augmented Generation)**: setiap resep diubah menjadi *embedding* (Gemini Embedding), lalu dicari yang maknanya paling mirip dengan pertanyaan (*cosine similarity*). Resep-resep itu diberikan ke Gemini sebagai bahan bacaan, sehingga jawabannya tidak mengarang.
- **Pencarian berdasarkan makna**: "makanan berkuah hangat buat musim hujan" menemukan sup, laksa, dan bubur walaupun kata-katanya tidak ada di judul.
- **Ingat obrolan**: pertanyaan lanjutan seperti "kalau yang tidak pakai santan?" tetap dipahami.
- **Jawaban terstruktur (JSON schema)**, dan id resep dari AI divalidasi ulang ke database untuk membuang halusinasi.
- **Tahan banting**: coba ulang otomatis dan **model cadangan** saat server Gemini sibuk (HTTP 503), pesan ramah saat gagal, serta *guardrail* terhadap pertanyaan di luar topik dan *prompt injection*.
- **Hemat kuota gratis**: vector resep disimpan di cache dan otomatis diperbarui saat resep diubah, serta dibatasi 8 pertanyaan per menit dan 40 per hari untuk setiap pengunjung.

### Untuk pengunjung
- 🔍 **Pencarian langsung**: hasil berubah saat mengetik, tanpa reload. Mencari di judul, deskripsi, **dan daftar bahan**.
- 🗂️ **Filter dan urutan**: kategori, tingkat kesulitan, terbaru, terpopuler, rating tertinggi, tercepat dimasak.
- ♾️ **Infinite scroll**: resep berikutnya dimuat otomatis saat digulir.
- ✅ **Checklist bahan dan langkah memasak** dengan progress bar, tetap tersimpan walau halaman di-refresh.
- ❤️ **Suka**: jumlahnya tersimpan di database, dan satu sesi browser hanya bisa menambah satu suka per resep.
- 🔖 **Simpan ke Favoritku**: tersimpan di browser, tanpa akun.
- ⭐ **Ulasan dan rating bintang**: terkirim tanpa reload, lengkap dengan grafik sebaran bintang.
- 🖨️ **Cetak resep** dengan tata letak khusus kertas, dan 🔗 **bagikan** lewat menu bagikan bawaan HP.
- ✨ **Animasi perpindahan halaman**: foto resep "terbang" dari kartu ke halaman detail (View Transitions).
- 📱 **Nyaman di HP**: efek saat kartu dan menu ditekan, penanda menu halaman aktif, dan footer yang selalu di dasar layar.
- 🌙 **Mode gelap**, dan semua animasi menghormati pengaturan "kurangi animasi".

### Untuk admin
- 🔐 Login khusus admin (tanpa halaman daftar publik), dibatasi 5 percobaan per menit.
- ✍️ Tambah, edit, dan hapus resep dengan **upload foto + pratinjau**.
- 🧺 Daftar bahan dan langkah yang bisa **ditambah, dihapus, dan diurutkan** langsung di form.
- 🗑️ Menghapus komentar yang tidak pantas.

## Tampilan

<p align="center"><img src="docs/screenshots/chef-ai.png" alt="Chef AI merekomendasikan resep dari bahan yang dimiliki pengguna" width="100%"></p>

| Detail resep | Jelajah resep |
|:---:|:---:|
| <img src="docs/screenshots/detail-resep.png" alt="Halaman detail resep"> | <img src="docs/screenshots/jelajah.png" alt="Halaman jelajah resep dengan pencarian"> |
| **Mode gelap** | **Dashboard admin** |
| <img src="docs/screenshots/mode-gelap.png" alt="Beranda dalam mode gelap"> | <img src="docs/screenshots/admin.png" alt="Dashboard admin untuk mengelola resep"> |

<p align="center"><img src="docs/screenshots/tampilan-hp.png" alt="Tampilan Dapurku di HP" width="85%"></p>

## Teknologi

| Bagian | Teknologi |
|---|---|
| Backend | PHP 8.3, Laravel 13 |
| Interaksi tanpa reload | Livewire 4 (pencarian, filter, infinite scroll, komentar, Favoritku) |
| Interaksi kecil di browser | Alpine.js (menu, mode gelap, checklist, tombol suka dan simpan) |
| Tampilan | Tailwind CSS 4 dengan sistem desain sendiri (warna, font, animasi) |
| Database | MySQL |
| AI | Google Gemini API (`gemini-3.1-flash-lite` + `gemini-embedding-001`), RAG dengan cosine similarity |
| Testing | PHPUnit (44 test, Gemini dipalsukan dengan `Http::fake`) |
| Container | Docker + Docker Compose (PHP 8.3 Apache, MySQL 8.4) |
| Hosting | Wasmer Edge |

## Hal teknis yang menarik

- **Pencarian berdasarkan bahan**: query dengan `orWhereHas('bahan', ...)`, jadi mengetik "santan" menemukan resep yang memakai santan walaupun tidak tertulis di judulnya.
- **Local scope** `Resep::untukKartu()`: satu tempat untuk eager loading penulis, kategori, rata-rata rating (`withAvg`), dan jumlah komentar (`withCount`), supaya tidak terjadi masalah N+1 query.
- **Slug otomatis** dari judul lewat model event `creating`, dan route model binding memakai slug (`/resep/rendang-daging-sapi`).
- **Fitur tanpa akun yang tetap jujur**: suka dicatat di session supaya tidak bisa ditambah berkali-kali, dan dibatasi dengan rate limiting. Komentar dibatasi 3 per menit per alamat IP.
- **Keamanan**: Gate `admin`, `is_admin` sengaja tidak bisa diisi lewat form (mass assignment), CSRF untuk request dari JavaScript, dan `DB::transaction` saat menyimpan resep beserta bahan dan langkahnya.
- **SEO dan pratinjau link**: meta description dan Open Graph per halaman, jadi link resep yang dibagikan ke WhatsApp tampil dengan foto dan deskripsinya.
- **View Transitions lintas halaman** (`@view-transition`): foto pada kartu yang diklik diberi `view-transition-name` lewat JavaScript, sehingga browser menganimasikannya ke foto besar di halaman detail. Browser yang belum mendukung tetap membuka halaman secara normal.
- **Desain untuk layar sentuh**: setiap efek `hover:` punya pasangan `active:` / `has-[a:active]:`, karena di HP tidak ada hover. Deretan kategori yang bisa digeser memakai `snap-x` + `scroll-px-4` supaya kartunya berhenti rapi di margin.
- **Halaman error** (404, 403, 419, 429, 500) dengan desain yang konsisten.

## Menjalankan di komputer sendiri

### Cara cepat: dengan Docker 🐳

Hanya butuh [Docker Desktop](https://www.docker.com/products/docker-desktop/). PHP, MySQL, dan Composer tidak perlu dipasang.

```bash
git clone https://github.com/luluahb/dapurku.git
cd dapurku
docker compose up -d --build
```

Buka `http://localhost:8080`. Saat container menyala, tabel dibuat dan data contoh diisi otomatis (lihat [`docker/start.sh`](docker/start.sh)). Login admin di `/admin/login` dengan `admin@dapurku.test` / `password123`, atau isi `ADMIN_EMAIL`, `ADMIN_PASSWORD`, dan `APP_KEY` di file `.env` untuk menggantinya. Untuk mengaktifkan Chef AI, isi juga `GEMINI_API_KEY` (gratis di [Google AI Studio](https://aistudio.google.com/apikey)).

| Perintah | Fungsi |
|---|---|
| `docker compose logs -f app` | Melihat log aplikasi |
| `docker compose down` | Mematikan container (data tetap tersimpan di volume) |
| `docker compose down -v` | Mematikan dan menghapus data database serta foto |

Isinya: dua container, yaitu `app` (PHP 8.3 + Apache, dibuat lewat [`Dockerfile`](Dockerfile) multi-stage) dan `db` (MySQL 8.4), diatur di [`compose.yaml`](compose.yaml).

### Cara manual: tanpa Docker

**Yang dibutuhkan:** PHP 8.3+, Composer, Node.js, dan MySQL (misalnya lewat [Laragon](https://laragon.org)).

```bash
# 1. Ambil kode
git clone https://github.com/luluahb/dapurku.git
cd dapurku

# 2. Pasang dependensi
composer install
npm install

# 3. Siapkan pengaturan
cp .env.example .env
php artisan key:generate
```

Buat database MySQL bernama `dapurku`, lalu sesuaikan bagian database di `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=dapurku
DB_USERNAME=root
DB_PASSWORD=

# Opsional, untuk Chef AI (key gratis di https://aistudio.google.com/apikey)
GEMINI_API_KEY=
```

```bash
# 4. Buat tabel dan isi data contoh (14 resep, 8 kategori, akun admin)
php artisan migrate --seed

# 5. Sambungkan folder upload foto
php artisan storage:link

# 6. Susun CSS dan JavaScript, lalu jalankan
npm run build
php artisan serve
```

Buka `http://localhost:8000`. Untuk masuk sebagai admin, buka `/admin/login` dan pakai `ADMIN_EMAIL` serta `ADMIN_PASSWORD` dari file `.env`.

### Menjalankan test

```bash
php artisan test
```

Test memakai database SQLite sementara di memori, jadi data di MySQL tidak tersentuh.

## Struktur penting

```
app/
├── Http/Controllers/
│   ├── HalamanController.php        # Beranda
│   ├── ResepController.php          # Detail resep + tombol suka
│   └── Admin/                       # Login admin + CRUD resep
├── Livewire/
│   ├── Jelajah.php                  # Pencarian, filter, infinite scroll
│   ├── KomentarResep.php            # Ulasan & rating
│   ├── Favoritku.php                # Resep yang disimpan
│   └── ChefAi.php                   # Widget chat Chef AI (pojok kanan bawah)
├── Services/
│   ├── ChefAi.php                   # Otak RAG: embedding, pencarian makna, prompt
│   └── Gemini.php                   # Komunikasi dengan Gemini API (retry + model cadangan)
└── Models/                          # Resep, Kategori, Bahan, Langkah, Komentar, User
resources/
├── css/app.css                      # Sistem desain (warna, font, animasi)
├── js/app.js                        # Komponen Alpine: tombol suka & simpan
└── views/                           # Blade: layout, komponen, halaman
tests/Feature/                       # 44 test otomatis
```

## Kredit

Foto resep contoh berasal dari [TheMealDB](https://www.themealdb.com). Font: Plus Jakarta Sans dan Fraunces.

---

<div align="center">
Dibuat oleh <a href="https://github.com/luluahb">@luluahb</a> dengan ❤️ dan sedikit garam.
</div>
