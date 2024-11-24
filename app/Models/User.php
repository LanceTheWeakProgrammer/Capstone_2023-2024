<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'account_number',
        'username',
        'password',
        'is_active',
        'role',
        'status',
        'verification_code',
        'logged_in_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'logged_in_at' => 'datetime'
    ];

    public function technicianProfile()
    {
        return $this->hasOne(Technician::class, 'user_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function allMessages()
    {
        return $this->sentMessages->merge($this->receivedMessages)->sortByDesc('created_at');
    }

    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id')->whereNull('read_at');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}