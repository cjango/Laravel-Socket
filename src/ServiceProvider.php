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
        $this->commands($this->commands);

        if ($this->app->runningInConsole()) {

        }
    }

    public function register()
    {

    }
}
