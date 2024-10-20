<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDetail extends Model
{
    protected $table = 'vehicle_details';

    protected $fillable = ['make', 'model', 'vehicle_type_id'];

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'vehicle_detail_id');
    }
}
