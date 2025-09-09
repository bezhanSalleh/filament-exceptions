<?php

namespace BezhanSalleh\FilamentExceptions\Trace;

use Phiki\Phiki;
use Phiki\Theme\Theme;
use Phiki\Grammar\Grammar;
use Phiki\Phast\ClassList;
use Filament\Facades\Filament;
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

    protected ?string $cachedOutput = null;

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

    public function codeString(): string
    {
        return once(fn (): string => $this->prefix . $this->line . $this->suffix);
    }

    public function output($focusLine, Theme $theme = Theme::GithubLight): string
    {
        if (blank($this->cachedOutput)) {
            $this->cachedOutput = (new Phiki)
                    ->codeToHtml(
                        code: $this->codeString(),
                        grammar: Grammar::Php,
                        theme: $theme
                    )
                    ->withGutter()
                    ->startingLine($this->getStartLine())
                    ->decoration(
                        LineDecoration::forLine($focusLine - $this->getStartLine())
                            ->class('bg-primary-400/20', 'dark:bg-primary/20'),
                    )
                    ->toString();
        }

        return $this->cachedOutput;
    }
}
