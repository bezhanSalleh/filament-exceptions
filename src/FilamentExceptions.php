<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use Closure;
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

    protected static bool $isRecording = true;

    protected static ?Closure $shouldRecordCallback = null;

    public static function report(Throwable $throwable): void
    {
        try {
            if (! static::isRecording()) {
                return;
            }

            if (static::$shouldRecordCallback instanceof Closure && ! call_user_func(static::$shouldRecordCallback, $throwable)) {
                return;
            }

            if (! static::shouldCapture($throwable)) {
                return;
            }

            // Ensure Listener is bound (Laravel only binds it when APP_DEBUG=true)
            if (! app()->bound(Listener::class)) {
                app()->singleton(Listener::class);
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

            // Generate markdown if the view exists (Laravel 12+), otherwise null
            // @phpstan-ignore method.impossibleType (view exists at runtime in Laravel 12+)
            $markdown = view()->exists('laravel-exceptions-renderer::markdown')
                ? view('laravel-exceptions-renderer::markdown', ['exception' => $exception])->render()
                : null;

            // Store all the data
            // Note: $exception->code() only exists in Laravel 12+, use $flattenException->getCode() for compatibility
            static::store([
                'type' => $exception->class(),
                'code' => (string) $flattenException->getCode(),
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

    public static function stopRecording(): void
    {
        static::$isRecording = false;
    }

    public static function startRecording(): void
    {
        static::$isRecording = true;
    }

    public static function isRecording(): bool
    {
        return static::$isRecording;
    }

    public static function recordUsing(?Closure $callback): void
    {
        static::$shouldRecordCallback = $callback;
    }

    public static function renderCss(): string
    {
        $css = file_get_contents(__DIR__ . '/../resources/dist/styles.css');

        // Override Tailwind colors to use Filament's theme variables
        // This must come after the main CSS to win in the cascade
        $colorOverrides = '
            :root {
                --color-neutral-50: var(--gray-50);
                --color-neutral-100: var(--gray-100);
                --color-neutral-200: var(--gray-200);
                --color-neutral-300: var(--gray-300);
                --color-neutral-400: var(--gray-400);
                --color-neutral-500: var(--gray-500);
                --color-neutral-600: var(--gray-600);
                --color-neutral-700: var(--gray-700);
                --color-neutral-800: var(--gray-800);
                --color-neutral-900: var(--gray-900);
                --color-neutral-950: var(--gray-950);
                --color-rose-50: var(--danger-50);
                --color-rose-100: var(--danger-100);
                --color-rose-200: var(--danger-200);
                --color-rose-500: var(--danger-500);
                --color-rose-600: var(--danger-600);
                --color-rose-900: var(--danger-900);
                --color-rose-950: var(--danger-950);
                --color-blue-50: var(--primary-50);
                --color-blue-100: var(--primary-100);
                --color-blue-300: var(--primary-300);
                --color-blue-500: var(--primary-500);
                --color-blue-600: var(--primary-600);
                --color-blue-700: var(--primary-700);
                --color-blue-800: var(--primary-800);
                --color-blue-900: var(--primary-900);
                --color-blue-950: var(--primary-950);
                --color-emerald-200: var(--success-200);
                --color-emerald-400: var(--success-400);
                --color-emerald-500: var(--success-500);
                --color-emerald-600: var(--success-600);
                --color-emerald-900: var(--success-900);
                --color-amber-200: var(--warning-200);
                --color-amber-300: var(--warning-300);
                --color-amber-500: var(--warning-500);
                --color-amber-600: var(--warning-600);
                --color-amber-800: var(--warning-800);
                --color-amber-900: var(--warning-900);
                --color-amber-950: var(--warning-950);
            }
        ';

        return '<style>' . $css . $colorOverrides . '</style>';
    }

    /**
     * Get the exception renderer's JavaScript content.
     */
    public static function renderJs(): string
    {
        return '<script type="module">' . file_get_contents(__DIR__ . '/../resources/dist/scripts.js') . '</script>';
    }
}
