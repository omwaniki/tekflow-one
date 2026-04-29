<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = [
        'name',
        'status',
        'created_by'
    ];

    /*
    |------------------------------------------------------------------
    | Relationships
    |------------------------------------------------------------------
    */

    public function records()
    {
        return $this->hasMany(AuditRecord::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

        // 🔥 Admin & Global → see all audits
        if ($user->hasRole(['admin', 'global'])) {
            return $query;
        }

        // 🔥 Others → only audits they created
        return $query->where('created_by', $user->id);
    }
}