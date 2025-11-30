<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

use Closure;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

trait HasNavigation
{
    /** @var class-string<Cluster> | Closure | null */
    protected string | Closure | null $cluster = null;

    protected bool | Closure $shouldEnableNavigationBadge = false;

    protected string | array | Closure | null $navigationBadgeColor = null;

    protected string | Closure | null $navigationGroup = null;

    protected string | Closure | null $navigationParentItem = null;

    protected string | Closure | null $navigationIcon = null;

    protected string | Closure | null $activeNavigationIcon = null;

    protected string | Closure | null $navigationLabel = null;

    protected int | Closure | null $navigationSort = null;

    protected string | Closure | null $slug = null;

    protected bool | Closure $shouldRegisterNavigation = true;

    protected SubNavigationPosition | Closure $subNavigationPosition = SubNavigationPosition::Start;

    // Setters
    public function cluster(string | Closure | null $cluster): static
    {
        $this->cluster = $cluster;

        return $this;
    }

    public function navigationBadge(bool | Closure $condition = true): static
    {
        $this->shouldEnableNavigationBadge = $condition;

        return $this;
    }

    public function navigationBadgeColor(string | array | Closure $color): static
    {
        $this->navigationBadgeColor = $color;

        return $this;
    }

    public function navigationGroup(string | Closure | null $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationParentItem(string | Closure | null $item): static
    {
        $this->navigationParentItem = $item;

        return $this;
    }

    public function navigationIcon(string | Closure | null $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function activeNavigationIcon(string | Closure | null $icon): static
    {
        $this->activeNavigationIcon = $icon;

        return $this;
    }

    public function navigationLabel(string | Closure | null $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function navigationSort(int | Closure | null $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function slug(string | Closure | null $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function registerNavigation(bool | Closure $shouldRegisterNavigation): static
    {
        $this->shouldRegisterNavigation = $shouldRegisterNavigation;

        return $this;
    }

    public function subNavigationPosition(SubNavigationPosition | Closure $subNavigationPosition): static
    {
        $this->subNavigationPosition = $subNavigationPosition;

        return $this;
    }

    // Getters
    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return $this->evaluate($this->subNavigationPosition);
    }

    public function shouldEnableNavigationBadge(): bool
    {
        return $this->evaluate($this->shouldEnableNavigationBadge);
    }

    public function getNavigationBadgeColor(): string | array | null
    {
        return $this->evaluate($this->navigationBadgeColor);
    }

    public function getNavigationGroup(): ?string
    {
        return $this->evaluate($this->navigationGroup ?? __('filament-exceptions::filament-exceptions.labels.navigation_group'));
    }

    public function getNavigationParentItem(): ?string
    {
        return $this->evaluate($this->navigationParentItem);
    }

    public function getNavigationIcon(): string
    {
        return $this->evaluate($this->navigationIcon ?? 'heroicon-o-bug-ant');
    }

    public function getActiveNavigationIcon(): ?string
    {
        return $this->evaluate($this->activeNavigationIcon ?? 'heroicon-s-bug-ant');
    }

    public function getNavigationLabel(): string
    {
        return (string) $this->evaluate($this->navigationLabel ?? __('filament-exceptions::filament-exceptions.labels.navigation'));
    }

    public function getNavigationSort(): ?int
    {
        return $this->evaluate($this->navigationSort);
    }

    public function shouldRegisterNavigation(): bool
    {
        return $this->evaluate($this->shouldRegisterNavigation);
    }

    public function getSlug(): ?string
    {
        return $this->evaluate($this->slug);
    }

    /**
     * @return class-string<Cluster> | null
     */
    public function getCluster(): ?string
    {
        return $this->evaluate($this->cluster);
    }
}
