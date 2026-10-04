<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class UserSearch extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'user_searches';
}
