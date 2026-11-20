<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResepSeeder extends Seeder
{
    public function run(): void
    {
        // ===== 1. User contoh (penulis resep) =====
        // Semua password: password123. Nanti bisa dipakai login setelah halaman login dibuat.
        $penulis = [];
        foreach (['Lia', 'Budi', 'Sari', 'Dimas', 'Rina', 'Oyen'] as $nama) {
            $penulis[$nama] = User::create([
                'name' => $nama,
                'email' => Str::lower($nama) . '@contoh.test',
                'password' => 'password123', // otomatis di-hash oleh model User
            ]);
        }

        // Ambil semua kategori, lalu jadikan "kamus" berdasarkan slug: ['sarapan' => Kategori, ...]
        $kategori = Kategori::all()->keyBy('slug');

        // ===== 2. Data resep =====
        $foto = 'https://www.themealdb.com/images/media/meals/';

        $daftarResep = [
            [
                'judul' => 'Nasi Lemak Komplit dengan Sambal Ikan Bilis',
                'kategori' => 'makanan-utama', 'penulis' => 'Lia', 'foto' => 'wai9bw1619788844.jpg',
                'waktu' => 60, 'porsi' => 4, 'kesulitan' => 'Sedang',
                'deskripsi' => 'Nasi gurih santan dengan sambal ikan bilis pedas manis, telur rebus, kacang goreng, dan timun. Sarapan khas yang mengenyangkan.',
                'bahan' => ['400 gr beras', '400 ml santan', '2 lembar daun pandan, simpul', '1 batang serai, memarkan', '100 gr ikan bilis (teri), goreng', '10 cabai merah kering, rendam', '5 siung bawang merah', '4 butir telur rebus', 'Kacang tanah goreng dan timun secukupnya'],
                'langkah' => ['Cuci beras, lalu masak bersama santan, daun pandan, serai, dan sedikit garam sampai matang.', 'Haluskan cabai kering dan bawang merah, lalu tumis sampai harum dan berminyak.', 'Masukkan ikan bilis, beri gula dan garam, aduk sampai sambal mengental.', 'Sajikan nasi dengan sambal, telur rebus, kacang goreng, dan irisan timun.'],
            ],
            [
                'judul' => 'Rendang Daging Sapi Empuk Bumbu Meresap',
                'kategori' => 'makanan-utama', 'penulis' => 'Budi', 'foto' => 'bc8v651619789840.jpg',
                'waktu' => 180, 'porsi' => 6, 'kesulitan' => 'Sulit',
                'deskripsi' => 'Rendang khas Minang yang dimasak perlahan sampai bumbunya kering dan meresap ke dalam daging. Butuh kesabaran, tapi hasilnya sepadan.',
                'bahan' => ['1 kg daging sapi, potong besar', '1 liter santan kental', '2 lembar daun kunyit', '4 lembar daun jeruk', '2 batang serai, memarkan', '15 butir bawang merah', '8 siung bawang putih', '15 cabai merah keriting', '3 cm jahe dan 3 cm lengkuas', 'Garam secukupnya'],
                'langkah' => ['Haluskan bawang merah, bawang putih, cabai, jahe, dan lengkuas.', 'Didihkan santan bersama bumbu halus, daun kunyit, daun jeruk, dan serai sambil terus diaduk.', 'Masukkan daging, masak dengan api kecil sambil sesekali diaduk sampai santan menyusut.', 'Terus masak sampai bumbu mengering dan berwarna cokelat gelap, sekitar 2,5–3 jam.'],
            ],
            [
                'judul' => 'Mi Goreng Mamak Pedas Manis',
                'kategori' => 'mi-pasta', 'penulis' => 'Sari', 'foto' => 'xquakq1619787532.jpg',
                'waktu' => 25, 'porsi' => 2, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Mi kuning goreng dengan tahu, kentang, dan sayuran, dibumbui kecap dan saus cabai. Cepat dibuat untuk makan malam.',
                'bahan' => ['300 gr mi kuning basah', '1 buah tahu, potong dadu, goreng', '1 buah kentang rebus, potong dadu', '1 genggam tauge', '2 butir telur', '3 siung bawang putih, cincang', '2 sdm kecap manis', '2 sdm saus cabai'],
                'langkah' => ['Tumis bawang putih sampai harum, lalu masukkan telur dan orak-arik.', 'Masukkan mi, tahu, dan kentang, lalu aduk rata.', 'Tambahkan kecap manis dan saus cabai, aduk dengan api besar.', 'Masukkan tauge, aduk sebentar, lalu angkat dan sajikan dengan perasan jeruk nipis.'],
            ],
            [
                'judul' => 'Ayam Percik Bakar Kuah Santan',
                'kategori' => 'makanan-utama', 'penulis' => 'Dimas', 'foto' => '020z181619788503.jpg',
                'waktu' => 75, 'porsi' => 4, 'kesulitan' => 'Sedang',
                'deskripsi' => 'Ayam bakar yang dilumuri kuah santan berbumbu berkali-kali selama dibakar. Gurih, sedikit pedas, dan wangi rempah.',
                'bahan' => ['1 ekor ayam, potong 4', '300 ml santan kental', '8 butir bawang merah', '4 siung bawang putih', '5 cabai merah', '1 batang serai', '1 sdt kunyit bubuk', '1 sdm gula merah'],
                'langkah' => ['Haluskan bawang, cabai, serai, dan kunyit.', 'Masak bumbu halus bersama santan dan gula merah sampai mengental.', 'Lumuri ayam dengan sebagian kuah, diamkan 30 menit.', 'Bakar ayam sambil terus diolesi kuah sampai matang dan kecokelatan.'],
            ],
            [
                'judul' => 'Laksa Udang Kuah Kari',
                'kategori' => 'sup-soto', 'penulis' => 'Rina', 'foto' => 'rvypwy1503069308.jpg',
                'waktu' => 45, 'porsi' => 3, 'kesulitan' => 'Sedang',
                'deskripsi' => 'Mi dalam kuah kari santan yang kental, dengan udang besar, tauge, dan telur. Hangat dan kaya rasa.',
                'bahan' => ['300 gr mi bihun atau mi kuning', '250 gr udang besar', '500 ml santan', '500 ml kaldu ayam', '3 sdm bumbu laksa atau kari', '1 genggam tauge', '2 butir telur rebus', 'Daun kemangi secukupnya'],
                'langkah' => ['Tumis bumbu laksa sampai harum.', 'Tuang kaldu dan santan, lalu didihkan sambil diaduk.', 'Masukkan udang, masak sampai berubah warna.', 'Siram kuah ke atas mi dan tauge, lalu beri telur dan kemangi.'],
            ],
            [
                'judul' => 'Roti John Telur Daging',
                'kategori' => 'sarapan', 'penulis' => 'Oyen', 'foto' => 'hx335q1619789561.jpg',
                'waktu' => 20, 'porsi' => 2, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Roti panjang yang dipanggang bersama telur dan daging cincang, lalu diberi saus. Sarapan praktis yang mengenyangkan.',
                'bahan' => ['2 buah roti panjang (baguette)', '3 butir telur', '150 gr daging sapi cincang', '1 buah bawang bombay, cincang', 'Saus sambal dan mayones', 'Margarin secukupnya'],
                'langkah' => ['Kocok telur bersama daging cincang, bawang bombay, garam, dan merica.', 'Belah roti memanjang tanpa terputus.', 'Panaskan margarin, tuang adonan telur, lalu tempelkan sisi dalam roti di atasnya.', 'Balik setelah telur matang, lalu beri saus sambal dan mayones.'],
            ],
            [
                'judul' => 'Martabak Manis Mini (Apam Balik)',
                'kategori' => 'kue-dessert', 'penulis' => 'Lia', 'foto' => 'adxcbq1619787919.jpg',
                'waktu' => 30, 'porsi' => 4, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Martabak manis tipis dan renyah dengan isian kacang, gula, dan jagung. Cocok untuk camilan sore.',
                'bahan' => ['200 gr tepung terigu', '1 butir telur', '250 ml susu cair', '2 sdm gula pasir', '1/2 sdt baking soda', 'Kacang tanah sangrai cincang', 'Jagung manis pipil', 'Mentega secukupnya'],
                'langkah' => ['Campur tepung, telur, susu, gula, dan baking soda sampai rata. Diamkan 15 menit.', 'Panaskan teflon, tuang adonan tipis, lalu tutup sampai permukaannya berlubang.', 'Taburi gula, kacang, dan jagung, lalu oles mentega.', 'Lipat dua dan sajikan selagi hangat.'],
            ],
            [
                'judul' => 'Kue Seri Muka Pandan',
                'kategori' => 'kue-dessert', 'penulis' => 'Rina', 'foto' => '6ut2og1619790195.jpg',
                'waktu' => 90, 'porsi' => 8, 'kesulitan' => 'Sulit',
                'deskripsi' => 'Kue dua lapis: ketan gurih di bawah dan srikaya pandan yang lembut di atas. Kue tradisional untuk acara keluarga.',
                'bahan' => ['300 gr beras ketan, rendam 3 jam', '250 ml santan', '3 butir telur', '150 gr gula pasir', '50 gr tepung terigu', '100 ml air daun pandan', 'Garam secukupnya'],
                'langkah' => ['Kukus ketan 20 menit, aduk dengan santan dan garam, lalu kukus lagi 10 menit.', 'Padatkan ketan di loyang.', 'Kocok telur dan gula, lalu masukkan tepung, santan, dan air pandan.', 'Tuang adonan pandan di atas ketan, kukus dengan api kecil 30 menit.'],
            ],
            [
                'judul' => 'Pancake Pisang Tanpa Mixer',
                'kategori' => 'sarapan', 'penulis' => 'Sari', 'foto' => 'sywswr1511383814.jpg',
                'waktu' => 15, 'porsi' => 2, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Pancake lembut dari pisang matang, cukup diaduk pakai garpu. Sarapan manis yang cepat dan hemat.',
                'bahan' => ['2 buah pisang matang', '2 butir telur', '4 sdm tepung terigu', '1/2 sdt baking powder', 'Madu untuk siraman'],
                'langkah' => ['Lumatkan pisang dengan garpu.', 'Masukkan telur, tepung, dan baking powder, lalu aduk rata.', 'Panggang adonan di teflon dengan api kecil sampai kedua sisi kecokelatan.', 'Sajikan dengan siraman madu.'],
            ],
            [
                'judul' => 'Sup Telur Jagung Hangat',
                'kategori' => 'sup-soto', 'penulis' => 'Budi', 'foto' => '1529446137.jpg',
                'waktu' => 20, 'porsi' => 3, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Sup bening dengan helaian telur yang lembut dan jagung manis. Cocok dimakan saat cuaca dingin atau badan kurang fit.',
                'bahan' => ['1 liter kaldu ayam', '1 kaleng jagung krim', '2 butir telur, kocok', '2 sdm tepung maizena, larutkan', '2 batang daun bawang, iris', 'Garam dan merica'],
                'langkah' => ['Didihkan kaldu bersama jagung krim.', 'Masukkan larutan maizena sambil diaduk sampai kuah agak kental.', 'Tuang telur kocok perlahan sambil kuah diaduk memutar.', 'Bumbui garam dan merica, lalu taburi daun bawang.'],
            ],
            [
                'judul' => 'Pisang Goreng Krispi',
                'kategori' => 'camilan', 'penulis' => 'Oyen', 'foto' => 'ul2uy31764794321.jpg',
                'waktu' => 20, 'porsi' => 4, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Pisang goreng dengan balutan tepung yang renyah lama. Teman ngeteh paling cocok.',
                'bahan' => ['6 buah pisang kepok', '100 gr tepung terigu', '2 sdm tepung beras', '1 sdm gula pasir', '150 ml air dingin', 'Minyak untuk menggoreng'],
                'langkah' => ['Campur tepung terigu, tepung beras, gula, sejumput garam, dan air dingin.', 'Belah pisang, lalu celupkan ke adonan.', 'Goreng dalam minyak panas sampai kuning keemasan.', 'Tiriskan dan sajikan hangat.'],
            ],
            [
                'judul' => 'Bakwan Sayur Renyah',
                'kategori' => 'camilan', 'penulis' => 'Dimas', 'foto' => 'xjii2g1784836867.jpg',
                'waktu' => 25, 'porsi' => 4, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Gorengan sayur favorit semua orang: wortel, kol, dan tauge dalam adonan tepung berbumbu.',
                'bahan' => ['1 buah wortel, serut', '100 gr kol, iris tipis', '1 genggam tauge', '150 gr tepung terigu', '3 siung bawang putih, haluskan', '2 batang daun bawang, iris', 'Garam, merica, dan air secukupnya'],
                'langkah' => ['Campur semua sayuran dalam wadah.', 'Masukkan tepung, bawang putih, garam, dan merica.', 'Tambahkan air sedikit demi sedikit sampai adonan bisa diambil dengan sendok.', 'Goreng sesendok demi sesendok sampai renyah.'],
            ],
            [
                'judul' => 'Tumis Tahu Sayur Kacang Mete',
                'kategori' => 'sayuran', 'penulis' => 'Lia', 'foto' => 'minfsc1763766806.jpg',
                'waktu' => 20, 'porsi' => 2, 'kesulitan' => 'Mudah',
                'deskripsi' => 'Tumisan tahu dan sayuran hijau dengan kacang mete yang gurih. Sehat, cepat, dan tidak membosankan.',
                'bahan' => ['1 kotak tahu putih, potong dadu', '1 ikat pakcoy atau sawi', '1 buah brokoli kecil', '50 gr kacang mete sangrai', '3 siung bawang putih', '2 sdm kecap asin', '1 sdm saus tiram'],
                'langkah' => ['Goreng tahu sampai kulitnya kecokelatan, sisihkan.', 'Tumis bawang putih sampai harum.', 'Masukkan brokoli dan pakcoy, beri sedikit air.', 'Masukkan tahu, kecap asin, dan saus tiram, lalu taburi mete.'],
            ],
            [
                'judul' => 'Bubur Ayam Kampung',
                'kategori' => 'sarapan', 'penulis' => 'Budi', 'foto' => '1529446352.jpg',
                'waktu' => 60, 'porsi' => 4, 'kesulitan' => 'Sedang',
                'deskripsi' => 'Bubur nasi lembut dengan suwiran ayam, cakwe, kacang, dan kuah kuning. Sarapan andalan di pagi hari.',
                'bahan' => ['200 gr beras', '2 liter kaldu ayam', '1 dada ayam, rebus dan suwir', '2 lembar daun salam', 'Cakwe, kacang kedelai goreng, dan kerupuk', 'Kecap asin dan sambal'],
                'langkah' => ['Masak beras bersama kaldu dan daun salam dengan api kecil.', 'Aduk terus sampai beras hancur dan menjadi bubur, sekitar 45 menit.', 'Bumbui dengan garam.', 'Sajikan dengan ayam suwir, cakwe, kacang, kerupuk, kecap, dan sambal.'],
            ],
        ];

        // ===== 3. Simpan ke database =====
        $jumlah = count($daftarResep);

        foreach ($daftarResep as $i => $data) {
            $resep = new Resep([
                'user_id' => $penulis[$data['penulis']]->id,
                'kategori_id' => $kategori[$data['kategori']]->id,
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'],
                'foto' => $foto . $data['foto'],
                'waktu' => $data['waktu'],
                'porsi' => $data['porsi'],
                'kesulitan' => $data['kesulitan'],
                'dilihat' => random_int(20, 900),
            ]);

            // Buat tanggal "seolah-olah" ditulis beberapa hari lalu,
            // supaya urutan "Resep terbaru" terlihat wajar
            $resep->created_at = now()->subDays($jumlah - $i);
            $resep->save(); // slug dibuat otomatis oleh model (lihat booted() di Resep.php)

            // Simpan bahan & langkah. resep_id diisi otomatis karena lewat relasi.
            foreach ($data['bahan'] as $n => $isi) {
                $resep->bahan()->create(['urutan' => $n + 1, 'isi' => $isi]);
            }
            foreach ($data['langkah'] as $n => $isi) {
                $resep->langkah()->create(['urutan' => $n + 1, 'isi' => $isi]);
            }
        }
    }
}
