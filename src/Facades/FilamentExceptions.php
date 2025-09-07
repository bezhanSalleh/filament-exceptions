<?php

namespace BezhanSalleh\FilamentExceptions\Facades;

use Throwable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void report(Throwable $exception)
 * @method static string|null getModel()
 * @method static void model(string $model)
 * @method static void reportException(Throwable $exception)
 * @method static array stringify(void $data)
 * @method static bool store(array $data)
 *
 * @see \BezhanSalleh\FilamentExceptions\FilamentExceptions
 */
class FilamentExceptions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \BezhanSalleh\FilamentExceptions\FilamentExceptions::class;
    }
}
