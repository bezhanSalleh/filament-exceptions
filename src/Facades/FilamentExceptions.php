<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Facades;

use Illuminate\Support\Facades\Facade;
use Throwable;

/**
 * @method static void report(Throwable $exception)
 * @method static bool store(array $data)
 * @method static string|null getModel()
 * @method static void model(string $model)
 * @method static string|null getCluster()
 * @method static void cluster(string $cluster)
 * @method static bool shouldCapture(Throwable $exception)
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
