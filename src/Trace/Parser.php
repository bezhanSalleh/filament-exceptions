<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Trace;

use Throwable;

class Parser
{
    /**
     * @param  string|array<int, array<string, mixed>>|null  $trace
     */
    public function __construct(
        protected string | array | null $trace = null
    ) {}

    /**
     * Parse trace data into Frame objects.
     *
     * @return array<int, Frame>
     */
    public function parse(): array
    {
        try {
            if (blank($this->trace)) {
                return [];
            }

            // Handle JSON string
            if (is_string($this->trace)) {
                $decoded = json_decode($this->trace, true);

                if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
                    return $this->parseFrames($decoded);
                }

                return [];
            }

            // Handle array directly
            if (is_array($this->trace)) {
                return $this->parseFrames($this->trace);
            }

            return [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Parse array format from spatie/backtrace.
     *
     * @param  array<int, array<string, mixed>>  $frames
     * @return array<int, Frame>
     */
    protected function parseFrames(array $frames): array
    {
        return collect($frames)
            ->map(fn (array $frameData): Frame => new Frame($frameData))
            ->filter(fn (Frame $frame): bool => filled($frame->file()) && $frame->line() > 0)
            ->values()
            ->toArray();
    }
}
