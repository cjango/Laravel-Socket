<?php

namespace RuLong\Socket;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{

    protected $commands = [
        Commands::class,
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
            $this->publishes([__DIR__ . '/config.php' => config_path('workerman.php')]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'workerman');
    }
}
