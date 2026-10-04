<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AdminNotification extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'admin_notifications';

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
