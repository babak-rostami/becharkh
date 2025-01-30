<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryCommentLike extends Model
{
    use HasFactory;

    public function categoryComment()
    {
        return $this->belongsTo(CategoryComment::class, 'category_comment_id');
    }
}
