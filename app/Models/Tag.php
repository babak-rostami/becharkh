<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'tag_pages', 'tag_id', 'page_id')->where('page_class', 'question')->orderBy('id', 'desc');
    }

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'tag_pages', 'tag_id', 'page_id')->where('page_class', 'blog')->where('status',1)->orderBy('id', 'desc');
    }

    public function races()
    {
        return $this->belongsToMany(Race::class, 'tag_pages', 'tag_id', 'page_id')->where('page_class', 'race')->where('status',1)->orderBy('id', 'desc');
    }

    public function pageCount()
    {
        return $this->questions()->count() + $this->blogs()->count();
    }

}
