<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Resources\ExceptionResource\Pages;

use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use BezhanSalleh\FilamentExceptions\StoredException;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;

class ViewException extends ViewRecord
{
    protected static string $resource = ExceptionResource::class;

    protected string $view = 'filament-exceptions::view-exception';

    protected ?StoredException $storedException = null;

    /**
     * Get the stored exception instance for rendering with Laravel's components.
     */
    public function getStoredException(): StoredException
    {
        if ($this->storedException !== null) {
            return $this->storedException;
        }

        try {
            $this->storedException = new StoredException($this->record);
        } catch (Throwable) {
            // If something goes wrong, create with a fresh record
            $this->storedException = new StoredException($this->record);
        }

        return $this->storedException;
    }

    public function getHeading(): string | Htmlable | null
    {
        return null; // $this->heading ?? $this->getTitle();
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
        return Width::SixExtraLarge;
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
