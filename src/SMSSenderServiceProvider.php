<?php

namespace Accunity\SMSSender;

use Illuminate\Support\ServiceProvider;

class SMSSenderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => base_path('config/SMSSender.php')
        ]);

        $this->app->bind('SMSConfig', 'Accunity\SMSSender\SMSConfig');
    }
}
