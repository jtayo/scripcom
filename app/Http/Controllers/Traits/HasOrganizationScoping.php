<?php

namespace App\Http\Controllers\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasOrganizationScoping
{
    protected function organizationId(): ?int
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return null;
        }

        return $user->organization_id;
    }

    protected function organization(): ?Organization
    {
        $id = $this->organizationId();

        return $id ? Organization::find($id) : null;
    }

    protected function scopeOrganization(Builder $query, string $column = 'organization_id'): Builder
    {
        $id = $this->organizationId();

        if ($id) {
            $query->where($column, $id);
        }

        return $query;
    }
}
