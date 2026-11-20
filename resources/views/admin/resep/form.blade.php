{{--
    Form resep. SATU file dipakai untuk TAMBAH dan EDIT.
    $resep->exists = true kalau resepnya sudah ada di database (berarti mode edit).
--}}
@php
    $sedangEdit = $resep->exists;

    // Isi awal daftar bahan & langkah:
    // 1. old() = isian sebelumnya kalau validasi gagal (supaya tidak hilang)
    // 2. kalau edit → ambil dari database
    // 3. kalau tambah → 3 baris kosong
    $awalBahan = old('bahan', $sedangEdit ? $resep->bahan->pluck('isi')->all() : ['', '', '']);
    $awalLangkah = old('langkah', $sedangEdit ? $resep->langkah->pluck('isi')->all() : ['', '']);

    // Class yang sama untuk semua kotak isian
    $kelasInput = 'w-full rounded-2xl bg-stone-50 px-4 py-3 ring-1 ring-stone-200 outline-none transition focus:bg-white focus:ring-2 focus:ring-primer-400 dark:bg-stone-800 dark:ring-stone-700 dark:focus:bg-stone-800';
    $kelasKartu = 'rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-stone-200/70 sm:p-8 dark:bg-stone-900 dark:ring-stone-800';
@endphp

<x-layouts::app :title="$sedangEdit ? 'Edit Resep' : 'Tambah Resep'">
    <div class="mx-auto max-w-3xl px-4 pt-8 sm:px-6">

        <a href="{{ route('admin.resep.index') }}" class="text-sm font-semibold text-stone-500 hover:text-primer-600">← Kembali ke daftar resep</a>
        <h1 class="mt-3 font-judul text-4xl font-bold">{{ $sedangEdit ? 'Edit resep' : 'Tambah resep baru' }}</h1>
        @if ($sedangEdit)
            <p class="mt-1 text-stone-500">{{ $resep->judul }}</p>
        @endif

        {{-- Ringkasan error di atas, supaya admin tahu ada isian yang salah --}}
        @if ($errors->any())
            <div class="mt-6 rounded-2xl bg-rose-50 p-4 text-rose-700 ring-1 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/30">
                <p class="font-semibold">⚠️ Ada {{ $errors->count() }} isian yang perlu diperbaiki:</p>
                <ul class="mt-1 list-inside list-disc text-sm">
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- enctype="multipart/form-data" WAJIB untuk form yang mengirim file (foto) --}}
        <form method="POST" enctype="multipart/form-data"
            action="{{ $sedangEdit ? route('admin.resep.update', $resep) : route('admin.resep.store') }}"
            class="mt-8 space-y-6">
            @csrf
            @if ($sedangEdit)
                @method('PUT')
            @endif

            {{-- ===================== 1. INFO DASAR ===================== --}}
            <section class="{{ $kelasKartu }} space-y-5">
                <h2 class="font-judul text-2xl font-bold">📝 Info dasar</h2>

                <div>
                    <label for="judul" class="text-sm font-semibold">Judul resep</label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul', $resep->judul) }}" required
                        placeholder="Contoh: Soto Ayam Kuah Bening" class="{{ $kelasInput }} mt-1.5">
                    @error('judul') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="kategori_id" class="text-sm font-semibold">Kategori</label>
                    <select id="kategori_id" name="kategori_id" required class="{{ $kelasInput }} mt-1.5 cursor-pointer">
                        <option value="">Pilih kategori...</option>
                        @foreach ($daftarKategori as $k)
                            <option value="{{ $k->id }}" @selected(old('kategori_id', $resep->kategori_id) == $k->id)>
                                {{ $k->ikon }} {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Penghitung huruf langsung (Alpine) --}}
                <div x-data="{ teks: @js(old('deskripsi', $resep->deskripsi ?? '')) }">
                    <div class="flex items-center justify-between">
                        <label for="deskripsi" class="text-sm font-semibold">Deskripsi singkat</label>
                        <span class="text-xs tabular-nums" :class="teks.length > 1000 ? 'text-rose-600' : 'text-stone-400'"
                            x-text="teks.length + '/1000'"></span>
                    </div>
                    <textarea id="deskripsi" name="deskripsi" rows="3" required x-model="teks"
                        placeholder="Ceritakan sedikit tentang masakan ini..." class="{{ $kelasInput }} mt-1.5"></textarea>
                    @error('deskripsi') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="waktu" class="text-sm font-semibold">Waktu memasak (menit)</label>
                        <input type="number" id="waktu" name="waktu" min="1" max="1440" required
                            value="{{ old('waktu', $resep->waktu) }}" placeholder="30" class="{{ $kelasInput }} mt-1.5">
                        @error('waktu') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="porsi" class="text-sm font-semibold">Porsi (orang)</label>
                        <input type="number" id="porsi" name="porsi" min="1" max="50" required
                            value="{{ old('porsi', $resep->porsi) }}" class="{{ $kelasInput }} mt-1.5">
                        @error('porsi') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Kesulitan: radio button yang dibuat seperti tombol pil --}}
                <fieldset>
                    <legend class="text-sm font-semibold">Tingkat kesulitan</legend>
                    <div class="mt-2 flex gap-2">
                        @foreach (['Mudah' => 'peer-checked:bg-daun-500', 'Sedang' => 'peer-checked:bg-amber-500', 'Sulit' => 'peer-checked:bg-rose-500'] as $tingkat => $warna)
                            <label class="cursor-pointer">
                                {{-- sr-only = radio asli disembunyikan; tampilannya diganti span di bawahnya.
                                     peer-checked: = gaya span saat radio di sebelahnya terpilih --}}
                                <input type="radio" name="kesulitan" value="{{ $tingkat }}" class="peer sr-only"
                                    @checked(old('kesulitan', $resep->kesulitan) === $tingkat)>
                                <span class="block rounded-full bg-stone-100 px-5 py-2 font-semibold text-stone-600 transition peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-primer-400 dark:bg-stone-800 dark:text-stone-300 {{ $warna }}">
                                    {{ $tingkat }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('kesulitan') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                </fieldset>
            </section>

            {{-- ===================== 2. FOTO ===================== --}}
            {{-- Pratinjau: begitu file dipilih, URL.createObjectURL membuat link sementara
                 supaya fotonya langsung terlihat SEBELUM di-upload --}}
            <section class="{{ $kelasKartu }}" x-data="{ pratinjau: @js($resep->foto_url) }">
                <h2 class="font-judul text-2xl font-bold">📸 Foto</h2>
                <p class="mt-1 text-sm text-stone-500">JPG, PNG, atau WEBP. Maksimal 2 MB. {{ $sedangEdit ? 'Kosongkan kalau tidak ingin mengganti foto.' : 'Boleh dikosongkan.' }}</p>

                <label class="group mt-4 block cursor-pointer overflow-hidden rounded-3xl border-2 border-dashed border-stone-300 transition hover:border-primer-400 dark:border-stone-700">
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="sr-only"
                        @change="if ($event.target.files[0]) pratinjau = URL.createObjectURL($event.target.files[0])">

                    <div x-show="pratinjau" x-cloak class="relative aspect-[16/9]">
                        <img :src="pratinjau" alt="Pratinjau foto" class="size-full object-cover">
                        <span class="absolute inset-0 grid place-items-center bg-black/40 font-semibold text-white opacity-0 transition group-hover:opacity-100">🔄 Ganti foto</span>
                    </div>
                    <div x-show="!pratinjau" class="grid aspect-[16/9] place-items-center bg-stone-50 text-center dark:bg-stone-800/50">
                        <div>
                            <p class="text-5xl transition group-hover:scale-110">🖼️</p>
                            <p class="mt-3 font-semibold">Klik untuk memilih foto</p>
                        </div>
                    </div>
                </label>
                @error('foto') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
            </section>

            {{-- ===================== 3. BAHAN =====================
                 Daftar dinamis dengan Alpine:
                 - x-for mengulang setiap item di array "daftar"
                 - name="bahan[]" → di PHP diterima sebagai array: $request->bahan
                 - id acak dipakai sebagai :key supaya Alpine tidak tertukar saat baris dihapus --}}
            <section class="{{ $kelasKartu }}"
                x-data="{
                    daftar: @js($awalBahan).map(isi => ({ id: Math.random(), isi })),
                    tambah() {
                        this.daftar.push({ id: Math.random(), isi: '' });
                        // $nextTick = tunggu baris baru muncul, lalu taruh kursor di sana
                        this.$nextTick(() => this.$refs.daftar.querySelector('li:last-child input').focus());
                    },
                }">
                <div class="flex items-center justify-between">
                    <h2 class="font-judul text-2xl font-bold">🧺 Bahan</h2>
                    <span class="text-sm text-stone-400" x-text="daftar.filter(b => b.isi.trim()).length + ' bahan'"></span>
                </div>
                <p class="mt-1 text-sm text-stone-500">Satu baris untuk satu bahan, lengkap dengan takarannya. Baris kosong diabaikan.</p>

                <ul x-ref="daftar" class="mt-4 space-y-2">
                    <template x-for="(bahan, i) in daftar" :key="bahan.id">
                        <li class="flex items-center gap-2">
                            <span class="w-6 shrink-0 text-right text-sm font-semibold text-stone-400" x-text="i + 1"></span>
                            <input type="text" name="bahan[]" x-model="bahan.isi" placeholder="Contoh: 500 gr daging sapi"
                                @keydown.enter.prevent="tambah()" class="{{ $kelasInput }} py-2.5">
                            <button type="button" @click="daftar.splice(i, 1)" x-show="daftar.length > 1" aria-label="Hapus bahan"
                                class="grid size-10 shrink-0 place-items-center rounded-xl text-stone-400 transition hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-500/15">✕</button>
                        </li>
                    </template>
                </ul>
                @error('bahan') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                @error('bahan.*') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror

                <button type="button" @click="tambah()"
                    class="mt-4 w-full rounded-2xl border-2 border-dashed border-stone-200 py-3 font-semibold text-stone-500 transition hover:border-primer-400 hover:text-primer-600 dark:border-stone-700">
                    + Tambah bahan <span class="text-xs font-normal">(atau tekan Enter)</span>
                </button>
            </section>

            {{-- ===================== 4. LANGKAH ===================== --}}
            <section class="{{ $kelasKartu }}"
                x-data="{
                    daftar: @js($awalLangkah).map(isi => ({ id: Math.random(), isi })),
                    tambah() {
                        this.daftar.push({ id: Math.random(), isi: '' });
                        this.$nextTick(() => this.$refs.daftar.querySelector('li:last-child textarea').focus());
                    },
                    // Tukar posisi langkah ke-i dengan langkah di atas/bawahnya
                    geser(i, arah) {
                        const j = i + arah;
                        if (j < 0 || j >= this.daftar.length) return;
                        [this.daftar[i], this.daftar[j]] = [this.daftar[j], this.daftar[i]];
                    },
                }">
                <h2 class="font-judul text-2xl font-bold">👩‍🍳 Langkah memasak</h2>
                <p class="mt-1 text-sm text-stone-500">Tulis berurutan. Gunakan tombol ↑ ↓ untuk mengubah urutan.</p>

                <ol x-ref="daftar" class="mt-4 space-y-3">
                    <template x-for="(langkah, i) in daftar" :key="langkah.id">
                        <li class="flex gap-3">
                            <span class="mt-1.5 grid size-9 shrink-0 place-items-center rounded-xl bg-primer-500 font-judul font-bold text-white" x-text="i + 1"></span>
                            <textarea name="langkah[]" x-model="langkah.isi" rows="2" placeholder="Jelaskan langkah ini..."
                                class="{{ $kelasInput }} py-2.5"></textarea>
                            <div class="flex shrink-0 flex-col gap-1">
                                <button type="button" @click="geser(i, -1)" :disabled="i === 0" aria-label="Naikkan"
                                    class="grid size-8 place-items-center rounded-lg text-stone-400 transition hover:bg-stone-100 disabled:opacity-30 dark:hover:bg-stone-800">↑</button>
                                <button type="button" @click="geser(i, 1)" :disabled="i === daftar.length - 1" aria-label="Turunkan"
                                    class="grid size-8 place-items-center rounded-lg text-stone-400 transition hover:bg-stone-100 disabled:opacity-30 dark:hover:bg-stone-800">↓</button>
                                <button type="button" @click="daftar.splice(i, 1)" x-show="daftar.length > 1" aria-label="Hapus langkah"
                                    class="grid size-8 place-items-center rounded-lg text-stone-400 transition hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-500/15">✕</button>
                            </div>
                        </li>
                    </template>
                </ol>
                @error('langkah') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                @error('langkah.*') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror

                <button type="button" @click="tambah()"
                    class="mt-4 w-full rounded-2xl border-2 border-dashed border-stone-200 py-3 font-semibold text-stone-500 transition hover:border-primer-400 hover:text-primer-600 dark:border-stone-700">
                    + Tambah langkah
                </button>
            </section>

            {{-- ===================== TOMBOL SIMPAN (menempel di bawah layar) ===================== --}}
            <div class="sticky bottom-4 z-10 flex items-center justify-end gap-3 rounded-full bg-white/80 p-2 shadow-xl ring-1 ring-stone-200/70 backdrop-blur-lg dark:bg-stone-900/80 dark:ring-stone-800">
                <a href="{{ route('admin.resep.index') }}" class="rounded-full px-5 py-3 font-semibold text-stone-500 transition hover:text-stone-800 dark:hover:text-stone-200">Batal</a>
                <button type="submit"
                    class="rounded-full bg-primer-500 px-8 py-3 font-bold text-white shadow-lg shadow-primer-500/30 transition hover:bg-primer-600 active:scale-95">
                    {{ $sedangEdit ? '💾 Simpan perubahan' : '✨ Terbitkan resep' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::app>
