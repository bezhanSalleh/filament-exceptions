<?php

namespace BezhanSalleh\FilamentExceptions;


use BezhanSalleh\FilamentExceptions\Models\Exception as ExceptionModel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\LaravelIgnition\Recorders\QueryRecorder\QueryRecorder;
use Throwable;

class FilamentExceptions
{
    /**
     * @var Request
     */
    protected $request;

    protected Application $app;

    /**
     * Reporter constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return void
     */
    public static function report(Throwable $exception)
    {
        $reporter = new static(request());

        $reporter->reportException($exception);
    }

    /** @return ExceptionModel */
    public static function model()
    {
        $class = config('filament-exceptions.exception_model');
        return $class;
    }

    /**
     * @return void
     */
    public function reportException(Throwable $exception)
    {
        $data = [
            'method' => request()->getMethod(),
            'ip' => implode(' ', json_decode(json_encode(request()->getClientIps()))),
            'path' => request()->path(),
            'query' => app()->make(QueryRecorder::class)->getQueries(), //Arr::except(request()->all(), ['_pjax', '_token', '_method', '_previous_']),
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

        try {
            $this->store($data);
        }
        catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Convert all items to string.
     */
    public function stringify($data) : array
    {
        return array_map(function ($item) {
            return is_array($item) ? json_encode($item, JSON_OBJECT_AS_ARRAY) : (string) $item;
        }, $data);
    }

    /**
     * Store exception info to db.
     */
    public function store(array $data) : bool
    {
        try {
            $this->model()::query()->create($data);

            return true;
        }
        catch (Throwable $e) {
            return false;
        }
    }

    public static function formatFileName(string $fileName) : string
    {
        return str($fileName)
            ->after(str(request()->getHost())->beforeLast('.')->toString())
            ->afterLast('/')
            ->prepend('.../')
            ->toString();
    }
}
