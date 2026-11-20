<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gate "admin": izin yang hanya dimiliki user dengan is_admin = true.
        // Dipakai di route (middleware 'can:admin') dan di Blade (@can('admin')).
        Gate::define('admin', fn (User $user) => $user->is_admin);
    }
}
