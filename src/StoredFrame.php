<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions;

use Illuminate\Foundation\Concerns\ResolvesDumpSource;

use function array_slice;
use function count;
use function file;
use function is_array;
use function is_file;
use function is_readable;
use function is_string;

/**
 * A stored frame that provides Laravel 12-compatible interface for both L11 and L12.
 * This replaces direct usage of Laravel's Frame class to ensure cross-version compatibility.
 */
class StoredFrame
{
    use ResolvesDumpSource;

    protected bool $isMain = false;

    /**
     * @param  array<string, string>  $classMap
     * @param  array{file: string, line: int, class?: string|null, type?: string|null, function?: string|null, args?: array}  $frame
     */
    public function __construct(
        protected array $classMap,
        protected array $frame,
        protected string $basePath,
        protected ?StoredFrame $previous = null
    ) {}

    /**
     * Get the frame's source / origin.
     */
    public function source(): string
    {
        $class = $this->class();

        return $class ?? $this->file();
    }

    /**
     * Get the frame's editor link.
     */
    public function editorHref(): string
    {
        return $this->resolveSourceHref($this->frame['file'] ?? '', $this->line());
    }

    /**
     * Get the frame's class, if any.
     */
    public function class(): ?string
    {
        $file = $this->frame['file'] ?? '';
        if (empty($file)) {
            return null;
        }

        $class = array_search((string) realpath($file), $this->classMap, true);

        return $class === false ? null : $class;
    }

    /**
     * Get the frame's file.
     */
    public function file(): string
    {
        $file = $this->frame['file'] ?? null;

        return match (true) {
            ! isset($file) => '[internal function]',
            ! is_string($file) => '[unknown file]',
            default => str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $file),
        };
    }

    /**
     * Get the frame's line number.
     */
    public function line(): int
    {
        $file = $this->frame['file'] ?? '';
        $line = $this->frame['line'] ?? 0;

        if (! is_file($file) || ! is_readable($file)) {
            return 0;
        }

        $maxLines = count(file($file) ?: []);

        return $line > $maxLines ? 1 : $line;
    }

    /**
     * Get the frame's function operator.
     */
    public function operator(): string
    {
        return $this->frame['type'] ?? '';
    }

    /**
     * Get the frame's function or method.
     */
    public function callable(): string
    {
        return match (true) {
            ! empty($this->frame['function']) => $this->frame['function'],
            default => 'throw',
        };
    }

    /**
     * Get the frame's arguments.
     *
     * @return array<int, string>
     */
    public function args(): array
    {
        if (! isset($this->frame['args']) || ! is_array($this->frame['args']) || $this->frame['args'] === []) {
            return [];
        }

        return array_map(function ($argument): string {
            if (! is_array($argument) || count($argument) < 2) {
                return 'unknown';
            }

            [$key, $value] = $argument;

            return match ($key) {
                'object' => sprintf('%s(%s)', $key, $value),
                default => (string) $key,
            };
        }, $this->frame['args']);
    }

    /**
     * Get the frame's code snippet.
     */
    public function snippet(): string
    {
        $file = $this->frame['file'] ?? '';

        if (! is_file($file) || ! is_readable($file)) {
            return '';
        }

        $contents = file($file) ?: [];
        $start = max($this->line() - 6, 0);
        $length = 8 * 2 + 1;

        return implode('', array_slice($contents, $start, $length));
    }

    /**
     * Determine if the frame is from the vendor directory.
     */
    public function isFromVendor(): bool
    {
        $file = $this->frame['file'] ?? '';

        return ! str_starts_with($file, $this->basePath)
            || str_starts_with($file, $this->basePath . DIRECTORY_SEPARATOR . 'vendor');
    }

    /**
     * Get the previous frame.
     */
    public function previous(): ?StoredFrame
    {
        return $this->previous;
    }

    /**
     * Mark this frame as the main frame.
     */
    public function markAsMain(): void
    {
        $this->isMain = true;
    }

    /**
     * Determine if this is the main frame.
     */
    public function isMain(): bool
    {
        return $this->isMain;
    }
}
