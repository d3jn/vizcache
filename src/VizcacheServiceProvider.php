<?php

namespace D3jn\Vizcache;

use D3jn\Vizcache\Console\Commands\MakeAnalystCommand;
use D3jn\Vizcache\Console\Commands\MakeManagerCommand;
use Illuminate\Support\ServiceProvider;

class VizcacheServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/vizcache.php' => config_path('vizcache.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAnalystCommand::class,
                MakeManagerCommand::class
            ]);
        }
    }

    /**
     * Registers this package's services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/vizcache.php',
            'vizcache'
        );

        $this->app->singleton('vizcache', 'D3jn\Vizcache\Vizcache');
    }
}
