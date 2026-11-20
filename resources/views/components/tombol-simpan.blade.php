{{--
    Tombol 🔖 Simpan. Logikanya ada di resources/js/app.js (tombolSimpan).
    Daftar resep yang disimpan ada di localStorage browser → tampil di halaman Favoritku.
--}}
@props(['resep', 'ukuran' => 'kecil'])

<button type="button"
    x-data="tombolSimpan({{ $resep->id }})"
    @click.prevent="klik()"
    @simpan-berubah.window="sinkron($event.detail)"
    :aria-label="tersimpan ? 'Hapus dari Favoritku' : 'Simpan ke Favoritku'"
    :aria-pressed="tersimpan"
    {{ $attributes->class([
        'font-semibold transition active:scale-90',
        'grid size-9 place-items-center rounded-full shadow-sm backdrop-blur' => $ukuran === 'kecil',
        // Bayangan tipis (shadow-sm), sedikit menebal & naik saat di-hover
        'flex items-center gap-1.5 rounded-full px-4 py-3 shadow-sm ring-1 hover:-translate-y-0.5 hover:shadow-md dark:shadow-none' => $ukuran === 'besar',
    ]) }}
    @if ($ukuran === 'kecil')
        :class="tersimpan ? 'bg-daun-500 text-white' : 'bg-white/90 text-stone-500 dark:bg-stone-900/90'"
    @else
        :class="tersimpan ? 'bg-daun-500 text-white ring-daun-500' : 'bg-white ring-stone-200 dark:bg-stone-900 dark:ring-stone-700'"
    @endif>
    <svg @class(['size-4' => $ukuran === 'kecil', 'size-5' => $ukuran === 'besar'])
        :class="tersimpan ? 'fill-current' : 'fill-none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linejoin="round" d="M6 4h12v17l-6-4-6 4V4z" />
    </svg>
    @if ($ukuran === 'besar')
        <span x-text="tersimpan ? 'Tersimpan' : 'Simpan'">Simpan</span>
    @endif
</button>
