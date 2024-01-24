<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Concerns;

trait HasTenantScope
{
    protected bool $isScopedToTenant = true;

    protected ?string $tenantOwnershipRelationshipName = null;

    protected ?string $tenantRelationshipName = null;

    public function scopeToTenant(bool $condition = true): static
    {
        $this->isScopedToTenant = $condition;

        return $this;
    }

    public function tenantOwnershipRelationshipName(string $ownershipRelationshipName): static
    {
        $this->tenantOwnershipRelationshipName = $ownershipRelationshipName;

        return $this;
    }

    public function tenantRelationshipName(string $relationshipName): static
    {
        $this->tenantRelationshipName = $relationshipName;

        return $this;
    }

    public function isScopedToTenant(): bool
    {
        return $this->isScopedToTenant;
    }

    public function getTenantRelationshipName(): ?string
    {
        return $this->tenantRelationshipName;
    }

    public function getTenantOwnershipRelationshipName(): ?string
    {
        return $this->tenantOwnershipRelationshipName;
    }
}
