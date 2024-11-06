<?php

namespace BezhanSalleh\FilamentExceptions\Trace;

use Iterator;
use ReturnTypeWillChange;

class Parser implements Iterator
{
    public function __construct(
        protected ?string $trace = ''
    ) {}

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
<<<<<<< HEAD
    #[ReturnTypeWillChange]
=======
    #[\ReturnTypeWillChange]
>>>>>>> main
    public function current()
    {
        // TODO: Implement current() method.
    }

    /**
     * {@inheritDoc}
     */
<<<<<<< HEAD
    #[ReturnTypeWillChange]
=======
    #[\ReturnTypeWillChange]
>>>>>>> main
    public function next()
    {
        // TODO: Implement next() method.
    }

    /**
     * {@inheritDoc}
     */
<<<<<<< HEAD
    #[ReturnTypeWillChange]
=======
    #[\ReturnTypeWillChange]
>>>>>>> main
    public function key()
    {
        // TODO: Implement key() method.
    }

    /**
     * {@inheritDoc}
     */
<<<<<<< HEAD
    #[ReturnTypeWillChange]
=======
    #[\ReturnTypeWillChange]
>>>>>>> main
    public function valid()
    {
        // TODO: Implement valid() method.
    }

    /**
     * {@inheritDoc}
     */
<<<<<<< HEAD
    #[ReturnTypeWillChange]
=======
    #[\ReturnTypeWillChange]
>>>>>>> main
    public function rewind()
    {
        // TODO: Implement rewind() method.
    }
}
