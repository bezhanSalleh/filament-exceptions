<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use Filament\Clusters\Cluster;
use Illuminate\Foundation\Exceptions\Renderer\Exception;
use Illuminate\Foundation\Exceptions\Renderer\Listener;
use Illuminate\Foundation\Exceptions\Renderer\Mappers\BladeMapper;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Throwable;

class FilamentExceptions
{
    protected static ?string $model = null;

    /** @var class-string<Cluster>|null */
    protected static ?string $cluster = null;

    public static function report(Throwable $throwable): void
    {
        try {
            if (! static::shouldCapture($throwable)) {
                return;
            }

            // Use Laravel's exact same process as Renderer::render()
            $flattenException = app(BladeMapper::class)->map(
                app(HtmlErrorRenderer::class)->render($throwable)
            );

            $exception = new Exception(
                $flattenException,
                request(),
                app(Listener::class),
                base_path()
            );

            // Generate markdown exactly like Laravel's Renderer does
            $markdown = view('laravel-exceptions-renderer::markdown', [
                'exception' => $exception,
            ])->render();

            // Store all the data
            static::store([
                'type' => $exception->class(),
                'code' => (string) $exception->code(),
                'message' => $exception->message(),
                'file' => $flattenException->getFile(),
                'line' => $flattenException->getLine(),
                'trace' => $flattenException->getTrace(),
                'method' => request()->getMethod(),
                'path' => request()->path(),
                'ip' => request()->ip(),
                'query' => $exception->applicationQueries(),
                'headers' => $exception->requestHeaders(),
                'body' => request()->all() ?: null,
                'cookies' => request()->cookies->all() ?: null,
                'route_context' => $exception->applicationRouteContext() ?: null,
                'route_parameters' => request()->route()?->parameters() ?: null,
                'markdown' => $markdown,
            ]);
        } catch (Throwable) {
            // Silent fail
        }
    }

    public static function shouldCapture(Throwable $exception): bool
    {
        $file = $exception->getFile();
        $message = $exception->getMessage();

        if (blank($file) || ! str($file)->endsWith('.php')) {
            return false;
        }

        if (str_contains($file, ")'d code")) {
            return false;
        }

        if (blank($message)) {
            return false;
        }

        if (str_contains($message, '__VSCODE_LARAVEL_')) {
            return false;
        }

        return $exception->getLine() > 0;
    }

    public static function store(array $data): bool
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

    public static function renderCss(): string
    {
        return '<style>' . file_get_contents(__DIR__ . '/../resources/dist/styles.css') . '</style>';
    }

    /**
     * Get the exception renderer's JavaScript content.
     */
    public static function renderJs(): string
    {
        return '<script type="module">' . file_get_contents(__DIR__ . '/../resources/dist/scripts.js') . '</script>';
    }
}
