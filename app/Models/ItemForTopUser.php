<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class ItemForTopUser extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'item_for_top_users';
}
