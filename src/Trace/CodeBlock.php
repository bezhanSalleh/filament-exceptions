<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Trace;

use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Phiki\Transformers\Decorations\LineDecoration;
use Throwable;

class CodeBlock
{
    public function __construct(
        protected int $startLine = 1,
        protected string $line = '',
        protected string $prefix = '',
        protected string $suffix = ''
    ) {}

    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function getLine(): string
    {
        return $this->line;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function codeString(): string
    {
        return $this->prefix . $this->line . $this->suffix;
    }

    public function output(int $focusLine, Theme $theme = Theme::GithubLight): string
    {
        try {
            $code = $this->codeString();

            if (blank($code)) {
                return '';
            }

            // LineDecoration expects 0-based index within the code block
            $lineIndex = $focusLine - $this->startLine;

            return (string) (new Phiki)
                ->codeToHtml(
                    code: $code,
                    grammar: Grammar::Php,
                    theme: $theme,
                )
                ->withGutter()
                ->startingLine($this->startLine)
                ->decoration(
                    LineDecoration::forLine($lineIndex)
                        ->class('bg-primary-400/20!', 'dark:bg-primary/20!', 'highlighted-line'),
                );
        } catch (Throwable) {
            // Fallback to plain text if Phiki fails
            return '<pre class="p-4 bg-gray-100 dark:bg-gray-800 rounded overflow-x-auto"><code>'
                . e($this->codeString())
                . '</code></pre>';
        }
    }
}
