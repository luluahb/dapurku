// =====================================================================
// JavaScript Dapurku
// Alpine.js sudah ikut terpasang bersama Livewire, jadi tidak perlu di-install.
// Di sini kita mendaftarkan "komponen Alpine" yang bisa dipakai berulang:
//   x-data="tombolSuka(...)"  dan  x-data="tombolSimpan(...)"
// =====================================================================

// Pembantu kecil untuk menyimpan daftar id resep di localStorage browser.
// try/catch: localStorage bisa diblokir (misalnya mode privat), jangan sampai halaman error.
const penyimpanan = {
    ambil(kunci) {
        try {
            return JSON.parse(localStorage.getItem(kunci)) ?? [];
        } catch {
            return [];
        }
    },
    simpan(kunci, daftar) {
        try {
            localStorage.setItem(kunci, JSON.stringify(daftar));
        } catch {
            // abaikan
        }
    },
};

// Token CSRF dari <meta name="csrf-token"> di layout. Wajib untuk request POST di Laravel.
const tokenCsrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

// "alpine:init" = acara saat Alpine siap. Di sinilah komponen didaftarkan.
document.addEventListener('alpine:init', () => {

    // ===== ❤️ TOMBOL SUKA =====
    // id: id resep, jumlahAwal: angka suka dari database, url: alamat POST suka
    window.Alpine.data('tombolSuka', (id, jumlahAwal, url) => ({
        jumlah: jumlahAwal,
        disukai: penyimpanan.ambil('dapurku-suka').includes(id),
        pop: false,

        async klik() {
            // 1. Ubah tampilan LANGSUNG, tanpa menunggu server (terasa cepat)
            this.disukai = !this.disukai;
            this.jumlah += this.disukai ? 1 : -1;
            this.pop = true;
            setTimeout(() => (this.pop = false), 300);

            // 2. Ingat pilihan ini di browser
            const daftar = penyimpanan.ambil('dapurku-suka').filter((i) => i !== id);
            if (this.disukai) daftar.push(id);
            penyimpanan.simpan('dapurku-suka', daftar);

            // 3. Kirim ke server dengan fetch (seperti pelajaran 12), lalu pakai angka dari server
            try {
                const jawaban = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': tokenCsrf(),
                    },
                    body: JSON.stringify({ aksi: this.disukai ? 'tambah' : 'kurang' }),
                });
                if (jawaban.ok) this.jumlah = (await jawaban.json()).suka;
            } catch {
                // Internet putus: biarkan angka di layar apa adanya
            }
        },
    }));

    // ===== 🔖 TOMBOL SIMPAN =====
    // Hanya disimpan di browser. Setiap berubah, kirim acara "simpan-berubah"
    // supaya bagian lain (angka di navbar, halaman Favoritku) ikut diperbarui.
    window.Alpine.data('tombolSimpan', (id) => ({
        tersimpan: penyimpanan.ambil('dapurku-simpan').includes(id),

        klik() {
            let daftar = penyimpanan.ambil('dapurku-simpan').filter((i) => i !== id);
            this.tersimpan = !this.tersimpan;
            if (this.tersimpan) daftar = [id, ...daftar]; // yang baru disimpan di paling depan
            penyimpanan.simpan('dapurku-simpan', daftar);

            window.dispatchEvent(new CustomEvent('simpan-berubah', { detail: daftar }));
        },

        // Kalau resep yang sama disimpan/dibatalkan dari tombol lain, ikut berubah
        sinkron(daftar) {
            this.tersimpan = daftar.includes(id);
        },
    }));

    // ===== Angka kecil di menu "Favoritku" =====
    window.Alpine.data('jumlahSimpan', () => ({
        jumlah: penyimpanan.ambil('dapurku-simpan').length,
    }));
});

// Dipakai halaman Favoritku untuk membaca daftar id yang tersimpan
window.ambilResepTersimpan = () => penyimpanan.ambil('dapurku-simpan');
