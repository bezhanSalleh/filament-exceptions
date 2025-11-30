<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\QueryRecorder\QueryRecorder;
use Filament\Clusters\Cluster;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Backtrace\Backtrace;
use Throwable;

class FilamentExceptions
{
    protected static ?string $model = null;

    /** @var class-string<Cluster>|null */
    protected static ?string $cluster = null;

    public function __construct(
        protected Request $request
    ) {}

    public static function report(Throwable $exception): void
    {
        try {
            if (! static::shouldCapture($exception)) {
                return;
            }

            $reporter = new self(request());
            $reporter->reportException($exception);
        } catch (Throwable) {
            // Never throw while handling exceptions - silent fail
        }
    }

    /**
     * Determine if the exception should be captured.
     */
    public static function shouldCapture(Throwable $exception): bool
    {
        $file = $exception->getFile();
        $message = $exception->getMessage();

        // Skip empty/invalid file paths
        if (blank($file) || ! str($file)->endsWith('.php')) {
            return false;
        }

        // Skip eval'd code
        if (str_contains($file, "eval()'d code")) {
            return false;
        }

        // Skip empty messages
        if (blank($message)) {
            return false;
        }

        // Skip VSCode Laravel extension noise
        if (str_contains($message, '__VSCODE_LARAVEL_')) {
            return false;
        }

        // Skip invalid line numbers
        return $exception->getLine() > 0;
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

    public function reportException(Throwable $exception): void
    {
        try {
            $data = [
                'method' => $this->getMethod(),
                'ip' => $this->getClientIp(),
                'path' => $this->getPath(),
                'query' => $this->getQueries(),
                'body' => $this->getBody(),
                'cookies' => $this->getCookies(),
                'headers' => $this->getHeaders(),

                'type' => $exception::class,
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace' => $this->buildTrace($exception),
            ];

            $data = $this->stringify($data);
            $this->store($data);
        } catch (Throwable) {
            // Silent fail - never throw while handling exceptions
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|false>
     */
    public function stringify(array $data): array
    {
        return array_map(
            fn ($item): string | false => is_array($item) ? json_encode($item, JSON_OBJECT_AS_ARRAY) : (string) $item,
            $data
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data): bool
    {
        try {
            $model = static::getModel();

            if (! $model || ! class_exists($model)) {
                return false;
            }

            $model::create($data);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Build trace data using spatie/backtrace.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildTrace(Throwable $exception): array
    {
        try {
            $frames = Backtrace::createForThrowable($exception)
                ->applicationPath(base_path())
                ->frames();

            return collect($frames)
                ->map(fn ($frame): array => [
                    'file' => $frame->file,
                    'line' => $frame->lineNumber,
                    'class' => $frame->class,
                    'method' => $frame->method,
                    'isApplicationFrame' => $frame->applicationFrame,
                ])
                ->filter(fn ($frame): bool => filled($frame['file']) && $frame['line'] > 0)
                ->values()
                ->toArray();
        } catch (Throwable) {
            // Fallback to basic trace if spatie/backtrace fails
            return collect($exception->getTrace())
                ->map(fn ($frame): array => [
                    'file' => $frame['file'] ?? null,
                    'line' => $frame['line'] ?? null,
                    'class' => $frame['class'] ?? null,
                    'method' => $frame['function'],
                    'isApplicationFrame' => isset($frame['file']) && ! str_contains($frame['file'], '/vendor/'),
                ])
                ->filter(fn ($frame): bool => filled($frame['file']) && $frame['line'] > 0)
                ->values()
                ->toArray();
        }
    }

    protected function getMethod(): string
    {
        try {
            return request()->getMethod();
        } catch (Throwable) {
            return 'CLI';
        }
    }

    protected function getClientIp(): string
    {
        try {
            return implode(', ', request()->getClientIps());
        } catch (Throwable) {
            return '';
        }
    }

    protected function getPath(): string
    {
        try {
            return request()->path();
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getQueries(): array
    {
        try {
            return app()->make(QueryRecorder::class)->getQueries();
        } catch (Throwable) {
            return [];
        }
    }

    protected function getBody(): string
    {
        try {
            return request()->getContent() ?: '';
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getCookies(): array
    {
        try {
            return request()->cookies->all();
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getHeaders(): array
    {
        try {
            return Arr::except(request()->headers->all(), 'cookie');
        } catch (Throwable) {
            return [];
        }
    }
}
