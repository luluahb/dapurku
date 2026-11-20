{{--
    Komponen kartu resep. Sekarang cukup dikirimi satu model Resep:
    <x-kartu-resep :resep="$resep" />
    Ambil resepnya dengan Resep::untukKartu() supaya penulis, kategori, dan rating ikut terambil.
--}}
@props(['resep'])

@php
    // Link ke halaman detail. route() otomatis memakai slug: /resep/rendang-daging-...
    $href = route('resep.show', $resep);

    // Warna label tingkat kesulitan
    $warnaKesulitan = match ($resep->kesulitan) {
        'Mudah' => 'bg-daun-100 text-daun-700',
        'Sedang' => 'bg-amber-100 text-amber-700',
        default => 'bg-rose-100 text-rose-700',
    };
    $penulis = $resep->user->name;
@endphp

{{--
    Animasi:
    - Laptop (ada mouse): hover → kartu naik, foto membesar.
    - HP (layar sentuh, tidak ada hover): has-[a:active] → saat LINK di kartu sedang ditekan jari,
      kartu sedikit mengecil & foto membesar. Menekan tombol suka/simpan tidak memicu efek ini.
    - data-kartu-link & data-foto-resep dipakai app.js untuk animasi "foto terbang" ke halaman detail.
--}}
<article {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-stone-200/70 transition duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-primer-500/10 has-[a:active]:scale-[0.97] has-[a:active]:shadow-md dark:bg-stone-900 dark:ring-stone-800']) }}>

    {{-- Foto: membesar perlahan saat di-hover / ditekan. Kalau resep tidak punya foto, tampil ikon kategorinya. --}}
    <a href="{{ $href }}" data-kartu-link data-foto-resep class="block aspect-[4/3] overflow-hidden bg-primer-50 dark:bg-stone-800">
        @if ($resep->foto_url)
            <img src="{{ $resep->foto_url }}" alt="{{ $resep->judul }}" loading="lazy"
                class="size-full object-cover transition duration-700 ease-out group-hover:scale-110 group-has-[a:active]:scale-110">
        @else
            <span class="grid size-full place-items-center text-6xl">{{ $resep->kategori->ikon }}</span>
        @endif
    </a>

    {{-- Label kategori di atas foto --}}
    <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-stone-700 backdrop-blur dark:bg-stone-900/90 dark:text-stone-200">
        {{ $resep->kategori->nama }}
    </span>

    {{-- Tombol suka & simpan (komponen terpisah, lihat tombol-suka.blade.php & tombol-simpan.blade.php) --}}
    <div class="absolute right-4 top-4 flex items-center gap-2">
        <x-tombol-simpan :resep="$resep" />
        <x-tombol-suka :resep="$resep" />
    </div>

    <div class="p-5">
        <a href="{{ $href }}" data-kartu-link>
            <h3 class="line-clamp-2 font-judul text-xl font-bold leading-snug transition-colors group-hover:text-primer-600 group-has-[a:active]:text-primer-600">{{ $resep->judul }}</h3>
        </a>

        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-stone-500 dark:text-stone-400">
            <span>⏱️ {{ $resep->waktu }} menit</span>
            {{-- Rating tampil kalau sudah ada ulasan; kalau belum, tampilkan jumlah dilihat --}}
            @if ($resep->komentar_count)
                <span><span class="text-amber-500">★</span> <span class="font-semibold text-stone-700 dark:text-stone-200">{{ number_format($resep->komentar_avg_rating, 1, ',') }}</span> ({{ $resep->komentar_count }})</span>
            @else
                <span>👀 {{ $resep->dilihat }}</span>
            @endif
            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $warnaKesulitan }}">{{ $resep->kesulitan }}</span>
        </div>

        <div class="mt-4 flex items-center gap-2 border-t border-stone-100 pt-4 dark:border-stone-800">
            {{-- Avatar berisi huruf pertama nama penulis --}}
            <span class="grid size-7 place-items-center rounded-full bg-primer-100 text-xs font-bold text-primer-700">
                {{ mb_strtoupper(mb_substr($penulis, 0, 1)) }}
            </span>
            <span class="text-sm text-stone-500 dark:text-stone-400">oleh <span class="font-semibold text-stone-700 dark:text-stone-200">{{ $penulis }}</span></span>
        </div>
    </div>
</article>
