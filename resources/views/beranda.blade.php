<x-layouts::app>

    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden">
        {{-- Bulatan warna buram sebagai hiasan latar --}}
        <div class="pointer-events-none absolute -right-32 -top-32 size-[28rem] rounded-full bg-primer-300/40 blur-3xl dark:bg-primer-700/20"></div>
        <div class="pointer-events-none absolute -left-24 top-48 size-72 rounded-full bg-amber-200/50 blur-3xl dark:bg-amber-700/10"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 pb-16 pt-10 sm:px-6 lg:grid-cols-2 lg:pt-16">
            {{-- Teks kiri --}}
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-primer-700 shadow-sm ring-1 ring-primer-100 dark:bg-stone-900 dark:ring-stone-800">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-primer-400 opacity-75"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-primer-500"></span>
                    </span>
                    {{ $statistik['resep'] }} resep rumahan menunggumu
                </span>

                <h1 class="mt-6 font-judul text-5xl font-bold leading-[1.05] tracking-tight sm:text-6xl">
                    Masak enak,<br>
                    {{-- Teks "dapurmu" dan titiknya ditulis rapat supaya tidak ada spasi sebelum titik --}}
                    mulai dari <span class="relative whitespace-nowrap text-primer-500">dapurmu<svg class="absolute -bottom-2 left-0 w-full text-primer-300" viewBox="0 0 200 12" fill="none" preserveAspectRatio="none"><path d="M2 9c40-6 120-8 196-2" stroke="currentColor" stroke-width="4" stroke-linecap="round" /></svg></span>.
                </h1>

                <p class="mt-6 max-w-lg text-lg text-stone-600 dark:text-stone-400">
                    Temukan ribuan resep masakan rumahan, simpan favoritmu, dan bagikan resep andalan keluarga ke semua orang.
                </p>

                {{-- Kotak pencarian: dikirim ke halaman Jelajah sebagai /jelajah?cari=... --}}
                <form action="{{ route('jelajah') }}" method="GET" class="mt-8 flex max-w-lg items-center gap-2 rounded-full bg-white p-2 shadow-xl shadow-stone-200/60 ring-1 ring-stone-200 transition focus-within:ring-2 focus-within:ring-primer-400 dark:bg-stone-900 dark:shadow-none dark:ring-stone-800">
                    <span class="pl-3 text-xl">🔍</span>
                    <input type="search" name="cari" placeholder="Cari resep: rendang, mi goreng, kue..."
                        class="w-full bg-transparent py-2 outline-none placeholder:text-stone-400">
                    <button type="submit" class="shrink-0 rounded-full bg-primer-500 px-6 py-3 font-semibold text-white transition hover:bg-primer-600 active:scale-95">Cari</button>
                </form>

                {{-- Angka-angka kecil --}}
                <dl class="mt-10 flex gap-8">
                    <div><dt class="text-sm text-stone-500">Resep</dt><dd class="font-judul text-3xl font-bold">{{ $statistik['resep'] }}</dd></div>
                    <div><dt class="text-sm text-stone-500">Koki rumahan</dt><dd class="font-judul text-3xl font-bold">{{ $statistik['penulis'] }}</dd></div>
                    <div><dt class="text-sm text-stone-500">Kategori</dt><dd class="font-judul text-3xl font-bold">{{ $statistik['kategori'] }}</dd></div>
                </dl>
            </div>

            {{-- Kolase foto kanan: melayang perlahan.
                 Memakai 2 resep terpopuler, jadi hanya tampil kalau resepnya minimal ada 2. --}}
            @if ($resepPopuler->count() >= 2)
                @php [$utama, $kedua] = $resepPopuler; @endphp
                <div class="relative mx-auto h-[26rem] w-full max-w-md sm:h-[30rem]">
                    <img src="{{ $utama->foto_url }}" alt="{{ $utama->judul }}"
                        style="--putar: -4deg"
                        class="absolute left-0 top-6 w-64 animate-melayang rounded-[2rem] object-cover shadow-2xl ring-8 ring-white sm:w-72 dark:ring-stone-900">
                    <img src="{{ $kedua->foto_url }}" alt="{{ $kedua->judul }}"
                        style="--putar: 6deg; animation-delay: -2s"
                        class="absolute bottom-0 right-0 w-56 animate-melayang-lambat rounded-[2rem] object-cover shadow-2xl ring-8 ring-white sm:w-64 dark:ring-stone-900">

                    {{-- Kartu kecil melayang: info resep terpopuler --}}
                    <div style="animation-delay: -4s" class="absolute bottom-16 left-2 flex max-w-60 animate-melayang items-center gap-3 rounded-2xl bg-white/95 p-3 pr-5 shadow-xl backdrop-blur dark:bg-stone-900/95">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-daun-100 text-2xl">{{ $utama->kategori->ikon }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold">{{ $utama->judul }}</p>
                            <p class="whitespace-nowrap text-xs text-stone-400">🔥 Terpopuler · {{ $utama->dilihat }}× dilihat</p>
                        </div>
                    </div>
                    <div style="animation-delay: -1s" class="absolute right-4 top-0 animate-melayang-lambat rounded-2xl bg-primer-500 px-4 py-3 text-white shadow-xl shadow-primer-500/30">
                        <p class="text-xs opacity-80">Waktu masak</p>
                        <p class="font-judul text-xl font-bold">⏱️ {{ $kedua->waktu }} mnt</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ===================== KATEGORI ===================== --}}
    {{-- id="kategori" + scroll-mt: tujuan link "Kategori" di navbar (berhenti sedikit di bawah navbar) --}}
    <section id="kategori" class="mx-auto max-w-6xl scroll-mt-24 px-4 sm:px-6">
        <div class="flex items-end justify-between">
            <div>
                <p class="font-semibold text-primer-600">Mau masak apa hari ini?</p>
                <h2 class="mt-1 font-judul text-3xl font-bold sm:text-4xl">Jelajahi kategori</h2>
            </div>
        </div>

        {{-- Di HP bisa digeser ke samping; di laptop jadi grid.
             snap-x + snap-start: kartu "mengunci" rapi setelah digeser.
             scroll-px-4: titik kuncinya 16px dari tepi layar (sama dengan margin), supaya kartu tidak menempel ke tepi. --}}
        <div class="scrollbar-none -mx-4 mt-6 flex snap-x scroll-px-4 gap-4 overflow-x-auto px-4 pb-6 pt-2 sm:mx-0 sm:grid sm:grid-cols-4 sm:overflow-visible sm:px-0 lg:grid-cols-8">
            @foreach ($kategori as $item)
                <a href="{{ route('jelajah', ['kategori' => $item->slug]) }}"
                    class="group flex w-28 shrink-0 snap-start flex-col items-center gap-3 rounded-3xl bg-white p-4 text-center shadow-sm ring-1 ring-stone-200/70 transition duration-300 hover:-translate-y-1 hover:bg-primer-500 hover:shadow-xl hover:shadow-primer-500/20 active:scale-95 active:bg-primer-500 sm:w-auto dark:bg-stone-900 dark:ring-stone-800 dark:hover:bg-primer-600">
                    <span class="grid size-14 place-items-center rounded-2xl bg-primer-50 text-3xl transition duration-300 group-hover:rotate-12 group-hover:scale-110 group-hover:bg-white/20 dark:bg-stone-800">{{ $item->ikon }}</span>
                    <span>
                        <span class="block text-sm font-semibold transition group-hover:text-white">{{ $item->nama }}</span>
                        {{-- resep_count berasal dari withCount('resep') di controller --}}
                        <span class="block text-xs text-stone-400 transition group-hover:text-white/80">{{ $item->resep_count }} resep</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ===================== RESEP POPULER ===================== --}}
    <section class="mx-auto mt-20 max-w-6xl px-4 sm:px-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="font-semibold text-primer-600">🔥 Lagi banyak dimasak</p>
                <h2 class="mt-1 font-judul text-3xl font-bold sm:text-4xl">Resep populer</h2>
            </div>
            <a href="{{ route('jelajah', ['urut' => 'populer']) }}" class="group hidden items-center gap-1 font-semibold text-primer-600 sm:flex">
                Lihat semua <span class="transition group-hover:translate-x-1">→</span>
            </a>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($resepPopuler as $resep)
                {{-- Muncul perlahan saat digulir ke layar (x-intersect) --}}
                <div x-data="{ tampil: false }" x-intersect.once="tampil = true"
                    :class="tampil ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                    class="transition duration-700 ease-out"
                    style="transition-delay: {{ $loop->index * 100 }}ms">
                    <x-kartu-resep :resep="$resep" />
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===================== AJAKAN MENULIS ===================== --}}
    <section class="mx-auto mt-24 max-w-6xl px-4 sm:px-6">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-primer-500 to-primer-700 px-8 py-14 text-white shadow-2xl shadow-primer-500/30 sm:px-14">
            <div class="pointer-events-none absolute -right-10 -top-10 text-[12rem] leading-none opacity-15">🍲</div>
            <div class="relative max-w-xl">
                <h2 class="font-judul text-3xl font-bold sm:text-4xl">Bingung mau masak apa?</h2>
                <p class="mt-4 text-lg text-white/85">Ceritakan bahan yang ada di kulkasmu ke Chef AI, atau cari sendiri berdasarkan bahan, misalnya "telur" atau "santan".</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('chef-ai') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 font-bold text-primer-700 shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0 active:scale-95">
                        🧑‍🍳 Tanya Chef AI
                    </a>
                    <a href="{{ route('jelajah') }}" class="inline-flex items-center gap-2 rounded-full px-7 py-3.5 font-bold text-white ring-2 ring-white/60 transition hover:bg-white/10 active:scale-95 active:bg-white/10">
                        🔍 Cari berdasarkan bahan
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== RESEP TERBARU ===================== --}}
    <section class="mx-auto mt-24 max-w-6xl px-4 sm:px-6">
        <div>
            <p class="font-semibold text-primer-600">✨ Baru saja dibagikan</p>
            <h2 class="mt-1 font-judul text-3xl font-bold sm:text-4xl">Resep terbaru</h2>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($resepTerbaru as $resep)
                <div x-data="{ tampil: false }" x-intersect.once="tampil = true"
                    :class="tampil ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                    class="transition duration-700 ease-out"
                    style="transition-delay: {{ $loop->index * 100 }}ms">
                    <x-kartu-resep :resep="$resep" />
                </div>
            @endforeach
        </div>
    </section>

</x-layouts::app>
