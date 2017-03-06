<?php

namespace Blockavel\Blockavel;

use Illuminate\Support\ServiceProvider;

class BlockavelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/main.php' => config_path('blockavel.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('blockavel-blockavel', function() {
            return new Blockavel;
        });
        
        $this->mergeConfigFrom(
            config_path('blockavel.php'), 'blockavel-blockavel'
        );
    }
}