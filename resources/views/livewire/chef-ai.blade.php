{{--
    Widget Chef AI: tombol 🧑‍🍳 di pojok kanan bawah + panel chat.
    Data & logika di App\Livewire\ChefAi. Dipasang di layouts/app.blade.php.

    Alpine (x-data) mengurus buka/tutup panel dan gelembung sapaan:
    - $persist(...).using(sessionStorage): panel tetap terbuka saat pindah halaman
    - @buka-chef-ai.window: bisa dibuka dari tombol lain, misalnya di beranda
    - ?chef-ai di alamat web (dari link /chef-ai): panel langsung terbuka
    - sapa: gelembung "Bingung mau masak apa?" muncul SEKALI per browser (diingat di localStorage)
--}}
<div x-data="{
        terbuka: $persist(false).using(sessionStorage).as('chef-ai-terbuka'),
        sapa: false,
        tampilkanSapa() {
            this.sapa = true;
            // Langsung dicatat begitu tampil, jadi tidak muncul lagi walau halaman di-reload.
            // try/catch: localStorage bisa diblokir browser (misal mode privat)
            try { localStorage.setItem('chef-ai-disapa', '1') } catch (e) {}
        },
    }"
    x-init="
        if (new URLSearchParams(location.search).has('chef-ai')) terbuka = true;

        let sudahDisapa = true;
        try { sudahDisapa = !! localStorage.getItem('chef-ai-disapa') } catch (e) {}

        // Sapa 2,5 detik setelah halaman terbuka, lalu hilang sendiri setelah 10 detik
        if (! sudahDisapa && ! terbuka) {
            setTimeout(() => {
                if (terbuka) return;
                tampilkanSapa();
                setTimeout(() => sapa = false, 10000);
            }, 2500);
        }

        // Begitu chat dibuka, gelembung sapaan tidak perlu lagi
        $watch('terbuka', (buka) => buka && (sapa = false));
    "
    x-effect="document.body.classList.toggle('max-sm:overflow-hidden', terbuka)"
    @buka-chef-ai.window="terbuka = true; $nextTick(() => $refs.ketik?.focus())"
    @keydown.escape.window="terbuka = false"
    class="print:hidden">

    {{-- ===================== GELEMBUNG SAPAAN ===================== --}}
    <div x-show="sapa && ! terbuka" x-cloak
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="translate-y-3 opacity-0"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-end="translate-y-3 opacity-0"
        class="fixed bottom-24 right-6 z-[70] flex max-w-[17rem] items-start gap-2 rounded-3xl rounded-br-lg bg-white py-3 pl-4 pr-2 shadow-xl shadow-stone-900/10 ring-1 ring-stone-200 dark:bg-stone-900 dark:ring-stone-700">
        <button type="button" @click="terbuka = true" class="text-left text-sm">
            <span class="font-bold">👋 Bingung mau masak apa?</span><br>
            <span class="text-stone-500 dark:text-stone-400">Ceritakan bahan yang kamu punya, nanti Chef AI carikan resepnya.</span>
        </button>
        <button type="button" @click="sapa = false" aria-label="Tutup sapaan"
            class="grid size-7 shrink-0 place-items-center rounded-full text-stone-400 transition hover:bg-stone-100 active:scale-90 dark:hover:bg-stone-800">✕</button>
    </div>

    {{-- ===================== TOMBOL CHEF AI =====================
         Tertutup: tombol "🧑‍🍳 Chef AI" (tulisannya selalu terlihat, termasuk di HP)
         Terbuka : tombol bulat ✕ (di HP disembunyikan, karena panel punya tombol ✕ sendiri) --}}
    <button type="button" @click="terbuka = !terbuka"
        :aria-label="terbuka ? 'Tutup Chef AI' : 'Buka Chef AI'" :aria-expanded="terbuka"
        :class="terbuka ? 'w-14 justify-center max-sm:hidden' : 'gap-2 pl-4 pr-5'"
        class="group fixed bottom-6 right-6 z-[70] flex h-14 items-center rounded-full bg-primer-500 font-bold text-white shadow-xl shadow-primer-500/40 transition hover:-translate-y-1 hover:bg-primer-600 active:scale-90">
        <span x-show="!terbuka" class="text-2xl transition group-hover:rotate-12">🧑‍🍳</span>
        <span x-show="!terbuka">Chef AI</span>
        <svg x-show="terbuka" x-cloak class="size-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
    </button>

    {{-- ===================== PANEL CHAT =====================
         HP     : memenuhi layar (inset-0)
         Laptop : jendela kecil di atas tombol (bottom-24 right-6, lebar 26rem) --}}
    <section x-show="terbuka" x-cloak role="dialog" aria-label="Chef AI"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="translate-y-6 opacity-0 sm:scale-95"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-end="translate-y-6 opacity-0 sm:scale-95"
        class="fixed inset-0 z-[80] flex origin-bottom-right flex-col bg-krem sm:inset-auto sm:bottom-24 sm:right-6 sm:h-[min(40rem,calc(100dvh-8rem))] sm:w-[26rem] sm:overflow-hidden sm:rounded-[2rem] sm:shadow-2xl sm:shadow-stone-900/20 sm:ring-1 sm:ring-stone-200 dark:bg-stone-950 sm:dark:ring-stone-800">

        {{-- Kepala panel --}}
        <header class="flex items-center gap-3 border-b border-stone-200/70 bg-white px-4 py-3 dark:border-stone-800 dark:bg-stone-900">
            <span class="grid size-10 place-items-center rounded-2xl bg-primer-50 text-xl dark:bg-stone-800">🧑‍🍳</span>
            <div class="min-w-0 flex-1">
                <h2 class="font-judul text-lg font-bold leading-tight">Chef AI</h2>
                <p class="truncate text-xs text-stone-500">Asisten masak · didukung Google Gemini</p>
            </div>
            @if ($percakapan)
                <button type="button" wire:click="mulaiUlang" title="Mulai ulang obrolan" aria-label="Mulai ulang obrolan"
                    class="grid size-9 place-items-center rounded-full text-stone-500 transition hover:bg-stone-100 active:scale-90 active:bg-stone-100 dark:hover:bg-stone-800 dark:active:bg-stone-800">↺</button>
            @endif
            <button type="button" @click="terbuka = false" aria-label="Tutup Chef AI"
                class="grid size-9 place-items-center rounded-full text-stone-500 transition hover:bg-stone-100 active:scale-90 active:bg-stone-100 dark:hover:bg-stone-800 dark:active:bg-stone-800">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </header>

        {{-- Isi obrolan (bisa digulir sendiri) --}}
        <div class="flex-1 space-y-4 overflow-y-auto overscroll-contain px-4 py-5">

            @if (! $percakapan)
                {{-- Belum ada obrolan: sapaan + contoh pertanyaan --}}
                <div class="text-center">
                    <p class="text-5xl">🧑‍🍳</p>
                    <h3 class="mt-3 font-judul text-xl font-bold">Halo! Mau masak apa hari ini?</h3>
                    <p class="mt-1 text-sm text-stone-500">Ceritakan bahan yang kamu punya, nanti aku carikan resep yang cocok dari Dapurku.</p>
                </div>
                <div class="grid gap-2">
                    @foreach ($contoh as $nomor => $teks)
                        <button type="button" wire:click="tanyaContoh({{ $nomor }})"
                            class="rounded-2xl bg-white px-4 py-3 text-left text-sm font-medium text-stone-700 ring-1 ring-stone-200/70 transition hover:bg-primer-50 hover:text-primer-700 hover:ring-primer-200 active:scale-[0.98] active:bg-primer-50 dark:bg-stone-900 dark:text-stone-200 dark:ring-stone-800 dark:hover:bg-stone-800">
                            {{ $teks }}
                        </button>
                    @endforeach
                </div>
            @endif

            @foreach ($percakapan as $i => $pesan)
                {{-- x-init: pesan baru otomatis digulir supaya terlihat --}}
                <div wire:key="pesan-{{ $i }}" x-data x-init="$el.scrollIntoView({ block: 'nearest' })"
                    @class(['flex', 'justify-end' => $pesan['peran'] === 'user'])>

                    @if ($pesan['peran'] === 'user')
                        <p class="max-w-[85%] whitespace-pre-line rounded-3xl rounded-br-lg bg-primer-500 px-4 py-2.5 text-white">{{ $pesan['teks'] }}</p>
                    @else
                        <div class="w-full space-y-2">
                            {{-- {{ }} otomatis "meng-escape" teks AI, jadi aman dari HTML/script berbahaya --}}
                            <p class="max-w-[92%] whitespace-pre-line rounded-3xl rounded-tl-lg bg-white px-4 py-2.5 ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">{{ $pesan['teks'] }}</p>

                            {{-- Kartu resep yang direkomendasikan. Di HP, panel ditutup saat resep diklik. --}}
                            @foreach ($pesan['resep_id'] as $id)
                                @if ($r = $resep->get($id))
                                    <a href="{{ route('resep.show', $r) }}" @click="if (window.innerWidth < 640) terbuka = false"
                                        class="group flex items-center gap-3 rounded-2xl bg-white p-2 pr-3 ring-1 ring-stone-200/70 transition hover:ring-primer-300 active:scale-[0.98] dark:bg-stone-900 dark:ring-stone-800">
                                        <img src="{{ $r->foto_url }}" alt="" loading="lazy"
                                            class="size-14 shrink-0 rounded-xl bg-stone-200 object-cover dark:bg-stone-800">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold group-hover:text-primer-600">{{ $r->judul }}</p>
                                            <p class="truncate text-xs text-stone-500">{{ $r->kategori?->ikon }} {{ $r->kategori?->nama }} · ⏱ {{ $r->waktu }} mnt · {{ $r->kesulitan }}</p>
                                        </div>
                                        <span class="text-primer-500 transition group-hover:translate-x-1">→</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Animasi "sedang mengetik":
                 - wire:loading → langsung tampil begitu tombol kirim ditekan (sebelum server membalas)
                 - $menunggu    → tetap tampil selama Gemini menyusun jawaban --}}
            @if ($menunggu)
                <div class="flex" x-data x-init="$el.scrollIntoView({ block: 'nearest' })">
            @else
                <div wire:loading.flex wire:target="tanya, tanyaContoh">
            @endif
                    <div class="flex items-center gap-1.5 rounded-3xl rounded-tl-lg bg-white px-5 py-4 ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800" aria-label="Chef AI sedang mengetik">
                        <span class="size-2 animate-bounce rounded-full bg-primer-400"></span>
                        <span class="size-2 animate-bounce rounded-full bg-primer-400 [animation-delay:150ms]"></span>
                        <span class="size-2 animate-bounce rounded-full bg-primer-400 [animation-delay:300ms]"></span>
                    </div>
                </div>
        </div>

        {{-- Kotak ketik --}}
        <form wire:submit="tanya" class="border-t border-stone-200/70 bg-white p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] dark:border-stone-800 dark:bg-stone-900">
            @error('pertanyaan')
                <p class="mb-2 rounded-xl bg-rose-50 px-3 py-2 text-center text-xs text-rose-600 dark:bg-rose-950/50 dark:text-rose-300">{{ $message }}</p>
            @enderror
            <div class="flex items-end gap-2 rounded-3xl bg-stone-100 p-1.5 focus-within:ring-2 focus-within:ring-primer-400 dark:bg-stone-800">
                {{-- Enter = kirim, Shift+Enter = baris baru --}}
                <textarea x-ref="ketik" wire:model="pertanyaan" rows="1" maxlength="300"
                    placeholder="Mau masak apa hari ini?"
                    @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $el.form.requestSubmit() }"
                    @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'"
                    class="max-h-30 flex-1 resize-none border-0 bg-transparent px-3 py-2 text-base placeholder:text-stone-400 focus:outline-none focus:ring-0"
                    aria-label="Pertanyaan untuk Chef AI"></textarea>
                <button type="submit" @disabled($menunggu) aria-label="Kirim"
                    class="grid size-10 shrink-0 place-items-center rounded-full bg-primer-500 text-white transition hover:bg-primer-600 active:scale-90 disabled:opacity-50">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" /></svg>
                </button>
            </div>
            <p class="mt-2 text-center text-[11px] text-stone-400">Chef AI bisa saja keliru. Jangan menulis data pribadi.</p>
        </form>
    </section>
</div>
