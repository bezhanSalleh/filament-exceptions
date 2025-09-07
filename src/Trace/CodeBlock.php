<?php

namespace BezhanSalleh\FilamentExceptions\Trace;

use Phiki\Phiki;
use Phiki\Theme\Theme;
use Phiki\Grammar\Grammar;
use Phiki\Phast\ClassList;
use Phiki\Transformers\AddClassesTransformer;
use Phiki\Transformers\Decorations\LineDecoration;
use Phiki\Transformers\Decorations\GutterDecoration;
use BezhanSalleh\FilamentExceptions\Trace\AddLineClass;

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

    public function codeString(): string
    {
        return str($this->prefix)->append($this->line)->append($this->suffix)->toString();
    }

    public function getHighlightedCodeBlock($focusLine)
    {
        return (new Phiki)
        ->codeToHtml(
            code: $this->codeString(),
            grammar: Grammar::Php,
            theme: Theme::GithubLight
            )
            ->withGutter()
            ->startingLine($this->getStartLine() - 1)
            ->decoration(
                LineDecoration::forLine($focusLine - $this->getStartLine() + 1)
                    ->class('bg-primary-400/30', 'dark:bg-primary/50'),
            )
            ->toString();
    }
}
