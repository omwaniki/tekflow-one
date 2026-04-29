<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'campus_id',
        'region_id',
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 CENTRALIZED ACCESS CONTROL
    |--------------------------------------------------------------------------
    */

    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // ✅ FIXED: use new helper methods
        $regionId = $user->regionId();
        $campusId = $user->campusId();

        // ✅ Admin & Global → full access
        if ($user->isAdmin()) {
            return $query;
        }

        // ✅ Regional & Manager → region-wide
        if ($user->isRegional()) {

            if (!$regionId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where(function ($q) use ($regionId) {

                $q->whereHas('campus', function ($sub) use ($regionId) {
                    $sub->where('region_id', $regionId);
                })

                ->orWhere('region_id', $regionId);

            });
        }

        // ✅ Campus / Agent → only their campus
        if ($user->isCampus()) {

            if (!$campusId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('campus_id', $campusId);
        }

        return $query->whereRaw('1 = 0');
    }
}