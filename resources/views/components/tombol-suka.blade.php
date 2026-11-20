{{--
    Tombol ❤️ Suka. Logikanya ada di resources/js/app.js (tombolSuka).
    Pakai: <x-tombol-suka :resep="$resep" />  atau  <x-tombol-suka :resep="$resep" ukuran="besar" />
--}}
@props(['resep', 'ukuran' => 'kecil'])

<button type="button" aria-label="Suka"
    x-data="tombolSuka({{ $resep->id }}, {{ $resep->suka }}, @js(route('resep.suka', $resep)))"
    @click.prevent="klik()"
    :class="pop && 'scale-125'"
    :aria-pressed="disukai"
    {{ $attributes->class([
        'flex items-center gap-1.5 font-semibold transition duration-300 active:scale-90',
        'rounded-full bg-white/90 px-3 py-1.5 text-sm shadow-sm backdrop-blur dark:bg-stone-900/90' => $ukuran === 'kecil',
        // Bayangan tipis (shadow-sm), sedikit menebal & naik saat di-hover
        'rounded-full bg-white px-4 py-3 shadow-sm ring-1 ring-stone-200 hover:-translate-y-0.5 hover:shadow-md dark:bg-stone-900 dark:shadow-none dark:ring-stone-700' => $ukuran === 'besar',
    ]) }}>
    <svg @class(['transition-colors', 'size-4' => $ukuran === 'kecil', 'size-5' => $ukuran === 'besar'])
        :class="disukai ? 'fill-rose-500 text-rose-500' : 'fill-none text-stone-500'"
        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linejoin="round" d="M12 21s-7.5-4.6-9.5-9.3C1.2 8.4 3.3 5 6.8 5c2 0 3.6 1.1 4.2 2.6h2C13.6 6.1 15.2 5 17.2 5c3.5 0 5.6 3.4 4.3 6.7C19.5 16.4 12 21 12 21z" />
    </svg>
    {{-- Angka awal ditulis langsung (terlihat sebelum Alpine siap), lalu diambil alih x-text --}}
    <span x-text="jumlah" class="tabular-nums">{{ $resep->suka }}</span>
    @if ($ukuran === 'besar')
        <span>Suka</span>
    @endif
</button>
