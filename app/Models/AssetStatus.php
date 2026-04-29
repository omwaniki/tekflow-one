<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_active'
    ];

    /**
     * Get all assets using this status
     */
    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class, 'status', 'name');
    }
}