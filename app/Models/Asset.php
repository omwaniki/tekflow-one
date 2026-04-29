<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\AssetAssignment;
use App\Models\AssetMovement;

class Asset extends Model
{
    protected $fillable = [
        'type',
        'name',
        'assigned_to_name',
        'assigned_to_email',
        'role',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'status',
        'manufacture_date',
        'campus_id',
        'agent_id',
    ];

    protected $casts = [
        'manufacture_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function movements()
    {
        return $this->hasMany(AssetMovement::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAgeAttribute()
    {
        if (!$this->manufacture_date) {
            return 0;
        }

        if ($this->manufacture_date->isFuture()) {
            return 0;
        }

        return $this->manufacture_date->diffInYears(now());
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (🔥 ACCESS CONTROL - STANDARDIZED)
    |--------------------------------------------------------------------------
    */

    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // 🔥 CENTRALIZED SOURCE OF TRUTH
        $regionId = $user->getRegionId();
        $campusId = $user->getCampusId();

        // 🔥 Admin & Global → full access
        if ($user->hasRole(['admin', 'global'])) {
            return $query;
        }

        // 🔥 Regional & Manager → ALL campuses in region
        if ($user->hasRole(['regional', 'manager'])) {

            if (!$regionId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereHas('campus', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        // 🔥 Campus → only their campus
        if ($user->hasRole('campus')) {

            if (!$campusId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('campus_id', $campusId);
        }

        // 🔥 Agent → same as campus
        if ($user->hasRole('agent')) {

            if (!$campusId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('campus_id', $campusId);
        }

        // 🔒 Fallback
        return $query->whereRaw('1 = 0');
    }
}