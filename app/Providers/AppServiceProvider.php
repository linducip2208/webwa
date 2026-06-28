<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\LicenseClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // AutoReplyListener tidak didaftarkan manual di sini: Laravel meng-auto-discover
        // listener di app/Listeners (AutoReplyListener@handle untuk MessageReceived).
        // Mendaftarkannya manual juga akan membuat listener terpanggil 2x → bot membalas
        // dua kali per pesan masuk.
    }
}
