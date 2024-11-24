<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingJustification extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'requested_date',
        'justification',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
