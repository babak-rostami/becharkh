<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class SuggestProduct extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'suggest_products';
}
