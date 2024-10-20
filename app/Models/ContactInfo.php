<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    use HasFactory;

    protected $table = 'contact_info';
    protected $primaryKey = 'contactID';
    protected $fillable = ['address', 'gmap', 'tel1', 'tel2', 'email', 'twt', 'fb', 'ig', 'iframe'];

    public $timestamps = false;
}
