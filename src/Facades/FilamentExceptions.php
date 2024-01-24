<?php

namespace BezhanSalleh\FilamentExceptions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void report(\Throwable $exception)
 * @method static string getModel()
 * @method static void reportException(\Throwable $exception)
 * @method static array stringify(void $data)
 * @method static bool store(array $data)
 *
 * @see \BezhanSalleh\FilamentExceptions\FilamentExceptions
 */
class FilamentExceptions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \BezhanSalleh\FilamentExceptions\FilamentExceptions::class;
    }
}
