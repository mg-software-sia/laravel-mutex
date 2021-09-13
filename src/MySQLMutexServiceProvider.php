<?php

namespace MgSoftware\MySQLMutex;

use Illuminate\Support\ServiceProvider;

class MySQLMutexServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mutex', fn($app) => new MySQLMutex());
    }
}
