<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class SuggestProduct extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'suggest_products';
}
