<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use App\Models\PengajuanBantuan;
use App\Policies\PengajuanBantuanPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paksa HTTPS di lingkungan produksi / Vercel cloud
        if (config('app.env') === 'production' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Daftarkan Policy Otorisasi & Anti-IDOR Pengajuan Bantuan
        Gate::policy(PengajuanBantuan::class, PengajuanBantuanPolicy::class);

        // Daftarkan Custom Tailwind Pagination View sebagai Default Aplikasi
        Paginator::defaultView('vendor.pagination.custom-tailwind');
        Paginator::defaultSimpleView('vendor.pagination.custom-tailwind');
    }
}
