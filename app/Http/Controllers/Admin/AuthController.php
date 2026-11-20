<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Login khusus admin. Tidak ada halaman daftar.
class AuthController extends Controller
{
    // GET /admin/login → tampilkan form
    public function create()
    {
        return view('admin.login');
    }

    // POST /admin/login → proses login
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [], ['password' => 'kata sandi']);

        // Syarat tambahan 'is_admin' => true: user biasa (penulis contoh) tidak bisa masuk
        // walaupun email & password-nya benar.
        $berhasil = Auth::attempt(
            [...$data, 'is_admin' => true],
            $request->boolean('ingat')
        );

        if (! $berhasil) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate(); // ganti ID sesi, mencegah pembajakan sesi

        return redirect()->intended(route('admin.resep.index'))
            ->with('pesan', 'Selamat datang kembali, ' . Auth::user()->name . '! 👋');
    }

    // POST /admin/logout
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('pesan', 'Kamu sudah keluar. Sampai jumpa! 👋');
    }
}
