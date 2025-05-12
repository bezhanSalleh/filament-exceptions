<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

use Carbon\Carbon;

trait HasModelPruneInterval
{
    protected ?Carbon $modelPruneInterval = null;

    public function modelPruneInterval(Carbon $interval): static
    {
        $this->modelPruneInterval = $interval;

        return $this;
    }

    public function getModelPruneInterval(): Carbon
    {
        return $this->modelPruneInterval ?? now()->subWeek();
    }
}
