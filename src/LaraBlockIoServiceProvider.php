<?php

namespace Blockavel\LaraBlockIo;

use Illuminate\Support\ServiceProvider;

class LaraBlockIoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/main.php' => config_path('larablockio.php'),
        ]);

        $file = __DIR__.'/../vendor/autoload.php';

        if (file_exists($file)) {
            require $file;
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('lara-block-io', function () {
            return new LaraBlockIo();
        });
    }
}
