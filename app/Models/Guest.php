<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//Shelved
class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_token',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
