<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $fillable = [
        'booking_id',
        'image',      
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
