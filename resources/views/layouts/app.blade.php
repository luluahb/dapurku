{{--
    Layout utama Dapurku.
    Dipakai halaman biasa dengan: <x-layouts::app title="Judul"> ... </x-layouts::app>
    dan otomatis dipakai oleh halaman Livewire.
--}}
{{--
    Props (semua boleh dikosongkan):
    - title       : judul tab browser
    - description : deskripsi untuk Google & pratinjau link
    - gambar      : gambar pratinjau saat link dibagikan (WhatsApp, Facebook, X)
--}}
@props(['title' => null, 'description' => null, 'gambar' => null])

@php
    $judulHalaman = $title ? $title . ' · Dapurku' : 'Dapurku · Resep Masakan Rumahan';
    $deskripsi = Str::limit($description ?? 'Temukan resep masakan rumahan, cari berdasarkan bahan yang ada di dapurmu, dan simpan resep favoritmu.', 160);
    $gambarPratinjau = $gambar ?? asset('images/og-dapurku.png');

    // Daftar menu navigasi (dipakai di menu laptop & menu HP).
    // 'aktif' = true kalau halaman ini sedang dibuka → menunya diberi warna penanda.
    $menu = [
        ['label' => 'Beranda', 'ikon' => '🏠', 'url' => route('beranda'), 'aktif' => request()->routeIs('beranda')],
        ['label' => 'Jelajah Resep', 'ikon' => '🔍', 'url' => route('jelajah'), 'aktif' => request()->routeIs('jelajah')],
        ['label' => 'Kategori', 'ikon' => '🗂️', 'url' => route('beranda') . '#kategori', 'aktif' => false],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulHalaman }}</title>
    <meta name="description" content="{{ $deskripsi }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph: dibaca WhatsApp, Facebook, LinkedIn, dll. saat link dibagikan --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Dapurku">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="{{ $judulHalaman }}">
    <meta property="og:description" content="{{ $deskripsi }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $gambarPratinjau }}">
    {{-- Versi X/Twitter: tampil sebagai kartu bergambar besar --}}
    <meta name="twitter:card" content="summary_large_image">

    {{-- Ikon di tab browser & warna bilah browser di HP --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <meta name="theme-color" content="#fb5d12">

    {{-- Token keamanan untuk request POST dari JavaScript (tombol suka) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Pasang mode gelap SEBELUM halaman tampil, supaya tidak "berkedip" putih.
         try/catch: localStorage bisa diblokir browser (mode privat). --}}
    <script>
        try {
            const tema = localStorage.getItem('tema');
            if (tema === 'gelap' || (!tema && matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    </script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-krem font-sans text-stone-800 antialiased transition-colors duration-300 print:bg-white dark:bg-stone-950 dark:text-stone-200">

    {{-- ===== NAVBAR =====
         x-data = "memori" kecil milik Alpine.js untuk navbar ini --}}
    <header x-data="{ menuTerbuka: false, sudahGulir: false }"
        x-init="sudahGulir = window.scrollY > 10"
        @scroll.window="sudahGulir = window.scrollY > 10"
        :class="sudahGulir ? 'bg-white/80 shadow-sm backdrop-blur-lg dark:bg-stone-900/80' : 'bg-transparent'"
        class="sticky top-0 z-50 transition-all duration-300 print:hidden">

        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="group flex items-center gap-2">
                <span class="grid size-10 place-items-center rounded-2xl bg-primer-500 text-xl shadow-lg shadow-primer-500/30 transition-transform duration-300 group-hover:-rotate-12 group-hover:scale-110">🍳</span>
                <span class="font-judul text-2xl font-bold tracking-tight">Dapur<span class="text-primer-500">ku</span></span>
            </a>

            {{-- Menu (laptop) --}}
            <div class="hidden items-center gap-1 md:flex">
                @foreach ($menu as $item)
                    {{-- aria-current: memberi tahu pembaca layar (tunanetra) bahwa ini halaman yang sedang dibuka --}}
                    <a href="{{ $item['url'] }}" @if ($item['aktif']) aria-current="page" @endif
                        @class([
                            'rounded-full px-4 py-2 font-medium transition hover:bg-primer-100 hover:text-primer-700 active:scale-95 active:bg-primer-100 dark:hover:bg-stone-800 dark:active:bg-stone-800',
                            'bg-primer-100 text-primer-700 dark:bg-stone-800 dark:text-primer-300' => $item['aktif'],
                        ])>{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                {{-- Favoritku + angka jumlah resep tersimpan (ikut berubah saat tombol 🔖 diklik) --}}
                <a href="{{ route('favoritku') }}" aria-label="Favoritku" title="Favoritku"
                    x-data="jumlahSimpan()" @simpan-berubah.window="jumlah = $event.detail.length"
                    class="relative grid size-10 place-items-center rounded-full transition hover:bg-primer-100 active:scale-90 dark:hover:bg-stone-800">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linejoin="round" d="M6 4h12v17l-6-4-6 4V4z" /></svg>
                    <span x-show="jumlah > 0" x-cloak x-text="jumlah" x-transition.scale
                        class="absolute -right-0.5 -top-0.5 grid h-5 min-w-5 place-items-center rounded-full bg-daun-500 px-1 text-[11px] font-bold text-white"></span>
                </a>

                {{-- Tombol mode gelap --}}
                <button type="button" aria-label="Ganti tema"
                    x-data="{ gelap: document.documentElement.classList.contains('dark') }"
                    @click="gelap = !gelap;
                            document.documentElement.classList.toggle('dark', gelap);
                            try { localStorage.setItem('tema', gelap ? 'gelap' : 'terang') } catch (e) {}"
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
                    class="grid size-10 place-items-center rounded-full transition hover:bg-primer-100 active:scale-90 active:bg-primer-100 md:hidden dark:hover:bg-stone-800 dark:active:bg-stone-800">
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
            {{-- Menu HP: sama seperti menu laptop + Favoritku.
                 active: = warna saat jari menekan (di HP tidak ada hover).
                 @click menutup menu, penting untuk link "Kategori" yang hanya menggulir di halaman yang sama. --}}
            @foreach ([...$menu, ['label' => 'Favoritku', 'ikon' => '🔖', 'url' => route('favoritku'), 'aktif' => request()->routeIs('favoritku')]] as $item)
                <a href="{{ $item['url'] }}" @click="menuTerbuka = false" @if ($item['aktif']) aria-current="page" @endif
                    @class([
                        'flex items-center gap-2 rounded-2xl px-4 py-3 font-medium transition hover:bg-primer-50 active:scale-[0.98] active:bg-primer-100 active:text-primer-700 dark:hover:bg-stone-800 dark:active:bg-stone-800',
                        'bg-primer-50 font-semibold text-primer-700 dark:bg-stone-800 dark:text-primer-300' => $item['aktif'],
                    ])>
                    <span>{{ $item['ikon'] }}</span>
                    <span class="flex-1">{{ $item['label'] }}</span>
                    @if ($item['aktif'])
                        {{-- Titik kecil penanda halaman yang sedang dibuka --}}
                        <span class="size-2 rounded-full bg-primer-500"></span>
                    @endif
                </a>
            @endforeach
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
    {{-- overflow-x-clip: hiasan yang keluar dari layar dipotong,
         supaya halaman tidak bisa "digeser ke samping" di HP --}}
    <main class="overflow-x-clip">
        {{ $slot }}
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="mt-24 border-t border-stone-200/70 bg-white/60 print:hidden dark:border-stone-800 dark:bg-stone-900/60">
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
                    <li><a href="{{ route('jelajah') }}" class="transition hover:text-primer-600 active:text-primer-600">Semua resep</a></li>
                    <li><a href="{{ route('beranda') }}#kategori" class="transition hover:text-primer-600 active:text-primer-600">Kategori</a></li>
                    <li><a href="{{ route('jelajah', ['urut' => 'populer']) }}" class="transition hover:text-primer-600 active:text-primer-600">Resep terpopuler</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold">Tentang</h3>
                <p class="mt-3 text-sm text-stone-500 dark:text-stone-400">Dibuat dengan Laravel, Livewire, dan Tailwind CSS sebagai proyek portofolio.</p>
            </div>
        </div>
        <p class="pb-8 text-center text-sm text-stone-400">© {{ date('Y') }} Dapurku. Dibuat dengan ❤️ dan sedikit garam.</p>
    </footer>

    {{-- ===== TOMBOL KEMBALI KE ATAS =====
         Muncul setelah halaman digulir cukup jauh --}}
    <button type="button" aria-label="Kembali ke atas"
        x-data="{ tampil: false }"
        @scroll.window.throttle.100ms="tampil = window.scrollY > 900"
        x-show="tampil" x-cloak
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="translate-y-4 opacity-0"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-end="translate-y-4 opacity-0"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-40 grid size-12 place-items-center rounded-full bg-primer-500 text-white shadow-xl shadow-primer-500/30 transition hover:-translate-y-1 hover:bg-primer-600 active:scale-90 print:hidden">
        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5M5 12l7-7 7 7" /></svg>
    </button>

    @livewireScripts
</body>

</html>
