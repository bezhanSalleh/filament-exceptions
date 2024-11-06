<?php

namespace BezhanSalleh\FilamentExceptions;

use Filament\Clusters\Cluster;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\LaravelIgnition\Recorders\QueryRecorder\QueryRecorder;
use Throwable;

class FilamentExceptions
{
    protected static ?string $model = null;

    /** @var class-string<Cluster> | null */
    protected static ?string $cluster = null;

    public function __construct(
        protected Request $request
    ) {}

    /**
     * @throws Throwable
     */
    public static function report(Throwable $exception): void
    {
        $reporter = new static(request());

        $reporter->reportException($exception);
    }

    public static function cluster(string $cluster): void
    {
        static::$cluster = $cluster;
    }

    public static function getCluster(): ?string
    {
        return static::$cluster;
    }

    public static function getModel(): ?string
    {
        return static::$model;
    }

    public static function model(string $model): void
    {
        static::$model = $model;
    }

    /**
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function reportException(Throwable $exception): void
    {
        $data = [
            'method' => request()->getMethod(),
            'ip' => implode(' ', json_decode(json_encode(request()->getClientIps()))),
            'path' => request()->path(),
            'query' => app()->make(QueryRecorder::class)->getQueries(),
            'body' => request()->getContent(),
            'cookies' => request()->cookies->all(),
            'headers' => Arr::except(request()->headers->all(), 'cookie'),

            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ];

        $data = $this->stringify($data);

        $this->store($data);
    }

    public function stringify($data): array
    {
        return array_map(function ($item) {
            return is_array($item) ? json_encode($item, JSON_OBJECT_AS_ARRAY) : (string) $item;
        }, $data);
    }

    public function store(array $data): bool
    {
        try {
            static::getModel()::create($data);

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }
}
