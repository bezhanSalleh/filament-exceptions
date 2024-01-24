<?php

namespace BezhanSalleh\ExceptionPlugin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BezhanSalleh\ExceptionPlugin\ExceptionManager
 */
class FilamentExceptions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \BezhanSalleh\ExceptionPlugin\ExceptionManager::class;
    }
}
