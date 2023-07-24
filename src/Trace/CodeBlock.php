<?php

namespace BezhanSalleh\FilamentExceptions\Trace;

class CodeBlock
{
    protected mixed $line = '';

    protected mixed $suffix = '';

    protected mixed $prefix = '';

    protected mixed $startLine = 1;

    public function __construct($startLine = 1, $line = '', $prefix = '', $suffix = '')
    {
        $this->startLine = $startLine;
        $this->line = $line;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public function getStartLine()
    {
        return $this->startLine;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function getSuffix()
    {
        return $this->suffix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function output(): string
    {
        return htmlentities($this->prefix . $this->line . $this->suffix);
    }
}
