<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;

    protected $table = 'carousel';
    protected $primaryKey = 'id';
    protected $fillable = ['carousel_image', 'active'];
}
