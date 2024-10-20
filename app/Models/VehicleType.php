<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $table = 'vehicle_types';

    protected $fillable = ['type'];

    public function vehicleDetails()
    {
        return $this->hasMany(VehicleDetail::class);
    }

    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'vehicle_mastery', 'vehicle_type_id', 'technician_id');
    }
}
