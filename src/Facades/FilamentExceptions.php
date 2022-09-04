<?php

namespace BezhanSalleh\FilamentExceptions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BezhanSalleh\FilamentExceptions\FilamentExceptions
 */
class FilamentExceptions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \BezhanSalleh\FilamentExceptions\FilamentExceptions::class;
    }
}
