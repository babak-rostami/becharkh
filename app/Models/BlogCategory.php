<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;


    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id')->orderBy('id','desc')->where('status', 1)->paginate(15);
    }
}
