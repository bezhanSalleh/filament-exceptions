<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\QueryRecorder\QueryRecorder;
use Closure;
use Filament\Clusters\Cluster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        if ($exception->getLine() <= 0) {
            return false;
        }

        return true;
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
                // Exception details
                'type' => $exception::class,
                'code' => (string) $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $this->buildTrace($exception),

                // Request details
                'method' => $this->getMethod(),
                'path' => $this->getPath(),
                'ip' => $this->getClientIp() ?: null,

                // Request data
                'headers' => $this->getHeaders() ?: null,
                'cookies' => $this->getCookies() ?: null,
                'body' => $this->getBody() ?: null,
                'query' => $this->getQueries() ?: null,

                // Route context
                'route_context' => $this->getRouteContext() ?: null,
                'route_parameters' => $this->getRouteParameters() ?: null,
            ];

            $this->store($data);
        } catch (Throwable) {
            // Silent fail - never throw while handling exceptions
        }
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
     * Build trace data from native PHP exception trace.
     * Prepends the exception's own location as the first frame.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildTrace(Throwable $exception): array
    {
        try {
            $basePath = base_path();
            $frames = [];

            $exceptionFile = $exception->getFile();
            $exceptionLine = $exception->getLine();

            // First frame: the actual exception location (not included in getTrace())
            if ($exceptionFile && $exceptionLine > 0) {
                $frames[] = [
                    'file' => $exceptionFile,
                    'line' => $exceptionLine,
                    'class' => null,
                    'type' => null,
                    'function' => null,
                ];
            }

            // Remaining frames from the trace
            $traceFrames = collect($exception->getTrace())
                ->filter(fn (array $frame): bool => isset($frame['file']))
                ->map(fn (array $frame): array => [
                    'file' => $frame['file'],
                    'line' => $frame['line'] ?? 0,
                    'class' => $frame['class'] ?? null,
                    'type' => $frame['type'] ?? null,
                    'function' => $frame['function'] ?? null,
                ])
                ->filter(fn (array $frame): bool => $frame['line'] > 0)
                // Filter out any frame that matches the exception location
                ->filter(function (array $frame) use ($exceptionFile, $exceptionLine): bool {
                    if (! $exceptionFile || ! $exceptionLine) {
                        return true;
                    }

                    return ! (basename($frame['file']) === basename($exceptionFile)
                        && $frame['line'] === $exceptionLine);
                })
                ->values()
                ->toArray();

            return array_merge($frames, $traceFrames);
        } catch (Throwable) {
            return [];
        }
    }

    protected function getMethod(): string
    {
        try {
            return $this->request->getMethod();
        } catch (Throwable) {
            return 'CLI';
        }
    }

    protected function getClientIp(): ?string
    {
        try {
            $ips = $this->request->getClientIps();

            return $ips ? implode(', ', $ips) : null;
        } catch (Throwable) {
            return null;
        }
    }

    protected function getPath(): string
    {
        try {
            return $this->request->path();
        } catch (Throwable) {
            return '/';
        }
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    protected function getQueries(): ?array
    {
        try {
            $queries = app()->make(QueryRecorder::class)->getQueries();

            return $queries ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getBody(): ?array
    {
        try {
            $payload = $this->request->all();

            return $payload ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getCookies(): ?array
    {
        try {
            $cookies = $this->request->cookies->all();

            return $cookies ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getHeaders(): ?array
    {
        try {
            $headers = Arr::except($this->request->headers->all(), 'cookie');

            return $headers ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Get the application's route context.
     *
     * @return array<string, string>|null
     */
    protected function getRouteContext(): ?array
    {
        try {
            $route = $this->request->route();

            if (! $route) {
                return null;
            }

            $context = array_filter([
                'controller' => $route->getActionName(),
                'route name' => $route->getName(),
                'middleware' => implode(', ', array_map(
                    fn ($middleware) => $middleware instanceof Closure ? 'Closure' : $middleware,
                    $route->gatherMiddleware()
                )),
            ]);

            return $context ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Get the application's route parameters.
     *
     * @return array<string, mixed>|null
     */
    protected function getRouteParameters(): ?array
    {
        try {
            $parameters = $this->request->route()?->parameters();

            if (! $parameters) {
                return null;
            }

            // Convert models to arrays without relations
            return array_map(
                fn ($value) => $value instanceof Model ? $value->withoutRelations()->toArray() : $value,
                $parameters
            );
        } catch (Throwable) {
            return null;
        }
    }
}
