<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamInfo extends Model
{
    use HasFactory;

    protected $table = 'team_info';
    protected $primaryKey = 'id';
    protected $fillable = [
        'memberName',
        'memberRole',
        'memberImg',
    ];
}
