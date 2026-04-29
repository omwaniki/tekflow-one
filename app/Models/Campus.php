<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\HasScope;

class Campus extends Model
{
    use HasFactory, HasScope;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'region_id'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 CENTRALIZED ACCESS CONTROL (CLEAN VERSION)
    |--------------------------------------------------------------------------
    */

    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // ✅ FIXED: use new helpers
        $regionId = $user->regionId();
        $campusId = $user->campusId();

        // ✅ Admin & Global → full access
        if ($user->isAdmin()) {
            return $query;
        }

        // ✅ Regional & Manager → all campuses in region
        if ($user->isRegional()) {

            if (!$regionId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('region_id', $regionId);
        }

        // ✅ Campus / Agent → only their campus
        if ($user->isCampus()) {

            if (!$campusId) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('id', $campusId);
        }

        // 🔒 Fallback
        return $query->whereRaw('1 = 0');
    }
}