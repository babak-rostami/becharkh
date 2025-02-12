<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class UserSearch extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_searches';
}
