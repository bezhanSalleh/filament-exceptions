<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

trait HasTabs
{
    protected int $activeTab = 1;

    protected ?string $cookiesTabIcon = null;

    protected ?string $cookiesTabLabel = null;

    protected ?string $bodyTabIcon = null;

    protected ?string $bodyTabLabel = null;

    protected ?string $exceptionTabIcon = null;

    protected ?string $exceptionTabLabel = null;

    protected ?string $headersTabIcon = null;

    protected ?string $headersTabLabel = null;

    protected ?string $queriesTabIcon = null;

    protected ?string $queriesTabLabel = null;

    /**
     * Tabs: 1 = Exception, 2 = Headers, 3 = Cookies, 4 = Body, 5 = Queries
     */
    public function activeTab(int $tab): static
    {
        $this->activeTab = $tab;

        return $this;
    }

    public function bodyTabIcon(string $icon): static
    {
        $this->bodyTabIcon = $icon;

        return $this;
    }

    public function bodyTabLabel(string $label): static
    {
        $this->bodyTabLabel = $label;

        return $this;
    }

    public function cookiesTabIcon(string $icon): static
    {
        $this->cookiesTabIcon = $icon;

        return $this;
    }

    public function cookiesTabLabel(string $label): static
    {
        $this->cookiesTabLabel = $label;

        return $this;
    }

    public function exceptionTabIcon(string $icon): static
    {
        $this->exceptionTabIcon = $icon;

        return $this;
    }

    public function exceptionTabLabel(string $label): static
    {
        $this->exceptionTabLabel = $label;

        return $this;
    }

    public function headersTabIcon(string $icon): static
    {
        $this->headersTabIcon = $icon;

        return $this;
    }

    public function headersTabLabel(string $label): static
    {
        $this->headersTabLabel = $label;

        return $this;
    }

    public function queriesTabIcon(string $icon): static
    {
        $this->queriesTabIcon = $icon;

        return $this;
    }

    public function queriesTabLabel(string $label): static
    {
        $this->queriesTabLabel = $label;

        return $this;
    }

    public function getActiveTab(): int
    {
        return $this->activeTab;
    }

    public function getBodyTabIcon(): string
    {
        return $this->bodyTabIcon ?? 'heroicon-o-code-bracket';
    }

    public function getBodyTabLabel(): string
    {
        return $this->bodyTabLabel ?? __('filament-exceptions::filament-exceptions.labels.tabs.body');
    }

    public function getCookiesTabIcon(): string
    {
        return $this->cookiesTabIcon ?? 'heroicon-o-chart-pie';
    }

    public function getCookiesTabLabel(): string
    {
        return $this->cookiesTabLabel ?? __('filament-exceptions::filament-exceptions.labels.tabs.cookies');
    }

    public function getExceptionTabIcon(): string
    {
        return $this->exceptionTabIcon ?? 'heroicon-o-bug-ant';
    }

    public function getExceptionTabLabel(): string
    {
        return $this->exceptionTabLabel ?? __('filament-exceptions::filament-exceptions.labels.tabs.exception');
    }

    public function getHeadersTabIcon(): string
    {
        return $this->headersTabIcon ?? 'heroicon-o-arrows-right-left';
    }

    public function getHeadersTabLabel(): string
    {
        return $this->headersTabLabel ?? __('filament-exceptions::filament-exceptions.labels.tabs.headers');
    }

    public function getQueriesTabIcon(): string
    {
        return $this->queriesTabIcon ?? 'heroicon-o-circle-stack';
    }

    public function getQueriesTabLabel(): string
    {
        return $this->queriesTabLabel ?? __('filament-exceptions::filament-exceptions.labels.tabs.queries');
    }
}
