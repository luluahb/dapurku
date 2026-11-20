{{-- Halaman detail resep. Data dari ResepController@show: $resep, $resepLain --}}
{{-- description & gambar dipakai untuk pratinjau saat link resep ini dibagikan --}}
<x-layouts::app :title="$resep->judul" :description="$resep->deskripsi" :gambar="$resep->foto_url">

    @php
        $warnaKesulitan = match ($resep->kesulitan) {
            'Mudah' => 'bg-daun-100 text-daun-700',
            'Sedang' => 'bg-amber-100 text-amber-700',
            default => 'bg-rose-100 text-rose-700',
        };
    @endphp

    {{-- ===================== BAGIAN ATAS ===================== --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute -right-32 -top-32 size-[28rem] rounded-full bg-primer-300/30 blur-3xl dark:bg-primer-700/15"></div>

        {{-- pb-10: ruang di bawah tombol, supaya bayangannya tidak terpotong overflow-hidden --}}
        <div class="relative mx-auto max-w-6xl px-4 pb-10 pt-6 sm:px-6 print:pb-0">
            {{-- Breadcrumb: jejak lokasi halaman --}}
            <nav class="flex flex-wrap items-center gap-2 text-sm text-stone-500 print:hidden dark:text-stone-400">
                <a href="{{ route('beranda') }}" class="transition hover:text-primer-600 active:text-primer-600">Beranda</a>
                <span>/</span>
                <a href="{{ route('jelajah', ['kategori' => $resep->kategori->slug]) }}" class="transition hover:text-primer-600 active:text-primer-600">{{ $resep->kategori->nama }}</a>
                <span>/</span>
                <span class="line-clamp-1 font-medium text-stone-700 dark:text-stone-200">{{ $resep->judul }}</span>
            </nav>

            {{-- print:... = gaya khusus saat dicetak: foto kecil di kiri, info di kanan --}}
            <div class="mt-6 grid items-center gap-10 lg:grid-cols-2 print:mt-0 print:grid-cols-[14rem_1fr] print:items-start print:gap-6">
                {{-- Foto besar --}}
                <div class="relative">
                    {{-- view-transition-name: tujuan animasi "foto terbang" dari kartu resep yang diklik --}}
                    <div style="view-transition-name: foto-resep" class="aspect-[4/3] overflow-hidden rounded-[2rem] bg-primer-50 shadow-2xl shadow-stone-300/50 print:rounded-2xl print:shadow-none dark:bg-stone-800 dark:shadow-none">
                        @if ($resep->foto_url)
                            <img src="{{ $resep->foto_url }}" alt="{{ $resep->judul }}" class="size-full object-cover">
                        @else
                            <span class="grid size-full place-items-center text-9xl">{{ $resep->kategori->ikon }}</span>
                        @endif
                    </div>
                    {{-- Stiker kategori yang melayang --}}
                    <span style="--putar: -6deg" class="absolute -bottom-5 -left-3 grid size-20 print:hidden animate-melayang place-items-center rounded-3xl bg-white text-4xl shadow-xl sm:-left-5 dark:bg-stone-900">
                        {{ $resep->kategori->ikon }}
                    </span>
                </div>

                {{-- Info resep --}}
                <div>
                    <span class="inline-block rounded-full bg-primer-100 px-3 py-1 text-sm font-semibold text-primer-700 dark:bg-primer-500/15 dark:text-primer-300">
                        {{ $resep->kategori->nama }}
                    </span>

                    <h1 class="mt-4 font-judul text-4xl font-bold leading-tight tracking-tight sm:text-5xl print:mt-2 print:text-3xl">{{ $resep->judul }}</h1>

                    {{-- Ringkasan rating, klik untuk turun ke bagian komentar --}}
                    @php
                        // Dua angka ini dihitung di controller (loadCount & loadAvg)
                        $jumlahUlasan = $resep->komentar_count;
                        $rataRata = $resep->komentar_avg_rating;
                    @endphp
                    <a href="#komentar" class="mt-3 inline-flex items-center gap-2 text-sm hover:text-primer-600">
                        @if ($jumlahUlasan)
                            <span class="text-lg tracking-wider text-amber-400">{{ str_repeat('★', (int) round($rataRata)) }}<span class="text-stone-300 dark:text-stone-600">{{ str_repeat('★', 5 - (int) round($rataRata)) }}</span></span>
                            <span class="font-semibold">{{ number_format($rataRata, 1, ',') }}</span>
                            <span class="text-stone-500">· {{ $jumlahUlasan }} ulasan</span>
                        @else
                            <span class="text-stone-500">☆ Belum ada ulasan, jadilah yang pertama</span>
                        @endif
                    </a>

                    <p class="mt-4 text-lg text-stone-600 dark:text-stone-400">{{ $resep->deskripsi }}</p>

                    {{-- Penulis --}}
                    <div class="mt-6 flex items-center gap-3">
                        <span class="grid size-11 place-items-center rounded-full bg-gradient-to-br from-primer-400 to-primer-600 font-bold text-white shadow-lg shadow-primer-500/30">
                            {{ mb_strtoupper(mb_substr($resep->user->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="font-semibold">{{ $resep->user->name }}</p>
                            {{-- translatedFormat: nama bulan otomatis bahasa Indonesia (APP_LOCALE=id) --}}
                            <p class="text-sm text-stone-500 dark:text-stone-400">Dibagikan {{ $resep->created_at->translatedFormat('j F Y') }}</p>
                        </div>
                    </div>

                    {{-- Info singkat --}}
                    <dl class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4 print:mt-4">
                        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                            <dt class="text-2xl">⏱️</dt>
                            <dd class="mt-1 font-bold">{{ $resep->waktu }} mnt</dd>
                            <dd class="text-xs text-stone-500">Waktu</dd>
                        </div>
                        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                            <dt class="text-2xl">🍽️</dt>
                            <dd class="mt-1 font-bold">{{ $resep->porsi }} orang</dd>
                            <dd class="text-xs text-stone-500">Porsi</dd>
                        </div>
                        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                            <dt class="text-2xl">📊</dt>
                            <dd class="mt-1"><span class="rounded-full px-2.5 py-0.5 text-sm font-bold {{ $warnaKesulitan }}">{{ $resep->kesulitan }}</span></dd>
                            <dd class="mt-0.5 text-xs text-stone-500">Kesulitan</dd>
                        </div>
                        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                            <dt class="text-2xl">👀</dt>
                            <dd class="mt-1 font-bold">{{ number_format($resep->dilihat, 0, ',', '.') }}</dd>
                            <dd class="text-xs text-stone-500">Dilihat</dd>
                        </div>
                    </dl>

                    {{-- Tombol aksi: grid 2×2 di HP & laptop kecil, 4 sejajar kalau ruangnya cukup --}}
                    <div class="mt-6 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4 print:hidden">
                        {{-- Komponen yang sama dengan di kartu, tapi ukuran besar --}}
                        <x-tombol-suka :resep="$resep" ukuran="besar" class="justify-center" />
                        <x-tombol-simpan :resep="$resep" ukuran="besar" class="justify-center" />
                        {{-- Bagikan:
                             - HP (dan browser yang mendukung): buka menu bagikan bawaan → WhatsApp, Telegram, dll.
                             - Selain itu: salin link ke clipboard --}}
                        <button type="button" x-data="{ tersalin: false }"
                            @click="if (navigator.share) {
                                        navigator.share({ title: document.title, url: location.href }).catch(() => {});
                                    } else {
                                        navigator.clipboard.writeText(location.href);
                                        tersalin = true;
                                        setTimeout(() => tersalin = false, 2000);
                                    }"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-4 py-3 font-semibold shadow-sm ring-1 ring-stone-200 transition hover:-translate-y-0.5 hover:shadow-md active:translate-y-0 active:scale-95 dark:bg-stone-900 dark:shadow-none dark:ring-stone-700">
                            <span x-text="tersalin ? '✅ Link tersalin!' : '🔗 Bagikan'">🔗 Bagikan</span>
                        </button>
                        {{-- Cetak: membuka jendela print browser. Bagian yang tidak perlu (navbar, tombol,
                             komentar) disembunyikan dengan class print:hidden.
                             x-data WAJIB ada: @click Alpine hanya bekerja di dalam komponen Alpine (x-data).
                             Tanpa x-data, klik tombol ini diabaikan begitu saja. --}}
                        <button type="button" x-data @click="window.print()"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-4 py-3 font-semibold shadow-sm ring-1 ring-stone-200 transition hover:-translate-y-0.5 hover:shadow-md active:translate-y-0 active:scale-95 dark:bg-stone-900 dark:shadow-none dark:ring-stone-700">
                            🖨️ Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== BAHAN & LANGKAH ===================== --}}
    <section class="mx-auto mt-16 grid max-w-6xl items-start gap-8 px-4 sm:px-6 lg:grid-cols-[22rem_1fr] print:mt-8 print:grid-cols-[16rem_1fr] print:gap-6">

        {{-- ----- BAHAN -----
             $persist = data Alpine yang disimpan di localStorage browser,
             jadi centangnya tetap ada walau halaman di-refresh. --}}
        <div x-data="{ siap: $persist([]).as('dapurku-bahan-{{ $resep->id }}') }"
            class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-stone-200/70 lg:sticky lg:top-24 print:static print:p-4 print:shadow-none dark:bg-stone-900 dark:ring-stone-800">
            <div class="flex items-center justify-between">
                <h2 class="font-judul text-2xl font-bold">🧺 Bahan-bahan</h2>
                <span class="rounded-full bg-primer-100 px-3 py-1 text-sm font-bold tabular-nums text-primer-700 print:hidden dark:bg-primer-500/15 dark:text-primer-300"
                    x-text="siap.length + '/{{ $resep->bahan->count() }}'"></span>
            </div>
            <p class="mt-1 text-sm text-stone-500 print:hidden">Centang bahan yang sudah siap.</p>

            <ul class="mt-5 space-y-1">
                @foreach ($resep->bahan as $bahan)
                    <li>
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl px-3 py-2.5 transition hover:bg-primer-50 dark:hover:bg-stone-800"
                            :class="siap.includes({{ $bahan->id }}) && 'text-stone-400 line-through'">
                            {{-- x-model.number: centang → id bahan masuk ke array "siap" --}}
                            <input type="checkbox" value="{{ $bahan->id }}" x-model.number="siap"
                                class="mt-0.5 size-5 shrink-0 cursor-pointer accent-primer-500">
                            <span>{{ $bahan->isi }}</span>
                        </label>
                    </li>
                @endforeach
            </ul>

            {{-- Muncul saat semua bahan sudah dicentang --}}
            <div x-show="siap.length === {{ $resep->bahan->count() }}" x-cloak x-transition
                class="mt-4 rounded-2xl bg-daun-100 p-4 text-center font-semibold text-daun-700">
                🎉 Semua bahan siap, ayo mulai masak!
            </div>

            <button type="button" x-show="siap.length" x-cloak @click="siap = []"
                class="mt-4 w-full rounded-xl py-2 text-sm font-semibold text-stone-500 transition hover:bg-stone-100 dark:hover:bg-stone-800">
                ↺ Hapus semua centang
            </button>
        </div>

        {{-- ----- LANGKAH ----- --}}
        <div x-data="{
                selesai: $persist([]).as('dapurku-langkah-{{ $resep->id }}'),
                total: {{ $resep->langkah->count() }},
                tandai(id) {
                    // Kalau sudah ada di daftar → hapus. Kalau belum → tambahkan.
                    this.selesai = this.selesai.includes(id)
                        ? this.selesai.filter(i => i !== id)
                        : [...this.selesai, id];
                },
            }">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="font-judul text-3xl font-bold">👩‍🍳 Cara memasak</h2>
                    <p class="mt-1 text-sm text-stone-500 print:hidden">Klik langkah yang sudah selesai.</p>
                </div>
                <p class="text-sm font-semibold text-stone-500 print:hidden"><span x-text="selesai.length"></span> dari {{ $resep->langkah->count() }} langkah</p>
            </div>

            {{-- Progress bar: lebarnya mengikuti persentase langkah yang selesai --}}
            <div class="mt-4 h-2 overflow-hidden rounded-full bg-stone-200 print:hidden dark:bg-stone-800">
                <div class="h-full rounded-full bg-gradient-to-r from-primer-400 to-primer-600 transition-all duration-500"
                    :style="`width: ${selesai.length / total * 100}%`"></div>
            </div>

            <ol class="mt-6 space-y-4">
                @foreach ($resep->langkah as $langkah)
                    <li>
                        <button type="button" @click="tandai({{ $langkah->id }})"
                            :class="selesai.includes({{ $langkah->id }})
                                ? 'bg-daun-100/60 ring-daun-500/40 dark:bg-daun-500/10'
                                : 'bg-white ring-stone-200/70 hover:-translate-y-0.5 hover:shadow-lg dark:bg-stone-900 dark:ring-stone-800'"
                            class="flex w-full items-start gap-4 rounded-3xl p-5 text-left shadow-sm ring-1 transition duration-300">
                            {{-- Nomor langkah → berubah jadi tanda ✓ kalau sudah selesai --}}
                            <span :class="selesai.includes({{ $langkah->id }}) ? 'bg-daun-500 text-white' : 'bg-primer-500 text-white shadow-lg shadow-primer-500/30'"
                                class="grid size-10 shrink-0 place-items-center rounded-2xl font-judul text-lg font-bold transition">
                                <span x-show="!selesai.includes({{ $langkah->id }})">{{ $loop->iteration }}</span>
                                <span x-show="selesai.includes({{ $langkah->id }})" x-cloak>✓</span>
                            </span>
                            <p class="pt-1.5 leading-relaxed transition"
                                :class="selesai.includes({{ $langkah->id }}) && 'text-stone-400 line-through'">{{ $langkah->isi }}</p>
                        </button>
                    </li>
                @endforeach
            </ol>

            {{-- Muncul saat semua langkah selesai --}}
            <div x-show="selesai.length === total" x-cloak
                x-transition:enter="transition duration-500 ease-out"
                x-transition:enter-start="scale-90 opacity-0"
                class="mt-6 rounded-3xl bg-gradient-to-br from-primer-500 to-primer-700 p-6 text-center text-white shadow-xl shadow-primer-500/30">
                <p class="text-4xl">🎉</p>
                <p class="mt-2 font-judul text-2xl font-bold">Selamat, masakanmu jadi!</p>
                <p class="mt-1 text-white/85">Selamat menikmati {{ $resep->judul }}.</p>
            </div>
        </div>
    </section>

    {{-- ===================== KOMENTAR (komponen Livewire) =====================
         Komponen Livewire bisa ditaruh di halaman Blade biasa seperti ini.
         :resep="$resep" = kirim resep ke property public $resep di KomentarResep.php --}}
    <livewire:komentar-resep :resep="$resep" />

    {{-- ===================== RESEP LAINNYA ===================== --}}
    @if ($resepLain->isNotEmpty())
        <section class="mx-auto mt-24 max-w-6xl px-4 sm:px-6 print:hidden">
            <p class="font-semibold text-primer-600">🍴 Masih lapar?</p>
            <h2 class="mt-1 font-judul text-3xl font-bold sm:text-4xl">Coba resep lainnya</h2>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($resepLain as $lain)
                    <div x-data="{ tampil: false }" x-intersect.once="tampil = true"
                        :class="tampil ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                        class="transition duration-700 ease-out"
                        style="transition-delay: {{ $loop->index * 100 }}ms">
                        <x-kartu-resep :resep="$lain" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</x-layouts::app>
