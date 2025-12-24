<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use BezhanSalleh\FilamentExceptions\Models\Exception as ExceptionModel;
use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Foundation\Exceptions\Renderer\Frame;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

/**
 * A stored exception that provides the same interface as Laravel's Exception renderer class.
 * This allows us to use Laravel's exception renderer components directly with stored exception data.
 */
class StoredException
{
    protected FlattenException $flattenException;

    protected Request $request;

    /** @var array<string, string> */
    protected array $classMap = [];

    public function __construct(
        protected ExceptionModel $record
    ) {
        // Create a dummy FlattenException - we'll override the methods we need
        $this->flattenException = FlattenException::createFromThrowable(new Exception(''));
        $this->request = $this->createRequest();
        $this->buildClassMap();
    }

    /**
     * Get the exception title (status text).
     */
    public function title(): string
    {
        return 'Internal Server Error';
    }

    /**
     * Get the exception message.
     */
    public function message(): string
    {
        return $this->record->message ?? '';
    }

    /**
     * Get the exception class name.
     */
    public function class(): string
    {
        return class_basename($this->record->type ?? 'Exception');
    }

    /**
     * Get the full exception class name.
     */
    public function fullClass(): string
    {
        return $this->record->type ?? 'Exception';
    }

    /**
     * Get the exception code.
     */
    public function code(): int | string
    {
        return $this->record->code ?? 0;
    }

    /**
     * Get the HTTP status code.
     */
    public function httpStatusCode(): int
    {
        return 500;
    }

    /**
     * Get the exception's frames.
     *
     * @return Collection<int, Frame>
     */
    public function frames(): Collection
    {
        return once(function (): \Illuminate\Support\Collection {
            $trace = $this->decodeTrace();
            $basePath = base_path();
            $frames = [];
            $previousFrame = null;

            // Process frames in reverse order like Laravel does
            foreach (array_reverse($trace) as $frameData) {
                $frame = new Frame(
                    $this->flattenException,
                    $this->classMap,
                    [
                        'file' => $frameData['file'] ?? '',
                        'line' => $frameData['line'] ?? 0,
                        'class' => $frameData['class'] ?? null,
                        'type' => $frameData['type'] ?? null,
                        'function' => $frameData['function'] ?? null,
                        'args' => $frameData['args'] ?? [],
                    ],
                    $basePath,
                    $previousFrame
                );
                $frames[] = $frame;
                $previousFrame = $frame;
            }

            // Reverse back to original order
            $frames = array_reverse($frames);

            // Mark first non-vendor frame as main
            foreach ($frames as $frame) {
                if (! $frame->isFromVendor()) {
                    $frame->markAsMain();

                    break;
                }
            }

            return new Collection($frames);
        });
    }

    /**
     * Get the exception's frames grouped by vendor status.
     *
     * @return array<int, array{is_vendor: bool, frames: array<int, Frame>}>
     */
    public function frameGroups(): array
    {
        $groups = [];

        foreach ($this->frames() as $frame) {
            $isVendor = $frame->isFromVendor();

            if ($groups === [] || $groups[array_key_last($groups)]['is_vendor'] !== $isVendor) {
                $groups[] = [
                    'is_vendor' => $isVendor,
                    'frames' => [],
                ];
            }

            $groups[array_key_last($groups)]['frames'][] = $frame;
        }

        return $groups;
    }

    /**
     * Get the exception's request instance.
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * Get the request's headers.
     *
     * @return array<string, string>
     */
    public function requestHeaders(): array
    {
        $headers = $this->record->headers ?? [];

        return array_map(fn ($header): string => is_array($header) ? implode(', ', $header) : (string) $header, $headers);
    }

    /**
     * Get the request's body.
     */
    public function requestBody(): ?string
    {
        $body = $this->record->body ?? [];

        if (empty($body)) {
            return null;
        }

        if (is_string($body)) {
            return $body;
        }

        return json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: null;
    }

    /**
     * Get the application's route context.
     *
     * @return array<string, string>
     */
    public function applicationRouteContext(): array
    {
        return $this->record->route_context ?? [];
    }

    /**
     * Get the application's route parameters context.
     */
    public function applicationRouteParametersContext(): ?string
    {
        $parameters = $this->record->route_parameters;

        if (empty($parameters)) {
            return null;
        }

        return json_encode($parameters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: null;
    }

    /**
     * Get the application's SQL queries.
     *
     * @return array<int, array{connectionName: string, time: float, sql: string}>
     */
    public function applicationQueries(): array
    {
        $queries = $this->record->query ?? [];

        return array_map(fn (array $query): array => [
            'connectionName' => $query['connectionName'] ?? $query['connection'] ?? 'default',
            'time' => (float) ($query['time'] ?? 0),
            'sql' => $query['sql'] ?? '',
        ], $queries);
    }

    /**
     * Get the stored record.
     */
    public function record(): ExceptionModel
    {
        return $this->record;
    }

    /**
     * Get the stored markdown.
     */
    public function markdown(): string
    {
        return $this->record->markdown ?? '';
    }

    /**
     * Create a mock Request object from stored data.
     */
    protected function createRequest(): Request
    {
        $request = Request::create(
            uri: '/' . ltrim($this->record->path ?? '', '/'),
            method: $this->record->method ?? 'GET',
            server: [
                'REMOTE_ADDR' => $this->record->ip ?? '127.0.0.1',
            ]
        );

        // Set headers from stored data
        if ($headers = $this->record->headers) {
            foreach ($headers as $key => $value) {
                $request->headers->set($key, $value);
            }
        }

        return $request;
    }

    /**
     * Decode trace from JSON string or return array as-is.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function decodeTrace(): array
    {
        $trace = $this->record->trace;

        if (is_array($trace)) {
            return $trace;
        }

        if (is_string($trace)) {
            $decoded = json_decode($trace, true);

            if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return [];
    }

    /**
     * Build class map from stored trace data.
     */
    protected function buildClassMap(): void
    {
        // First, try to get from Composer's autoloader
        $loaders = ClassLoader::getRegisteredLoaders();
        if (! empty($loaders)) {
            $this->classMap = array_map(fn (string $path): string => (string) realpath($path), array_values($loaders)[0]->getClassMap());
        }

        // Also add classes from our stored trace (in case files moved)
        foreach ($this->decodeTrace() as $frameData) {
            if (isset($frameData['class'], $frameData['file'])) {
                $realPath = @realpath($frameData['file']);
                if ($realPath && ! isset($this->classMap[$frameData['class']])) {
                    $this->classMap[$frameData['class']] = $realPath;
                }
            }
        }
    }
}
