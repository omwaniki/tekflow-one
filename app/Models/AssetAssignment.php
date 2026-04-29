<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id',
        'assigned_to_name',
        'assigned_to_email',
        'assigned_to_type',
        'user_id',
        'campus_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (🔥 IMPORTANT - matches your access model)
    |--------------------------------------------------------------------------
    */

    public function scopeVisibleTo($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Admin / Global → see all
        if ($user->hasRole(['admin', 'global'])) {
            return $query;
        }

        // Regional → all campuses in region
        if ($user->hasRole(['regional', 'manager'])) {
            return $query->whereHas('campus', function ($q) use ($user) {
                $q->where('region_id', $user->region_id);
            });
        }

        // Campus / Agent → only their campus
        return $query->where('campus_id', $user->campus_id);
    }
}