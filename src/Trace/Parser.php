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
    #[\ReturnTypeWillChange]
    public function current()
    {
        // TODO: Implement current() method.
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        // TODO: Implement next() method.
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        // TODO: Implement key() method.
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        // TODO: Implement valid() method.
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        // TODO: Implement rewind() method.
    }
}
