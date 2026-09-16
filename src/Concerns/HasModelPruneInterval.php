<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

use Carbon\CarbonInterface;

trait HasModelPruneInterval
{
    protected ?CarbonInterface $modelPruneInterval = null;

    public function modelPruneInterval(CarbonInterface $interval): static
    {
        $this->modelPruneInterval = $interval;

        return $this;
    }

    public function getModelPruneInterval(): CarbonInterface
    {
        return $this->modelPruneInterval ?? now()->subWeek();
    }
}
