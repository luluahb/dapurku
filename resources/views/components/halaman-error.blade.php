{{--
    Tampilan bersama untuk halaman error (404, 403, 419, 429).
    Laravel otomatis memakai file resources/views/errors/{kode}.blade.php saat error terjadi.
--}}
@props(['kode', 'ikon', 'judul', 'pesan'])

<x-layouts::app :title="$judul">
    <section class="relative px-4 py-20 text-center sm:py-28">
        <div class="pointer-events-none absolute left-1/2 top-10 size-96 -translate-x-1/2 rounded-full bg-primer-300/30 blur-3xl dark:bg-primer-700/15"></div>

        <div class="relative mx-auto max-w-lg">
            {{-- Angka kode error besar, dengan ikon melayang di tengahnya --}}
            <div class="relative inline-block">
                <p class="font-judul text-[8rem] font-bold leading-none tracking-tighter text-primer-500/15 sm:text-[11rem]">{{ $kode }}</p>
                <span class="absolute inset-0 grid animate-melayang place-items-center text-7xl sm:text-8xl">{{ $ikon }}</span>
            </div>

            <h1 class="mt-4 font-judul text-3xl font-bold sm:text-4xl">{{ $judul }}</h1>
            <p class="mt-3 text-lg text-stone-600 dark:text-stone-400">{{ $pesan }}</p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('beranda') }}"
                    class="rounded-full bg-primer-500 px-6 py-3 font-bold text-white shadow-lg shadow-primer-500/30 transition hover:-translate-y-0.5 hover:bg-primer-600 active:translate-y-0">
                    🏠 Ke Beranda
                </a>
                {{ $slot }}
            </div>
        </div>
    </section>
</x-layouts::app>
