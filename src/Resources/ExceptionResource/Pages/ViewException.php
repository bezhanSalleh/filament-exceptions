<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use BezhanSalleh\FilamentExceptions\Trace\Parser;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Phiki\Theme\Theme;

class ViewException extends ViewRecord
{
    protected static string $resource = ExceptionResource::class;

    protected string $view = 'filament-exceptions::view-exception';

    protected ?array $cachedFrames = null;

    public function getFramesProperty(): ?array
    {
        if (blank($this->cachedFrames)) {
            $trace = "#0 {$this->record->file}({$this->record->line})\n";
            $frames = (new Parser($trace . $this->record->trace))->parse();
            array_pop($frames);

            $this->cachedFrames = $frames;
        }

        return $this->cachedFrames;
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function renderFrame(int $frameIndex, bool $isDark = false): string
    {
        $frames = $this->frames;

        if (! isset($frames[$frameIndex])) {
            return '<div class="text-red-500">Frame not found</div>';
        }

        $frame = $frames[$frameIndex];
        $theme = $isDark ? Theme::GithubDark : Theme::GithubLight;

        return $frame->getCodeBlock()->output($frame->line(), $theme);
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return [
            'fi-resource-view-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug(Filament::getCurrentOrDefaultPanel())),
            "fi-resource-record-{$this->getRecord()->getKey()}",
        ];
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }
}
