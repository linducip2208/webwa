<?php

namespace App\Providers;

use App\Listeners\AutoReplyListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Kstmostofa\LaravelWhatsApp\Events\Web\MessageReceived;

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
        Event::listen(MessageReceived::class, AutoReplyListener::class);
    }
}
