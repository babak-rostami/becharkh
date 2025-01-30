<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaceOption extends Model
{
    use HasFactory;

    public function race()
    {
        return $this->belongsTo(Race::class, 'race_id');
    }

    public function votes()
    {
        return $this->hasMany(RaceOptionPercent::class, 'option_id');
    }

    public function percent()
    {
        $votecount = 0;
        foreach ($this->race->options as $op) {
            $votecount += $op->votes->count();
        }
        if ($votecount == 0) {
            return 0;
        } else {
            return intval(($this->votes->count() / $votecount) * (100));
        }
    }

}
