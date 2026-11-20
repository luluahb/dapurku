{{-- 419: token CSRF kedaluwarsa (halaman dibiarkan terbuka terlalu lama sebelum form dikirim) --}}
<x-halaman-error kode="419" ikon="⏰" judul="Waktunya sudah habis"
    pesan="Halaman ini terbuka terlalu lama, jadi demi keamanan perlu dimuat ulang. Silakan kembali dan coba lagi.">
    <button type="button" onclick="history.back()"
        class="rounded-full bg-white px-6 py-3 font-bold shadow-lg ring-1 ring-stone-200 transition hover:-translate-y-0.5 active:translate-y-0 dark:bg-stone-900 dark:ring-stone-700">
        ← Kembali
    </button>
</x-halaman-error>
