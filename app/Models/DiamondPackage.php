<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiamondPackage extends Model
{
    use HasFactory;

    public function showPrice()
    {
        $pr = $this->price;
        $characters = str_split($pr);
        // if($characters.length)
        if (count($characters) == 5) {
            if ($characters[2] == 0 && $characters[3] == 0 && $characters[4] == 0) {
                $p = $characters[0] . $characters[1];
                return $p . " تومان ";
            } else {
                return $pr;
            }
        } elseif (count($characters) == 6) {
            if ($characters[3] == 0 && $characters[4] == 0 && $characters[5] == 0) {
                $p = $characters[0] . $characters[1] . $characters[2];
                return $p . " تومان ";
            } else {
                return $pr;
            }
        }
    }
}
