<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

use Filament\Clusters\Cluster;
use Filament\Pages\SubNavigationPosition;

trait HasNavigation
{
    /** @var class-string<Cluster> | null */
    protected ?string $cluster = null;

    protected bool $shouldEnableNavigationBadge = false;

    protected string | array | null $navigationBadgeColor = null;

    protected ?string $navigationGroup = null;

    protected ?string $navigationParentItem = null;

    protected ?string $navigationIcon = null;

    protected ?string $activeNavigationIcon = null;

    protected ?string $navigationLabel = null;

    protected ?int $navigationSort = null;

    protected ?string $slug = null;

    protected bool $shouldRegisterNavigation = true;

    protected SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return $this->subNavigationPosition;
    }

    public function navigationBadge(bool $condition = true): static
    {
        $this->shouldEnableNavigationBadge = $condition;

        return $this;
    }

    public function shouldEnableNavigationBadge(): bool
    {
        return $this->shouldEnableNavigationBadge;
    }

    public function navigationBadgeColor(string | array $color): static
    {
        $this->navigationBadgeColor = $color;

        return $this;
    }

    public function getNavigationBadgeColor(): string | array | null
    {
        return $this->navigationBadgeColor;
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? __('filament-exceptions::filament-exceptions.labels.navigation_group');
    }

    public function getNavigationParentItem(): ?string
    {
        return $this->navigationParentItem;
    }

    public function navigationParentItem(?string $item): static
    {
        $this->navigationParentItem = $item;

        return $this;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon ?? 'heroicon-o-bug-ant';
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function activeNavigationIcon(?string $icon): static
    {
        $this->activeNavigationIcon = $icon;

        return $this;
    }

    public function getActiveNavigationIcon(): ?string
    {
        return $this->activeNavigationIcon ?? 'heroicon-o-bug-ant';
    }

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->navigationLabel ?? __('filament-exceptions::filament-exceptions.labels.navigation');
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    public function shouldRegisterNavigation(): bool
    {
        return $this->shouldRegisterNavigation;
    }

    public function slug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function cluster(string $cluster): static
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * @return class-string<Cluster> | null
     */
    public function getCluster(): ?string
    {
        return $this->cluster;
    }
}
