<?php


namespace MgSoftware\Mutex;

use Illuminate\Support\Facades\Facade;

class MutexFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mutex';
    }
}