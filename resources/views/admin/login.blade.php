{{-- Form login admin --}}
<x-layouts::app title="Masuk Admin">
    <section class="relative overflow-hidden px-4 py-16 sm:py-24">
        <div class="pointer-events-none absolute -left-24 top-0 size-80 rounded-full bg-primer-300/30 blur-3xl dark:bg-primer-700/15"></div>
        <div class="pointer-events-none absolute -right-24 bottom-0 size-80 rounded-full bg-amber-200/40 blur-3xl dark:bg-amber-700/10"></div>

        <div class="relative mx-auto max-w-md">
            <div class="text-center">
                <span class="mx-auto grid size-16 animate-melayang place-items-center rounded-3xl bg-primer-500 text-3xl shadow-xl shadow-primer-500/30">🔐</span>
                <h1 class="mt-6 font-judul text-4xl font-bold">Masuk Admin</h1>
                <p class="mt-2 text-stone-500 dark:text-stone-400">Khusus pengelola Dapurku untuk menambah dan mengubah resep.</p>
            </div>

            <form method="POST" action="{{ route('admin.login') }}"
                class="mt-8 space-y-5 rounded-[2rem] bg-white p-6 shadow-xl shadow-stone-200/60 ring-1 ring-stone-200/70 sm:p-8 dark:bg-stone-900 dark:shadow-none dark:ring-stone-800">
                @csrf

                <div>
                    <label for="email" class="text-sm font-semibold">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        @class([
                            'mt-1.5 w-full rounded-2xl bg-stone-50 px-4 py-3 ring-1 outline-none transition focus:bg-white focus:ring-2 focus:ring-primer-400 dark:bg-stone-800 dark:focus:bg-stone-800',
                            'ring-rose-400' => $errors->has('email'),
                            'ring-stone-200 dark:ring-stone-700' => ! $errors->has('email'),
                        ])>
                    @error('email')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol 👁️ untuk melihat/menyembunyikan password (Alpine) --}}
                <div x-data="{ lihat: false }">
                    <label for="password" class="text-sm font-semibold">Kata sandi</label>
                    <div class="relative mt-1.5">
                        <input :type="lihat ? 'text' : 'password'" type="password" id="password" name="password" required
                            class="w-full rounded-2xl bg-stone-50 px-4 py-3 pr-12 ring-1 ring-stone-200 outline-none transition focus:bg-white focus:ring-2 focus:ring-primer-400 dark:bg-stone-800 dark:ring-stone-700 dark:focus:bg-stone-800">
                        <button type="button" @click="lihat = !lihat" :aria-label="lihat ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'"
                            class="absolute right-2 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-xl text-lg transition hover:bg-stone-200/60 dark:hover:bg-stone-700">
                            <span x-text="lihat ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex cursor-pointer items-center gap-2 text-sm">
                    <input type="checkbox" name="ingat" class="size-4 accent-primer-500">
                    Ingat saya
                </label>

                <button type="submit"
                    class="w-full rounded-2xl bg-primer-500 py-3.5 font-bold text-white shadow-lg shadow-primer-500/30 transition hover:-translate-y-0.5 hover:bg-primer-600 active:translate-y-0">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-sm">
                <a href="{{ route('beranda') }}" class="font-semibold text-stone-500 hover:text-primer-600">← Kembali ke Beranda</a>
            </p>
        </div>
    </section>
</x-layouts::app>
