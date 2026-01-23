<?php

namespace App\Providers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Observers\PembayaranObserver;
use App\Observers\PemesananObserver;
use Illuminate\Support\ServiceProvider;

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
        Pemesanan::observe(PemesananObserver::class);
        Pembayaran::observe(PembayaranObserver::class);
    }
}
