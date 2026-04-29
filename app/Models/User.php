<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'campus_id',
        'region_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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
        return $this->hasOne(Agent::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 CENTRALIZED ACCESS HELPERS (FINAL CLEAN VERSION)
    |--------------------------------------------------------------------------
    */

    public function regionId()
    {
        return optional($this->agent)->region_id ?? $this->region_id;
    }

    public function campusId()
    {
        return optional($this->agent)->campus_id ?? $this->campus_id;
    }

    public function isAdmin()
    {
        return $this->hasRole(['admin', 'global']);
    }

    public function isRegional()
    {
        return $this->hasRole(['regional', 'manager']);
    }

    public function isCampus()
    {
        return $this->hasRole(['campus', 'agent']);
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
}