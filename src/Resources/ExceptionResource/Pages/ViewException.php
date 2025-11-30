<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use BezhanSalleh\FilamentExceptions\Trace\Frame;
use BezhanSalleh\FilamentExceptions\Trace\Parser;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Phiki\Theme\Theme;
use Throwable;

class ViewException extends ViewRecord
{
    protected static string $resource = ExceptionResource::class;

    protected string $view = 'filament-exceptions::view-exception';

    /** @var array<int, Frame>|null */
    protected ?array $cachedFrames = null;

    /**
     * @return array<int, Frame>
     */
    public function getFramesProperty(): array
    {
        if ($this->cachedFrames !== null) {
            return $this->cachedFrames;
        }

        try {
            $this->cachedFrames = (new Parser($this->record->trace ?? ''))->parse();
        } catch (Throwable) {
            $this->cachedFrames = [];
        }

        return $this->cachedFrames;
    }

    public function renderFrame(int $frameIndex, bool $isDark = false): string
    {
        try {
            $frames = $this->frames;

            if (! isset($frames[$frameIndex])) {
                return '<div class="text-gray-500 p-4">Frame not available</div>';
            }

            $frame = $frames[$frameIndex];
            $codeBlock = $frame->getCodeBlock();

            if (blank($codeBlock->codeString())) {
                return '<div class="text-gray-500 p-4">Source code not available</div>';
            }

            $theme = $isDark ? Theme::GithubDark : Theme::GithubLight;

            return $codeBlock->output($frame->line(), $theme);
        } catch (Throwable) {
            return '<div class="text-gray-500 p-4">Unable to render code</div>';
        }
    }

    /**
     * @return array<string>
     */
    public function getPageClasses(): array
    {
        return [
            'fi-resource-view-record-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug(Filament::getCurrentOrDefaultPanel())),
            'fi-resource-record-' . $this->getRecord()->getKey(),
        ];
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
