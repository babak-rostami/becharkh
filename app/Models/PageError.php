<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class PageError extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'page_errors';
}
