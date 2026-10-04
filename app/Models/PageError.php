<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class PageError extends Model
{

    protected $connection = 'mongodb';
    protected $table = 'page_errors';
}
