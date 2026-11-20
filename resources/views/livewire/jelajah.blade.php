{{--
    Tampilan komponen Livewire "Jelajah".
    Aturan Livewire: isi komponen harus dibungkus SATU elemen paling luar (div di bawah).

    Kata kunci Livewire yang dipakai di sini:
    - wire:model.live  → input terhubung ke property PHP, dikirim ke server saat berubah
    - wire:click       → klik menjalankan method PHP di komponen
    - wire:loading     → tampil HANYA selama menunggu jawaban server
    - wire:key         → "KTP" tiap kartu, supaya Livewire tahu kartu mana yang baru/lama
--}}
<div class="mx-auto max-w-6xl px-4 pt-8 sm:px-6">

    {{-- ===================== JUDUL & PENCARIAN ===================== --}}
    <div class="relative">
        <div class="pointer-events-none absolute -right-20 -top-24 size-80 rounded-full bg-primer-300/30 blur-3xl dark:bg-primer-700/15"></div>

        <p class="relative font-semibold text-primer-600">🔍 Jelajah resep</p>
        <h1 class="relative mt-1 font-judul text-4xl font-bold tracking-tight sm:text-5xl">Mau masak apa hari ini?</h1>
        <p class="relative mt-3 text-stone-600 dark:text-stone-400">Cari berdasarkan nama masakan <span class="font-semibold">atau bahan</span> yang ada di dapurmu, misalnya "santan" atau "telur".</p>

        <div class="relative mt-6 flex items-center gap-2 rounded-full bg-white p-2 pl-5 shadow-xl shadow-stone-200/60 ring-1 ring-stone-200 transition focus-within:ring-2 focus-within:ring-primer-400 dark:bg-stone-900 dark:shadow-none dark:ring-stone-800">
            {{-- Ikon kaca pembesar berganti jadi spinner selama Livewire memproses pencarian --}}
            <span wire:loading.remove wire:target="cari" class="text-xl">🔍</span>
            <span wire:loading wire:target="cari" class="size-5 animate-spin rounded-full border-2 border-primer-200 border-t-primer-500"></span>

            {{-- .debounce.400ms = tunggu 400 milidetik setelah berhenti mengetik, baru dikirim.
                 Supaya server tidak dipanggil di setiap huruf. --}}
            {{-- type="text" (bukan "search") supaya tidak muncul tombol ✕ bawaan browser,
                 karena kita sudah punya tombol ✕ sendiri --}}
            <input type="text" wire:model.live.debounce.400ms="cari" autofocus
                placeholder="Cari resep atau bahan: rendang, santan, telur..."
                class="w-full bg-transparent py-3 text-lg outline-none placeholder:text-stone-400">

            @if ($cari)
                <button type="button" wire:click="$set('cari', '')" aria-label="Hapus pencarian"
                    class="grid size-10 shrink-0 place-items-center rounded-full text-stone-400 transition hover:bg-stone-100 hover:text-stone-700 dark:hover:bg-stone-800">✕</button>
            @endif
        </div>
    </div>

    {{-- ===================== FILTER KATEGORI ===================== --}}
    {{-- @class: menyusun class sesuai kondisi. Class yang nilainya true saja yang dipakai. --}}
    <div class="-mx-4 mt-8 flex gap-2 overflow-x-auto px-4 pb-2 sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0">
        <button type="button" wire:click="$set('kategori', '')"
            @class([
                'shrink-0 rounded-full px-4 py-2 text-sm font-semibold ring-1 transition active:scale-95',
                'bg-primer-500 text-white shadow-lg shadow-primer-500/30 ring-primer-500' => $kategori === '',
                'bg-white ring-stone-200 hover:ring-primer-300 dark:bg-stone-900 dark:ring-stone-800' => $kategori !== '',
            ])>
            🍽️ Semua
        </button>
        @foreach ($daftarKategori as $item)
            <button type="button" wire:click="pilihKategori('{{ $item->slug }}')"
                @class([
                    'shrink-0 rounded-full px-4 py-2 text-sm font-semibold ring-1 transition active:scale-95',
                    'bg-primer-500 text-white shadow-lg shadow-primer-500/30 ring-primer-500' => $kategori === $item->slug,
                    'bg-white ring-stone-200 hover:ring-primer-300 dark:bg-stone-900 dark:ring-stone-800' => $kategori !== $item->slug,
                ])>
                {{ $item->ikon }} {{ $item->nama }}
                <span @class(['ml-1 text-xs', 'text-white/75' => $kategori === $item->slug, 'text-stone-400' => $kategori !== $item->slug])>{{ $item->resep_count }}</span>
            </button>
        @endforeach
    </div>

    {{-- ===================== KESULITAN & URUTAN ===================== --}}
    <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-3">
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-stone-500">Kesulitan:</span>
            @foreach (['Mudah' => 'daun', 'Sedang' => 'amber', 'Sulit' => 'rose'] as $tingkat => $warna)
                <button type="button" wire:click="pilihKesulitan('{{ $tingkat }}')"
                    @class([
                        'rounded-full px-3 py-1 text-sm font-semibold transition active:scale-95',
                        'bg-daun-500 text-white' => $kesulitan === $tingkat && $warna === 'daun',
                        'bg-amber-500 text-white' => $kesulitan === $tingkat && $warna === 'amber',
                        'bg-rose-500 text-white' => $kesulitan === $tingkat && $warna === 'rose',
                        'bg-stone-200/70 text-stone-600 hover:bg-stone-300/70 dark:bg-stone-800 dark:text-stone-300' => $kesulitan !== $tingkat,
                    ])>
                    {{ $tingkat }}
                </button>
            @endforeach
        </div>

        <label class="flex items-center gap-2">
            <span class="text-sm font-medium text-stone-500">Urutkan:</span>
            <select wire:model.live="urut"
                class="cursor-pointer rounded-full bg-white px-4 py-1.5 text-sm font-semibold ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-primer-400 dark:bg-stone-900 dark:ring-stone-800">
                <option value="terbaru">✨ Terbaru</option>
                <option value="populer">🔥 Terpopuler</option>
                <option value="tercepat">⏱️ Tercepat dimasak</option>
            </select>
        </label>

        @if ($adaFilter)
            <button type="button" wire:click="resetFilter"
                class="text-sm font-semibold text-primer-600 transition hover:text-primer-700 sm:ml-auto">
                ↺ Hapus semua filter
            </button>
        @endif
    </div>

    {{-- ===================== HASIL ===================== --}}
    <p class="mt-10 text-stone-500 dark:text-stone-400">
        <span class="font-bold text-stone-800 dark:text-stone-100">{{ $total }} resep</span> ditemukan
        @if ($cari) untuk "<span class="font-semibold text-primer-600">{{ $cari }}</span>" @endif
    </p>

    {{-- Selama filter diproses, hasil lama dibuat agak pudar --}}
    <div wire:loading.class="opacity-50"
        wire:target="cari, urut, pilihKategori, pilihKesulitan, resetFilter, kategori"
        class="transition-opacity">

        @if ($resep->isEmpty())
            {{-- Tampilan saat tidak ada hasil --}}
            <div class="mt-6 rounded-[2rem] border-2 border-dashed border-stone-200 px-6 py-16 text-center dark:border-stone-800">
                <p class="text-6xl">🍳</p>
                <h2 class="mt-4 font-judul text-2xl font-bold">Resepnya belum ada</h2>
                <p class="mt-2 text-stone-500">Coba kata kunci lain, atau hapus beberapa filter.</p>
                <button type="button" wire:click="resetFilter"
                    class="mt-6 rounded-full bg-primer-500 px-6 py-3 font-semibold text-white shadow-lg shadow-primer-500/30 transition hover:bg-primer-600 active:scale-95">
                    Lihat semua resep
                </button>
            </div>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($resep as $item)
                    <x-kartu-resep :resep="$item" wire:key="resep-{{ $item->id }}" class="animate-muncul" />
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===================== INFINITE SCROLL =====================
         Elemen "pemicu" di bawah daftar. Saat terlihat di layar (x-intersect dari Alpine),
         method muatLagi() di PHP dipanggil lewat $wire → 6 resep berikutnya ditambahkan.
         wire:key ikut berubah, supaya pemicu dibuat ulang dan bisa terpicu lagi. --}}
    @if ($adaLagi)
        <div wire:key="pemicu-{{ $jumlah }}" x-intersect="$wire.muatLagi()" class="mt-10 flex justify-center">
            <span class="inline-flex items-center gap-3 rounded-full bg-white px-5 py-3 text-sm font-semibold text-stone-500 shadow-sm ring-1 ring-stone-200 dark:bg-stone-900 dark:ring-stone-800">
                <span class="size-4 animate-spin rounded-full border-2 border-primer-200 border-t-primer-500"></span>
                Memuat resep lainnya...
            </span>
        </div>
    @elseif ($resep->isNotEmpty())
        <p class="mt-12 text-center text-sm text-stone-400">— Semua resep sudah ditampilkan 🍽️ —</p>
    @endif
</div>
