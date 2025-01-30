<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class AffilateEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'affilate_editor_images';
}
