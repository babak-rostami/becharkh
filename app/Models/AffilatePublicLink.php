<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class AffilatePublicLink extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'affilate_plink';
}
