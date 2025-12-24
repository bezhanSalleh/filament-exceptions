<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use Throwable;

/**
 * @method static void report(Throwable $exception)
 * @method static bool store(array $data)
 * @method static string|null getModel()
 * @method static void model(string $model)
 * @method static string|null getCluster()
 * @method static void cluster(string $cluster)
 * @method static void stopRecording()
 * @method static void startRecording()
 * @method static bool isRecording()
 * @method static void recordUsing(?Closure $callback)
 * @method static string renderCss()
 * @method static string renderJs()
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
