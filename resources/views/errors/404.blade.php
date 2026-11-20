{{-- 404: halaman / resep tidak ditemukan --}}
<x-halaman-error kode="404" ikon="🍳" judul="Wah, resepnya hilang dari dapur"
    pesan="Halaman yang kamu cari tidak ada. Mungkin resepnya sudah dihapus atau alamatnya salah ketik.">
    <a href="{{ route('jelajah') }}"
        class="rounded-full bg-white px-6 py-3 font-bold shadow-sm ring-1 ring-stone-200 transition hover:-translate-y-0.5 hover:shadow-md active:translate-y-0 dark:bg-stone-900 dark:ring-stone-700">
        🔍 Cari resep lain
    </a>
</x-halaman-error>
