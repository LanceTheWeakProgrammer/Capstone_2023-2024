<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service';
    protected $primaryKey = 'id';

    protected $fillable = ['name', 'icon', 'fee', 'description'];

    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'service_offered', 'service_id', 'technician_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'services_selected')->withPivot('service_fee');
    }
}
