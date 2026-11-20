{{--
    Halaman Favoritku. Data dari App\Livewire\Favoritku.

    x-init: begitu halaman terbuka, baca daftar id dari localStorage (fungsi di app.js),
            lalu kirim ke PHP lewat $wire.muat(...).
    @simpan-berubah.window: kalau ada resep yang dibatalkan simpannya di halaman ini,
            kirim daftar terbaru ke PHP supaya kartunya hilang dari daftar.
--}}
<div class="mx-auto max-w-6xl px-4 pt-8 sm:px-6"
    x-init="$wire.muat(window.ambilResepTersimpan())"
    @simpan-berubah.window="$wire.muat($event.detail)">

    <p class="font-semibold text-primer-600">🔖 Koleksi pribadimu</p>
    <h1 class="mt-1 font-judul text-4xl font-bold tracking-tight sm:text-5xl">Favoritku</h1>
    <p class="mt-3 text-stone-600 dark:text-stone-400">
        Resep yang kamu simpan. Tersimpan di browser ini, jadi tidak perlu akun.
    </p>

    @if (! $sudahDimuat)
        {{-- Kerangka "loading" (skeleton) selama menunggu data dari browser --}}
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @for ($i = 0; $i < 3; $i++)
                <div class="animate-pulse overflow-hidden rounded-3xl bg-white ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                    <div class="aspect-[4/3] bg-stone-200 dark:bg-stone-800"></div>
                    <div class="space-y-3 p-5">
                        <div class="h-5 w-3/4 rounded-full bg-stone-200 dark:bg-stone-800"></div>
                        <div class="h-4 w-1/2 rounded-full bg-stone-200 dark:bg-stone-800"></div>
                    </div>
                </div>
            @endfor
        </div>
    @elseif ($resep->isEmpty())
        <div class="mt-10 rounded-[2rem] border-2 border-dashed border-stone-200 px-6 py-16 text-center dark:border-stone-800">
            <p class="text-6xl">🔖</p>
            <h2 class="mt-4 font-judul text-2xl font-bold">Belum ada resep yang disimpan</h2>
            <p class="mt-2 text-stone-500">Tekan ikon 🔖 di kartu resep untuk menyimpannya di sini.</p>
            <a href="{{ route('jelajah') }}"
                class="mt-6 inline-block rounded-full bg-primer-500 px-6 py-3 font-semibold text-white shadow-lg shadow-primer-500/30 transition hover:bg-primer-600 active:scale-95">
                🔍 Jelajah resep
            </a>
        </div>
    @else
        <p class="mt-10 text-stone-500"><span class="font-bold text-stone-800 dark:text-stone-100">{{ $resep->count() }} resep</span> tersimpan</p>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($resep as $item)
                <x-kartu-resep :resep="$item" wire:key="fav-{{ $item->id }}" class="animate-muncul" />
            @endforeach
        </div>
    @endif
</div>
