<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

trait HasLabels
{
    protected bool $isGloballySearchable = true;

    protected ?string $modelLabel = null;

    protected ?string $pluralModelLabel = null;

    protected bool $hasTitleCaseModelLabel = true;

    public function modelLabel(string $label): static
    {
        $this->modelLabel = $label;

        return $this;
    }

    public function pluralModelLabel(string $label): static
    {
        $this->pluralModelLabel = $label;

        return $this;
    }

    public function titleCaseModelLabel(bool $condition = true): static
    {
        $this->hasTitleCaseModelLabel = $condition;

        return $this;
    }

    public function getModelLabel(): string
    {
        return $this->modelLabel ?? __('filament-exceptions::filament-exceptions.labels.model');
    }

    public function getPluralModelLabel(): string
    {
        return $this->pluralModelLabel ?? __('filament-exceptions::filament-exceptions.labels.model_plural');
    }

    public function hasTitleCaseModelLabel(): bool
    {
        return $this->hasTitleCaseModelLabel;
    }

    public function globallySearchable(bool $condition = true): static
    {
        $this->isGloballySearchable = $condition;

        return $this;
    }

    public function canGloballySearch(): bool
    {
        return $this->isGloballySearchable;
    }
}
