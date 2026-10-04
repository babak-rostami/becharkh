<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class AffilateEditorImage extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'affilate_editor_images';
}
