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
        }
    }

    public function register()
    {

    }
}
