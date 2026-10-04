<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Admin extends Model implements AuthenticatableContract
{
    use Notifiable, Authenticatable;
    protected $connection = 'mongodb';
    protected $table = 'admins';

    protected $fillable = ['email', 'password', 'name'];

    protected $hidden = ['password'];

    public function notifications()
    {
        return $this->hasMany(AdminNotification::class, 'admin_id')->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(AdminNotification::class, 'admin_id')->where('unread', 1)->orderBy('created_at', 'desc');
    }
}
