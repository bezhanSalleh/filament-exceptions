<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

use Closure;

trait HasTenantScope
{
    protected bool | Closure $isScopedToTenant = true;

    protected string | Closure | null $tenantOwnershipRelationshipName = null;

    protected string | Closure | null $tenantRelationshipName = null;

    public function scopeToTenant(bool | Closure $condition = true): static
    {
        $this->isScopedToTenant = $condition;

        return $this;
    }

    public function tenantOwnershipRelationshipName(string | Closure | null $ownershipRelationshipName): static
    {
        $this->tenantOwnershipRelationshipName = $ownershipRelationshipName;

        return $this;
    }

    public function tenantRelationshipName(string | Closure | null $relationshipName): static
    {
        $this->tenantRelationshipName = $relationshipName;

        return $this;
    }

    public function isScopedToTenant(): bool
    {
        return $this->evaluate($this->isScopedToTenant);
    }

    public function getTenantRelationshipName(): ?string
    {
        return $this->evaluate($this->tenantRelationshipName);
    }

    public function getTenantOwnershipRelationshipName(): ?string
    {
        return $this->evaluate($this->tenantOwnershipRelationshipName);
    }
}
