<?php

namespace MgSoftware\Mutex;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use MgSoftware\Mutex\Commands\MigrateCommand;

class MutexServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mutex', fn($app) => new MySQLMutex());

        if ($this->app->runningInConsole()) {
            $this->app->singleton('command.mutex-migrate', function ($app) {
                return new MigrateCommand($app['migrator'], $app[Dispatcher::class]);
                return new \Illuminate\Database\Console\Migrations\MigrateCommand($app['migrator'], $app[Dispatcher::class]);
            });
            $this->commands([
                'command.mutex-migrate'
            ]);
        }
    }
}
