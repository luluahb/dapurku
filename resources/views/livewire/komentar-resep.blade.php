{{-- Komponen Livewire: komentar & rating. Data dari App\Livewire\KomentarResep --}}
<section id="komentar" class="mx-auto mt-24 max-w-6xl scroll-mt-24 px-4 sm:px-6 print:hidden">
    <p class="font-semibold text-primer-600">💬 Kata mereka yang sudah mencoba</p>
    <h2 class="mt-1 font-judul text-3xl font-bold sm:text-4xl">Ulasan & komentar</h2>

    <div class="mt-8 grid items-start gap-8 lg:grid-cols-[22rem_1fr]">

        {{-- ===================== KIRI: RINGKASAN + FORM ===================== --}}
        <div class="space-y-6 lg:sticky lg:top-24">

            {{-- Ringkasan rating --}}
            <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                @if ($daftar->isEmpty())
                    <p class="text-center text-stone-500">Belum ada ulasan.<br>Jadilah yang pertama! ✨</p>
                @else
                    <div class="flex items-center gap-4">
                        <p class="font-judul text-5xl font-bold">{{ number_format($rataRata, 1, ',') }}</p>
                        <div>
                            <p class="text-xl tracking-wider text-amber-400">
                                {{-- Bintang penuh sebanyak pembulatan rata-rata, sisanya abu-abu --}}
                                {{ str_repeat('★', (int) round($rataRata)) }}<span class="text-stone-300 dark:text-stone-600">{{ str_repeat('★', 5 - (int) round($rataRata)) }}</span>
                            </p>
                            <p class="text-sm text-stone-500">dari {{ $daftar->count() }} ulasan</p>
                        </div>
                    </div>

                    {{-- Grafik batang sebaran bintang --}}
                    <div class="mt-5 space-y-1.5">
                        @foreach ($sebaran as $bintang => $jumlah)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-6 shrink-0 text-stone-500">{{ $bintang }}★</span>
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-stone-100 dark:bg-stone-800">
                                    <div class="h-full rounded-full bg-amber-400 transition-all duration-700"
                                        style="width: {{ $jumlah / $daftar->count() * 100 }}%"></div>
                                </div>
                                <span class="w-5 shrink-0 text-right tabular-nums text-stone-400">{{ $jumlah }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Form komentar.
                 wire:submit="kirim" = saat form dikirim, jalankan method kirim() di PHP (tanpa reload) --}}
            <form wire:submit="kirim"
                x-data="{ terkirim: false }"
                @komentar-terkirim.window="terkirim = true; setTimeout(() => terkirim = false, 4000)"
                class="space-y-4 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                <h3 class="font-judul text-xl font-bold">Tulis ulasanmu</h3>

                {{-- Pilihan bintang.
                     Radio asli disembunyikan (sr-only) & terhubung ke $rating lewat wire:model.
                     Alpine hanya mengurus tampilan: bintang menyala saat disorot / dipilih. --}}
                <div x-data="{ bintang: 0, sorot: 0 }" @komentar-terkirim.window="bintang = 0">
                    <div class="flex items-center gap-1" @mouseleave="sorot = 0">
                        @for ($n = 1; $n <= 5; $n++)
                            <label class="cursor-pointer" @mouseenter="sorot = {{ $n }}">
                                <input type="radio" value="{{ $n }}" wire:model="rating" class="peer sr-only"
                                    @change="bintang = {{ $n }}">
                                <span class="inline-block text-3xl transition duration-150 peer-focus-visible:ring-2 peer-focus-visible:ring-primer-400"
                                    :class="(sorot || bintang) >= {{ $n }} ? 'scale-110 text-amber-400' : 'text-stone-300 dark:text-stone-600'">★</span>
                            </label>
                        @endfor
                        <span class="ml-2 text-sm font-semibold text-stone-500"
                            x-text="['Pilih bintang', 'Kurang', 'Lumayan', 'Enak', 'Enak banget', 'Juara! 🏆'][sorot || bintang]"></span>
                    </div>
                    @error('rating') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <input type="text" wire:model="nama" placeholder="Nama kamu" maxlength="50"
                        class="w-full rounded-2xl bg-stone-50 px-4 py-3 ring-1 ring-stone-200 outline-none transition focus:bg-white focus:ring-2 focus:ring-primer-400 dark:bg-stone-800 dark:ring-stone-700">
                    @error('nama') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <textarea wire:model="isi" rows="3" placeholder="Bagaimana hasil masakanmu?" maxlength="1000"
                        class="w-full rounded-2xl bg-stone-50 px-4 py-3 ring-1 ring-stone-200 outline-none transition focus:bg-white focus:ring-2 focus:ring-primer-400 dark:bg-stone-800 dark:ring-stone-700"></textarea>
                    @error('isi') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- wire:loading.attr="disabled" = tombol tidak bisa diklik dua kali selama mengirim --}}
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-2xl bg-primer-500 py-3 font-bold text-white shadow-lg shadow-primer-500/30 transition hover:bg-primer-600 active:scale-95 disabled:opacity-60">
                    <span wire:loading.remove wire:target="kirim">Kirim ulasan</span>
                    <span wire:loading wire:target="kirim">Mengirim...</span>
                </button>

                <p x-show="terkirim" x-cloak x-transition class="rounded-2xl bg-daun-100 p-3 text-center text-sm font-semibold text-daun-700">
                    🎉 Terima kasih, ulasanmu sudah tampil!
                </p>
            </form>
        </div>

        {{-- ===================== KANAN: DAFTAR KOMENTAR ===================== --}}
        <div class="space-y-4">
            @forelse ($daftar as $komentar)
                <article wire:key="komentar-{{ $komentar->id }}"
                    class="animate-muncul rounded-3xl bg-white p-5 shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                    <div class="flex items-start gap-3">
                        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-amber-300 to-primer-400 font-bold text-white">
                            {{ mb_strtoupper(mb_substr($komentar->nama, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-x-2">
                                <p class="font-semibold">{{ $komentar->nama }}</p>
                                <p class="text-sm tracking-wider text-amber-400">{{ str_repeat('★', $komentar->rating) }}<span class="text-stone-300 dark:text-stone-600">{{ str_repeat('★', 5 - $komentar->rating) }}</span></p>
                            </div>
                            {{-- diffForHumans: "2 jam yang lalu" (bahasa Indonesia karena APP_LOCALE=id) --}}
                            <p class="text-xs text-stone-400">{{ $komentar->created_at->diffForHumans() }}</p>
                            <p class="mt-2 whitespace-pre-line text-stone-700 dark:text-stone-300">{{ $komentar->isi }}</p>
                        </div>

                        {{-- Hanya admin yang melihat tombol hapus. wire:confirm = minta konfirmasi dulu --}}
                        @can('admin')
                            <button type="button" wire:click="hapus({{ $komentar->id }})" wire:confirm="Hapus komentar dari {{ $komentar->nama }}?"
                                title="Hapus komentar" class="grid size-9 shrink-0 place-items-center rounded-xl transition hover:bg-rose-100 dark:hover:bg-rose-500/15">🗑️</button>
                        @endcan
                    </div>
                </article>
            @empty
                <div class="rounded-[2rem] border-2 border-dashed border-stone-200 px-6 py-14 text-center dark:border-stone-800">
                    <p class="text-5xl">💬</p>
                    <p class="mt-3 font-semibold">Belum ada komentar</p>
                    <p class="text-sm text-stone-500">Sudah mencoba resep ini? Ceritakan hasilnya!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
