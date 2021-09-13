<?php

namespace MgSoftware\Mutex;

use Illuminate\Support\ServiceProvider;

class MutexServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mutex', fn($app) => new MySQLMutex());
    }
}
