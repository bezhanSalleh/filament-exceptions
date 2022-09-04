<?php

namespace BezhanSalleh\FilamentExceptions\Trace;

use Iterator;

class Parser implements Iterator
{
    public function __construct(protected ?string $trace = '')
    {
    }

    public function parse(): ?array
    {
        $frames = explode("\n", $this->trace);

        return collect($frames)->map(function ($frame) {
            return new Frame($frame);
        })->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        // TODO: Implement current() method.
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        // TODO: Implement next() method.
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        // TODO: Implement key() method.
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        // TODO: Implement valid() method.
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        // TODO: Implement rewind() method.
    }
}
