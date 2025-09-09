<?php

namespace BezhanSalleh\FilamentExceptions\Trace;

use ReturnTypeWillChange;
use Iterator;

class Parser
{
    public function __construct(
        protected ?string $trace = ''
    ) {}

    public function parse(): ?array
    {
        $lines = explode("\n", $this->trace);

        if (blank($lines)) {
            return null;
        }

        return collect($lines)
            ->filter(fn($line) => filled(trim($line)))
            ->map(fn ($line) => new Frame($line))
            ->toArray();
    }
}
