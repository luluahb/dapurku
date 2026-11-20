{{--
    Layout utama Dapurku.
    Dipakai halaman biasa dengan: <x-layouts::app title="Judul"> ... </x-layouts::app>
    dan otomatis dipakai oleh halaman Livewire.
--}}
@props(['title' => null])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' · Dapurku' : 'Dapurku · Resep Masakan Rumahan' }}</title>
    <meta name="description" content="Dapurku: temukan dan bagikan resep masakan rumahan favoritmu.">

    {{-- Pasang mode gelap SEBELUM halaman tampil, supaya tidak "berkedip" putih --}}
    <script>
        if (localStorage.getItem('tema') === 'gelap' ||
            (!localStorage.getItem('tema') && matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-krem font-sans text-stone-800 antialiased transition-colors duration-300 dark:bg-stone-950 dark:text-stone-200">

    {{-- ===== NAVBAR =====
         x-data = "memori" kecil milik Alpine.js untuk navbar ini --}}
    <header x-data="{ menuTerbuka: false, sudahGulir: false }"
        x-init="sudahGulir = window.scrollY > 10"
        @scroll.window="sudahGulir = window.scrollY > 10"
        :class="sudahGulir ? 'bg-white/80 shadow-sm backdrop-blur-lg dark:bg-stone-900/80' : 'bg-transparent'"
        class="sticky top-0 z-50 transition-all duration-300">

        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="group flex items-center gap-2">
                <span class="grid size-10 place-items-center rounded-2xl bg-primer-500 text-xl shadow-lg shadow-primer-500/30 transition-transform duration-300 group-hover:-rotate-12 group-hover:scale-110">🍳</span>
                <span class="font-judul text-2xl font-bold tracking-tight">Dapur<span class="text-primer-500">ku</span></span>
            </a>

            {{-- Menu (laptop) --}}
            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ url('/') }}" class="rounded-full px-4 py-2 font-medium transition hover:bg-primer-100 hover:text-primer-700 dark:hover:bg-stone-800">Beranda</a>
                <a href="{{ route('jelajah') }}" class="rounded-full px-4 py-2 font-medium transition hover:bg-primer-100 hover:text-primer-700 dark:hover:bg-stone-800">Jelajah Resep</a>
                <a href="{{ route('beranda') }}#kategori" class="rounded-full px-4 py-2 font-medium transition hover:bg-primer-100 hover:text-primer-700 dark:hover:bg-stone-800">Kategori</a>
            </div>

            <div class="flex items-center gap-2">
                {{-- Tombol mode gelap --}}
                <button type="button" aria-label="Ganti tema"
                    x-data="{ gelap: document.documentElement.classList.contains('dark') }"
                    @click="gelap = !gelap;
                            document.documentElement.classList.toggle('dark', gelap);
                            localStorage.setItem('tema', gelap ? 'gelap' : 'terang')"
                    class="grid size-10 place-items-center rounded-full transition hover:bg-primer-100 active:scale-90 dark:hover:bg-stone-800">
                    <span x-show="!gelap" x-transition.scale.origin.center class="text-xl">🌙</span>
                    <span x-show="gelap" x-cloak x-transition.scale.origin.center class="text-xl">☀️</span>
                </button>

                {{-- Tombol admin: HANYA terlihat kalau admin sedang login.
                     Pengunjung biasa tidak melihat tombol login sama sekali. --}}
                @can('admin')
                    <a href="{{ route('admin.resep.index') }}" class="hidden rounded-full bg-primer-500 px-5 py-2.5 font-semibold text-white shadow-lg shadow-primer-500/30 transition hover:-translate-y-0.5 hover:bg-primer-600 active:translate-y-0 sm:inline-block">⚙️ Kelola resep</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="hidden sm:block">
                        @csrf
                        <button class="rounded-full px-4 py-2 font-semibold text-stone-500 transition hover:text-primer-600">Keluar</button>
                    </form>
                @endcan

                {{-- Tombol menu (HP) --}}
                <button type="button" @click="menuTerbuka = !menuTerbuka" aria-label="Buka menu"
                    class="grid size-10 place-items-center rounded-full transition hover:bg-primer-100 md:hidden dark:hover:bg-stone-800">
                    <svg x-show="!menuTerbuka" class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                    <svg x-show="menuTerbuka" x-cloak class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>
        </nav>

        {{-- Menu (HP): muncul dengan animasi saat tombol ditekan --}}
        <div x-show="menuTerbuka" x-cloak
            x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="-translate-y-4 opacity-0"
            x-transition:leave="transition duration-150 ease-in"
            x-transition:leave-end="-translate-y-4 opacity-0"
            @click.outside="menuTerbuka = false"
            class="mx-4 mb-3 rounded-3xl bg-white p-3 shadow-xl md:hidden dark:bg-stone-900">
            <a href="{{ url('/') }}" class="block rounded-2xl px-4 py-3 font-medium hover:bg-primer-50 dark:hover:bg-stone-800">🏠 Beranda</a>
            <a href="{{ route('jelajah') }}" class="block rounded-2xl px-4 py-3 font-medium hover:bg-primer-50 dark:hover:bg-stone-800">🔍 Jelajah Resep</a>
            <a href="{{ route('beranda') }}#kategori" class="block rounded-2xl px-4 py-3 font-medium hover:bg-primer-50 dark:hover:bg-stone-800">🗂️ Kategori</a>
            @can('admin')
                <div class="mt-2 grid grid-cols-2 gap-2 border-t border-stone-100 pt-3 dark:border-stone-800">
                    <a href="{{ route('admin.resep.index') }}" class="rounded-2xl bg-primer-500 py-3 text-center font-semibold text-white">⚙️ Kelola</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="w-full rounded-2xl border border-stone-200 py-3 font-semibold dark:border-stone-700">Keluar</button>
                    </form>
                </div>
            @endcan
        </div>
    </header>

    {{-- ===== NOTIFIKASI (flash message) =====
         Muncul sekali setelah redirect()->with('pesan', ...), lalu hilang sendiri setelah 4 detik --}}
    @if (session('pesan'))
        <div x-data="{ tampil: false }"
            x-init="$nextTick(() => tampil = true); setTimeout(() => tampil = false, 4000)"
            x-show="tampil"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-y-6 opacity-0"
            x-transition:leave="transition duration-300 ease-in"
            x-transition:leave-end="translate-y-6 opacity-0"
            class="fixed inset-x-4 bottom-6 z-[60] mx-auto flex max-w-md items-center gap-3 rounded-2xl bg-stone-900 px-5 py-4 text-white shadow-2xl dark:bg-white dark:text-stone-900">
            <p class="flex-1 font-medium">{{ session('pesan') }}</p>
            <button type="button" @click="tampil = false" aria-label="Tutup" class="opacity-60 transition hover:opacity-100">✕</button>
        </div>
    @endif

    {{-- ===== ISI HALAMAN ===== --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="mt-24 border-t border-stone-200/70 bg-white/60 dark:border-stone-800 dark:bg-stone-900/60">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3">
            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <span class="grid size-9 place-items-center rounded-xl bg-primer-500 text-lg">🍳</span>
                    <span class="font-judul text-xl font-bold">Dapur<span class="text-primer-500">ku</span></span>
                </a>
                <p class="mt-3 text-sm text-stone-500 dark:text-stone-400">Tempat berbagi resep masakan rumahan, dari dapur kita untuk semua.</p>
            </div>
            <div>
                <h3 class="font-semibold">Jelajahi</h3>
                <ul class="mt-3 space-y-2 text-sm text-stone-500 dark:text-stone-400">
                    <li><a href="{{ route('jelajah') }}" class="hover:text-primer-600">Semua resep</a></li>
                    <li><a href="{{ route('beranda') }}#kategori" class="hover:text-primer-600">Kategori</a></li>
                    <li><a href="{{ route('jelajah', ['urut' => 'populer']) }}" class="hover:text-primer-600">Resep terpopuler</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold">Tentang</h3>
                <p class="mt-3 text-sm text-stone-500 dark:text-stone-400">Dibuat dengan Laravel, Livewire, dan Tailwind CSS sebagai proyek portofolio.</p>
            </div>
        </div>
        <p class="pb-8 text-center text-sm text-stone-400">© {{ date('Y') }} Dapurku. Dibuat dengan ❤️ dan sedikit garam.</p>
    </footer>

    @livewireScripts
</body>

</html>
