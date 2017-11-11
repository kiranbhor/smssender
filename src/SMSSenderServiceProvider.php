<?php

namespace Accunity\SMSSender;


use Accunity\SMSSender\Commands\UpdateSMSStatus;
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
        $this->publishes([
            __DIR__.'/config.php' => config_path('smssender.php'),
        ]);


        $this->loadMigrationsFrom(__DIR__.'/database/migrations');


        if ($this->app->runningInConsole()) {
            $this->commands([
               UpdateSMSStatus::class
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
