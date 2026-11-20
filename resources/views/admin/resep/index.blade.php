{{-- Dashboard admin: daftar resep. Data dari Admin\ResepController@index --}}
<x-layouts::app title="Kelola Resep">
    <div class="mx-auto max-w-6xl px-4 pt-8 sm:px-6">

        {{-- Judul + tombol tambah --}}
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="font-semibold text-primer-600">⚙️ Dashboard admin</p>
                <h1 class="mt-1 font-judul text-4xl font-bold">Kelola resep</h1>
            </div>
            <a href="{{ route('admin.resep.create') }}"
                class="inline-flex items-center gap-2 rounded-full bg-primer-500 px-6 py-3 font-bold text-white shadow-lg shadow-primer-500/30 transition hover:-translate-y-0.5 hover:bg-primer-600 active:translate-y-0">
                ✍️ Tambah resep
            </a>
        </div>

        {{-- Statistik --}}
        <dl class="mt-8 grid gap-4 sm:grid-cols-3">
            @foreach ([
                ['📖', 'Total resep', $statistik['resep']],
                ['👀', 'Total dilihat', number_format($statistik['dilihat'], 0, ',', '.')],
                ['🗂️', 'Kategori', $statistik['kategori']],
            ] as [$ikon, $label, $angka])
                <div class="flex items-center gap-4 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
                    <span class="grid size-12 place-items-center rounded-2xl bg-primer-50 text-2xl dark:bg-stone-800">{{ $ikon }}</span>
                    <div>
                        <dt class="text-sm text-stone-500">{{ $label }}</dt>
                        <dd class="font-judul text-2xl font-bold">{{ $angka }}</dd>
                    </div>
                </div>
            @endforeach
        </dl>

        {{-- Pencarian sederhana (form GET biasa, bukan Livewire) --}}
        <form method="GET" class="mt-8 flex gap-2">
            <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari judul resep..."
                class="w-full rounded-full bg-white px-5 py-3 ring-1 ring-stone-200 outline-none focus:ring-2 focus:ring-primer-400 sm:max-w-sm dark:bg-stone-900 dark:ring-stone-800">
            <button class="rounded-full bg-stone-800 px-5 font-semibold text-white transition hover:bg-stone-700 dark:bg-stone-700">Cari</button>
            @if ($cari)
                <a href="{{ route('admin.resep.index') }}" class="grid place-items-center px-3 text-sm font-semibold text-primer-600">Reset</a>
            @endif
        </form>

        {{-- Daftar resep --}}
        <div class="mt-6 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-stone-200/70 dark:bg-stone-900 dark:ring-stone-800">
            @forelse ($resep as $item)
                <div class="flex flex-wrap items-center gap-4 border-b border-stone-100 p-4 last:border-0 sm:flex-nowrap dark:border-stone-800">
                    {{-- Foto kecil --}}
                    <div class="size-16 shrink-0 overflow-hidden rounded-2xl bg-primer-50 dark:bg-stone-800">
                        @if ($item->foto_url)
                            <img src="{{ $item->foto_url }}" alt="" class="size-full object-cover">
                        @else
                            <span class="grid size-full place-items-center text-2xl">{{ $item->kategori->ikon }}</span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold">{{ $item->judul }}</p>
                        <p class="mt-0.5 text-sm text-stone-500">
                            {{ $item->kategori->ikon }} {{ $item->kategori->nama }}
                            · ⏱️ {{ $item->waktu }} mnt · 👀 {{ $item->dilihat }}
                            · {{ $item->created_at->translatedFormat('j M Y') }}
                        </p>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="flex shrink-0 items-center gap-1">
                        <a href="{{ route('resep.show', $item) }}" target="_blank" title="Lihat di website"
                            class="grid size-10 place-items-center rounded-xl transition hover:bg-stone-100 dark:hover:bg-stone-800">👁️</a>
                        <a href="{{ route('admin.resep.edit', $item) }}" title="Edit"
                            class="grid size-10 place-items-center rounded-xl transition hover:bg-stone-100 dark:hover:bg-stone-800">✏️</a>
                        {{-- Hapus: form DELETE + konfirmasi bawaan browser.
                             Js::from() mengubah teks PHP jadi teks JavaScript yang aman (tanda kutip di judul tidak merusak kode) --}}
                        <form method="POST" action="{{ route('admin.resep.destroy', $item) }}"
                            onsubmit="return confirm({{ Js::from('Yakin hapus resep "' . $item->judul . '"? Tidak bisa dibatalkan.') }})">
                            @csrf
                            @method('DELETE')
                            <button title="Hapus" class="grid size-10 place-items-center rounded-xl transition hover:bg-rose-100 dark:hover:bg-rose-500/15">🗑️</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <p class="text-5xl">🍳</p>
                    <p class="mt-3 font-semibold">Belum ada resep{{ $cari ? ' yang cocok' : '' }}.</p>
                </div>
            @endforelse
        </div>

        {{-- Tombol halaman 1, 2, 3... (dibuat otomatis oleh paginate()) --}}
        <div class="mt-6">
            {{ $resep->links() }}
        </div>
    </div>
</x-layouts::app>
