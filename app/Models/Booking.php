<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'technician_id',
        'guest_id',
        'vehicle_detail_id',  
        'booking_date',
        'rescheduled_date',
        'status',
        'total_fee',
        'additional_info',
    ];

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'id');
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function vehicleDetail()
    {
        return $this->belongsTo(VehicleDetail::class, 'vehicle_detail_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'services_selected')->withPivot('service_fee');
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'materials_selected')->withPivot('quantity_used', 'total_material_cost');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
