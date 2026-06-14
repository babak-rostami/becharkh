<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class InputImage extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'input_images';
}
