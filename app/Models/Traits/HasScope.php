<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasScope
{
    public function scopeVisibleTo(Builder $query, $user)
    {
        // Admin / Global → see everything
        if ($user->hasRole('admin') || $user->hasRole('global')) {
            return $query;
        }

        // Regional / Manager → filter by region
        if ($user->hasRole('regional') || $user->hasRole('manager')) {
            return $query->where('region_id', $user->region_id);
        }

        // Campus / Agent → filter by campus
        if ($user->hasRole('campus') || $user->hasRole('agent')) {
            return $query->where('campus_id', $user->campus_id);
        }

        return $query;
    }
}