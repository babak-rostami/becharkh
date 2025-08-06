<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class InputImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'input_images';
}
