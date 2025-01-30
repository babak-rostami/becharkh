<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCommentLike extends Model
{
    use HasFactory;

    public function blogComment()
    {
        return $this->belongsTo(BlogComment::class, 'blog_comment_id');
    }
}
