<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class AdminInfo extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'admin_info';
    protected $primaryKey = 'adminID';
    protected $fillable = ['adminUsername', 'adminPassword'];

    protected $hidden = [
        'adminPassword',
    ];
}
