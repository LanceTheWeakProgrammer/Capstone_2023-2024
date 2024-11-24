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
        'vehicle_detail_id',  
        'booking_date',
        'status',
        'reference_number',
        'total_fee',
        'additional_info',
    ];

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function technician()
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }

    // public function guest()
    // {
    //     return $this->belongsTo(Guest::class);
    // }

    public function vehicleDetail()
    {
        return $this->belongsTo(VehicleDetail::class, 'vehicle_detail_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'services_selected')->withPivot('service_fee');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function justifications()
    {
        return $this->hasMany(BookingJustification::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->reference_number = self::generateReferenceNumber();
        });
    }

    private static function generateReferenceNumber(): string
    {
        $date = now()->format('Ymd'); 
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); 
        return "BK-{$date}-{$random}";
    }
}
