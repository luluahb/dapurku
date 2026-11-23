{{-- Halaman Chef AI. Data & logika di App\Livewire\ChefAi --}}
<div class="mx-auto max-w-3xl px-4 pt-8 sm:px-6">

    {{-- ===================== JUDUL ===================== --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="font-semibold text-primer-600">🤖 Asisten masak pintar</p>
            <h1 class="mt-1 font-judul text-4xl font-bold tracking-tight sm:text-5xl">Chef AI</h1>
            <p class="mt-3 text-stone-600 dark:text-stone-400">
                Ceritakan bahan yang kamu punya atau masakan yang kamu mau. Chef AI mencarikan resep yang cocok dari koleksi Dapurku.
            </p>
        </div>
        @if ($percakapan)
            <button type="button" wire:click="mulaiUlang"
                class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold text-stone-500 ring-1 ring-stone-200 transition hover:bg-stone-100 active:scale-95 dark:ring-stone-700 dark:hover:bg-stone-800">
                ↺ Mulai ulang
            </button>
        @endif
    </div>

    {{-- ===================== AREA OBROLAN ===================== --}}
    <div class="mt-8 space-y-6">

        @if (! $percakapan)
            {{-- Belum ada obrolan: sapaan + contoh pertanyaan --}}
            <div class="animate-muncul rounded-[2rem] bg-white p-6 text-center shadow-sm ring-1 ring-stone-200/70 sm:p-10 dark:bg-stone-900 dark:ring-stone-800">
                <p class="text-6xl">🧑‍🍳</p>
                <h2 class="mt-4 font-judul text-2xl font-bold">Halo! Mau masak apa hari ini?</h2>
                <p class="mt-2 text-stone-500">Coba salah satu pertanyaan ini:</p>
                <div class="mt-6 grid gap-2 sm:grid-cols-2">
                    @foreach ($contoh as $nomor => $teks)
                        <button type="button" wire:click="tanyaContoh({{ $nomor }})"
                            class="rounded-2xl bg-primer-50 px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-primer-100 hover:text-primer-700 active:scale-[0.98] active:bg-primer-100 dark:bg-stone-800 dark:text-stone-200 dark:hover:bg-stone-700">
                            {{ $teks }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @foreach ($percakapan as $i => $pesan)
            {{-- x-init: pesan baru otomatis digulir supaya terlihat --}}
            <div wire:key="pesan-{{ $i }}" x-data x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'nearest' })"
                @class(['flex animate-muncul gap-3', 'justify-end' => $pesan['peran'] === 'user'])>

                @if ($pesan['peran'] === 'user')
                    <p class="max-w-[85%] whitespace-pre-line rounded-3xl rounded-br-lg bg-primer-500 px-5 py-3 text-white shadow-sm shadow-primer-500/20">{{ $pesan['teks'] }}</p>
                @else
                    <span class="hidden size-10 shrink-0 sm:grid place-items-center rounded-2xl bg-primer-50 text-xl dark:bg-stone-800">🧑‍🍳</span>
                    <div class="min-w-0 flex-1 space-y-3 sm:max-w-[85%] sm:flex-none">
                        {{-- whitespace-pre-line: baris baru dari AI tetap tampil sebagai baris baru.
                             {{ }} otomatis "meng-escape" teks AI, jadi aman dari HTML/script berbahaya. --}}
                        <p class="whitespace-pre-line rounded-3xl rounded-tl-lg bg-white px-5 py-3 shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">{{ $pesan['teks'] }}</p>

                        {{-- Kartu resep yang direkomendasikan --}}
                        @foreach ($pesan['resep_id'] as $id)
                            @if ($r = $resep->get($id))
                                <a href="{{ route('resep.show', $r) }}"
                                    class="group flex items-center gap-3 rounded-2xl bg-white p-2 pr-4 shadow-sm ring-1 ring-stone-200/70 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-primer-300 active:scale-[0.98] dark:bg-stone-900 dark:ring-stone-800">
                                    <img src="{{ $r->foto_url }}" alt="" loading="lazy"
                                        class="size-16 shrink-0 rounded-xl bg-stone-200 object-cover dark:bg-stone-800">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-bold group-hover:text-primer-600">{{ $r->judul }}</p>
                                        <p class="text-sm text-stone-500">{{ $r->kategori?->ikon }} {{ $r->kategori?->nama }} · ⏱ {{ $r->waktu }} menit · {{ $r->kesulitan }}</p>
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
            <div class="flex animate-muncul gap-3" x-data x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'nearest' })">
        @else
            <div wire:loading.flex wire:target="tanya, tanyaContoh" class="animate-muncul gap-3">
        @endif
                <span class="grid size-10 shrink-0 place-items-center rounded-2xl bg-primer-50 text-xl dark:bg-stone-800">🧑‍🍳</span>
                <div class="flex items-center gap-1.5 rounded-3xl rounded-tl-lg bg-white px-5 py-4 shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800" aria-label="Chef AI sedang mengetik">
                    <span class="size-2 animate-bounce rounded-full bg-primer-400"></span>
                    <span class="size-2 animate-bounce rounded-full bg-primer-400 [animation-delay:150ms]"></span>
                    <span class="size-2 animate-bounce rounded-full bg-primer-400 [animation-delay:300ms]"></span>
                </div>
            </div>
    </div>

    {{-- ===================== KOTAK KETIK =====================
         sticky bottom-4: kotak ketik selalu terlihat di bawah layar saat obrolan panjang --}}
    <form wire:submit="tanya" class="sticky bottom-4 z-10 mt-8">
        <div class="flex items-end gap-2 rounded-[1.75rem] bg-white p-2 shadow-xl shadow-stone-900/10 ring-1 ring-stone-200 focus-within:ring-2 focus-within:ring-primer-400 dark:bg-stone-900 dark:ring-stone-700">
            {{-- Enter = kirim, Shift+Enter = baris baru --}}
            <textarea wire:model="pertanyaan" rows="1" maxlength="300"
                placeholder="Mau masak apa hari ini?"
                @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $el.form.requestSubmit() }"
                x-data x-init="$el.style.height = 'auto'"
                @input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 160) + 'px'"
                class="max-h-40 flex-1 resize-none border-0 bg-transparent px-4 py-3 placeholder:text-stone-400 focus:ring-0 focus:outline-none"
                aria-label="Pertanyaan untuk Chef AI"></textarea>
            <button type="submit" @disabled($menunggu) aria-label="Kirim"
                class="grid size-12 shrink-0 place-items-center rounded-full bg-primer-500 text-white shadow-lg shadow-primer-500/30 transition hover:bg-primer-600 active:scale-90 disabled:opacity-50">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" /></svg>
            </button>
        </div>
        @error('pertanyaan')
            <p class="mt-2 rounded-full bg-rose-50 px-4 py-2 text-center text-sm text-rose-600 dark:bg-rose-950/50 dark:text-rose-300">{{ $message }}</p>
        @enderror
    </form>

    <p class="mt-4 text-center text-xs text-stone-400">
        Chef AI memakai Google Gemini dan bisa saja keliru. Jangan menulis data pribadi di sini.
    </p>
</div>
