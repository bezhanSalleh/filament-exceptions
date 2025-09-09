<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Trace;

class Parser
{
    public function __construct(
        protected ?string $trace = ''
    ) {}

    public function parse(): ?array
    {
        $lines = explode("\n", (string) $this->trace);

        if (blank($lines)) {
            return null;
        }

        return collect($lines)
            ->filter(fn ($line): bool => filled(trim((string) $line)))
            ->map(fn ($line): \BezhanSalleh\FilamentExceptions\Trace\Frame => new Frame($line))
            ->toArray();
    }
}
