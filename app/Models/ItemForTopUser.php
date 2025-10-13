<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ItemForTopUser extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'item_for_top_users';
}
