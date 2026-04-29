<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditRecord extends Model
{
    protected $fillable = [
        'audit_id',
        'asset_id',
        'campus_id',
        'agent_id',
        'expected_status',
        'found',
        'suggested_status',
        'notes',
        'verified_at'
    ];

    /*
    |------------------------------------------------------------------
    | Relationships
    |------------------------------------------------------------------
    */

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    /*
    |------------------------------------------------------------------
    | 🔥 CENTRALIZED ACCESS CONTROL
    |------------------------------------------------------------------
    */

    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // 🔥 CENTRAL SOURCE
        $regionId = $user->getRegionId();
        $campusId = $user->getCampusId();

        // ✅ Admin & Global → full access
        if ($user->hasRole(['admin', 'global'])) {
            return $query;
        }

        // ✅ Regional & Manager → region-wide
        if ($user->hasRole(['regional', 'manager'])) {

            if (!$regionId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereHas('campus', function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            });
        }

        // ✅ Campus / Agent → only their campus
        if ($user->hasRole(['campus', 'agent'])) {

            if (!$campusId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('campus_id', $campusId);
        }

        // 🔒 Fallback
        return $query->whereRaw('1 = 0');
    }
}