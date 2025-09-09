<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Trace;

use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Phiki\Transformers\Decorations\LineDecoration;

class CodeBlock
{
    public function __construct(protected mixed $startLine = 1, protected mixed $line = '', protected mixed $prefix = '', protected mixed $suffix = '')
    {
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

    public function codeString(): string
    {
        return once(fn (): string => $this->prefix . $this->line . $this->suffix);
    }

    public function output($focusLine, Theme $theme = Theme::GithubLight): string
    {
        return (new Phiki)
            ->codeToHtml(
                code: $this->codeString(),
                grammar: Grammar::Php,
                theme: $theme,
            )
            ->withGutter()
            ->startingLine($this->getStartLine())
            ->decoration(
                LineDecoration::forLine($focusLine - $this->getStartLine())
                    ->class('bg-primary-400/20', 'dark:bg-primary/20'),
            )
            ->toString();

    }
}
