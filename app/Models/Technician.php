<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $table = 'technician_profiles';

    protected $fillable = [
        'user_id',
        'full_name',
        'date_of_birth',
        'phone_number',
        'year_experience',
        'profile_image',
        'bio',
        'avail_status',
        'is_removed',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function vehicleTypes()
    {
        return $this->belongsToMany(VehicleType::class, 'vehicle_mastery', 'technician_id', 'vehicle_type_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_offered', 'technician_id', 'service_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'technician_id');
    }
}
