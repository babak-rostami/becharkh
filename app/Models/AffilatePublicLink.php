<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
class AffilatePublicLink extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'affilate_plink';
}
