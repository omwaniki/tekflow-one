<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMovement extends Model
{
    protected $fillable = [
        'asset_id',
        'from_campus_id',
        'to_campus_id',
        'movement_type',
        'reason',
        'movement_date',
        'performed_by',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromCampus()
    {
        return $this->belongsTo(Campus::class, 'from_campus_id');
    }

    public function toCampus()
    {
        return $this->belongsTo(Campus::class, 'to_campus_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}