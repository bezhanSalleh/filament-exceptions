<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Trace;

use SplFileObject;
use Throwable;

class Frame
{
    protected string $file = '';

    protected int $line = 0;

    protected ?string $class = null;

    protected ?string $method = null;

    protected bool $isApplicationFrame = true;

    protected ?CodeBlock $codeBlock = null;

    /**
     * Create a Frame from array data (spatie/backtrace format).
     *
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $data = [])
    {
        try {
            $this->file = (string) ($data['file'] ?? '');
            $this->line = (int) ($data['line'] ?? 0);
            $this->class = $data['class'] ?? null;
            $this->method = $data['method'] ?? null;
            $this->isApplicationFrame = (bool) ($data['isApplicationFrame'] ?? $this->detectApplicationFrame());

            $this->fetchCodeBlock();
        } catch (Throwable) {
            // Silent fail - ensure frame is always safe to use
        }
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function class(): ?string
    {
        return $this->class;
    }

    public function method(): string
    {
        return $this->method ?? '';
    }

    public function isApplicationFrame(): bool
    {
        return $this->isApplicationFrame;
    }

    public function isVendorFrame(): bool
    {
        return ! $this->isApplicationFrame;
    }

    public function getCodeBlock(): CodeBlock
    {
        return $this->codeBlock ?? new CodeBlock;
    }

    /**
     * Get a shortened file path relative to base path.
     */
    public function shortFilePath(): string
    {
        try {
            $basePath = base_path() . '/';

            return str_starts_with($this->file, $basePath)
                ? substr($this->file, strlen($basePath))
                : $this->file;
        } catch (Throwable) {
            return $this->file;
        }
    }

    /**
     * Get just the filename without path.
     */
    public function filename(): string
    {
        return basename($this->file);
    }

    /**
     * Detect if this frame is an application frame (not vendor).
     */
    protected function detectApplicationFrame(): bool
    {
        if (blank($this->file)) {
            return false;
        }

        // Vendor directory = not application frame
        if (str_contains($this->file, '/vendor/')) {
            return false;
        }

        // CLI tools are considered vendor frames
        return ! str_ends_with($this->file, 'artisan') && ! str_ends_with($this->file, 'please');
    }

    /**
     * Fetch code block around the line.
     */
    protected function fetchCodeBlock(): void
    {
        if (blank($this->file) || $this->line <= 0) {
            return;
        }

        if (! file_exists($this->file) || ! is_readable($this->file)) {
            return;
        }

        try {
            $contextLines = 5;
            $file = new SplFileObject($this->file);
            $startLine = max(0, $this->line - $contextLines - 1);
            $file->seek($startLine);

            $currentLine = $startLine + 1;
            $focusLine = '';
            $prefix = '';
            $suffix = '';

            while (! $file->eof() && $currentLine <= $this->line + $contextLines) {
                $lineContent = $file->current();

                if ($currentLine === $this->line) {
                    $focusLine = $lineContent;
                } elseif ($currentLine < $this->line) {
                    $prefix .= $lineContent;
                } else {
                    $suffix .= $lineContent;
                }

                $currentLine++;
                $file->next();
            }

            $this->codeBlock = new CodeBlock($startLine + 1, $focusLine, $prefix, $suffix);
        } catch (Throwable) {
            // Silent fail - code block is optional
        }
    }
}
