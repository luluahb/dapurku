{{--
    Komponen kartu resep. Sekarang cukup dikirimi satu model Resep:
    <x-kartu-resep :resep="$resep" />
    Ambil resepnya dengan ->with('user', 'kategori') supaya tidak ada query tambahan per kartu.
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

<article {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-stone-200/70 transition duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-primer-500/10 dark:bg-stone-900 dark:ring-stone-800']) }}>

    {{-- Foto: membesar perlahan saat di-hover. Kalau resep tidak punya foto, tampil ikon kategorinya. --}}
    <a href="{{ $href }}" class="block aspect-[4/3] overflow-hidden bg-primer-50 dark:bg-stone-800">
        @if ($resep->foto_url)
            <img src="{{ $resep->foto_url }}" alt="{{ $resep->judul }}" loading="lazy"
                class="size-full object-cover transition duration-700 ease-out group-hover:scale-110">
        @else
            <span class="grid size-full place-items-center text-6xl">{{ $resep->kategori->ikon }}</span>
        @endif
    </a>

    {{-- Label kategori di atas foto --}}
    <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-stone-700 backdrop-blur dark:bg-stone-900/90 dark:text-stone-200">
        {{ $resep->kategori->nama }}
    </span>

    {{-- Tombol suka: masih tampilan saja (belum tersimpan). Jumlah asli dibuat di tahap fitur Suka. --}}
    <button type="button" aria-label="Suka"
        x-data="{ disukai: false, jumlah: 0, pop: false }"
        @click="disukai = !disukai; jumlah += disukai ? 1 : -1; pop = true; setTimeout(() => pop = false, 300)"
        :class="pop && 'scale-125'"
        class="absolute right-4 top-4 flex items-center gap-1 rounded-full bg-white/90 px-3 py-1.5 text-sm font-semibold shadow-sm backdrop-blur transition-transform duration-300 active:scale-90 dark:bg-stone-900/90">
        <svg class="size-4 transition-colors" :class="disukai ? 'fill-rose-500 text-rose-500' : 'fill-none text-stone-500'"
            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linejoin="round" d="M12 21s-7.5-4.6-9.5-9.3C1.2 8.4 3.3 5 6.8 5c2 0 3.6 1.1 4.2 2.6h2C13.6 6.1 15.2 5 17.2 5c3.5 0 5.6 3.4 4.3 6.7C19.5 16.4 12 21 12 21z" />
        </svg>
        <span x-text="jumlah" class="tabular-nums"></span>
    </button>

    <div class="p-5">
        <a href="{{ $href }}">
            <h3 class="line-clamp-2 font-judul text-xl font-bold leading-snug transition-colors group-hover:text-primer-600">{{ $resep->judul }}</h3>
        </a>

        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-stone-500 dark:text-stone-400">
            <span>⏱️ {{ $resep->waktu }} menit</span>
            <span>👀 {{ $resep->dilihat }}</span>
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
