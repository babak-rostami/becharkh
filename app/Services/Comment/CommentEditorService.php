<?php

namespace App\Services\Comment;

use App\Models\AffilateEditorImage;
use App\Models\BlogEditorImage;
use App\Models\CategoryCommentEditorImage;
use App\Models\ProductCommentEditorImage;
use App\Models\QuestionEditorImage;
use DOMDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommentEditorService
{
    public function store($page, $editor, $commentObject)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $editor);
        $paragraphs = $dom->getElementsByTagName('p');

        $text = '';
        foreach ($paragraphs as $paragraph) {
            $text .= $paragraph->nodeValue . "\n";
        }
        if ($page == 'create_question_admin' || $page == 'create_question') {
            $body = Str::limit($text, 200, '...');
        } else {
            $body = Str::limit($text, 100, '...');
        }

        if ($page == 'create_affilate') {
            $commentObject->body = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else if ($page == 'create_affilate_body_2') {
            $commentObject->body2 = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else if ($page == 'create_blog') {
            $commentObject->content = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else if ($page == 'admin_create_product_comment' || $page == 'show_product') {
            $commentObject->editor = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else {
            $commentObject->body = $body;
            $commentObject->editor = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        }

        //for store editor img
        $imageTags = $dom->getElementsByTagName('img');
        $imagePaths = [];
        foreach ($imageTags as $imageTag) {
            $image_src = $imageTag->getAttribute('src');
            if (isset(explode('https://dl.becharkh.com/user_files/', $image_src)[1])) {
                $image_full_path = explode('https://dl.becharkh.com/user_files/', $image_src)[1];
                $imagePaths[] = $image_full_path;
            }
        }
        $images = collect();
        if (
            $page == 'comment' || $page == 'admin_edit_comment' || $page == 'admin_create_comment'
            || $page == 'show_question' || $page == 'admin_edit_qanswer' || $page == "admin_qanswers"
        ) {
            $images = CategoryCommentEditorImage::where('comment_id', null)->whereIn('path', $imagePaths)->get();
        } elseif ($page == 'create_question_admin' || $page == 'create_question') {
            $images = QuestionEditorImage::where('comment_id', null)->whereIn('path', $imagePaths)->get();
        } elseif ($page == 'create_blog') {
            $images = BlogEditorImage::where('comment_id', null)->whereIn('path', $imagePaths)->get();
        } elseif ($page == 'create_affilate' || $page == 'create_affilate_body_2') {
            $images = AffilateEditorImage::where('comment_id', null)->whereIn('path', $imagePaths)->get();
        } elseif ($page == 'admin_create_product_comment' || $page == 'show_product') {
            $images = ProductCommentEditorImage::where('comment_id', null)->whereIn('path', $imagePaths)->get();
        }
        return $images;
    }

    public function updateImageCommentId($images, $comment_id)
    {
        if (!$images->isEmpty()) {
            foreach ($images as $image) {
                $image->comment_id = $comment_id;
                $image->update();
            }
        }
    }

    public function update($page, $editor, $commentObject)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $editor);
        $paragraphs = $dom->getElementsByTagName('p');

        $text = '';
        foreach ($paragraphs as $paragraph) {
            $text .= $paragraph->nodeValue . "\n";
        }
        if ($page == 'edit_question_admin' || $page == 'edit_question') {
            $body = Str::limit($text, 200, '...');
        } else {
            $body = Str::limit($text, 100, '...');
        }

        if ($page == 'edit_affilate') {
            $commentObject->body = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else if ($page == 'edit_affilate_body_2') {
            $commentObject->body2 = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else if ($page == 'edit_blog') {
            $commentObject->content = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else if ($page == 'admin_edit_product_comment') {
            $commentObject->editor = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        } else {
            $commentObject->body = $body;
            $commentObject->editor = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $editor);
        }

        $newCommentImages = collect();
        $imageTags = $dom->getElementsByTagName('img');
        foreach ($imageTags as $imageTag) {
            $image_src = $imageTag->getAttribute('src');
            if (isset(explode('https://dl.becharkh.com/user_files/', $image_src)[1])) {
                $image_full_path = explode('https://dl.becharkh.com/user_files/', $image_src)[1];
                if (
                    $page == 'comment' || $page == 'admin_edit_comment'
                    || $page == 'show_question' || $page == 'admin_edit_qanswer' || $page == 'admin_qanswers'
                ) {
                    $image = CategoryCommentEditorImage::where('comment_id', null)->where('path', $image_full_path)->first();
                    if ($image) {
                        $image->comment_id = $commentObject->id;
                        $image->update();
                        $newCommentImages->add($image);
                    }
                    $oldImage = CategoryCommentEditorImage::where('comment_id', $commentObject->id)->where('path', $image_full_path)->first();
                    if ($oldImage) {
                        $newCommentImages->add($oldImage);
                    }
                } elseif ($page == 'edit_question_admin' || $page == 'edit_question') {
                    $image = QuestionEditorImage::where('comment_id', null)->where('path', $image_full_path)->first();
                    if ($image) {
                        $image->comment_id = $commentObject->id;
                        $image->update();
                        $newCommentImages->add($image);
                    }
                    $oldImage = QuestionEditorImage::where('comment_id', $commentObject->id)->where('path', $image_full_path)->first();
                    if ($oldImage) {
                        $newCommentImages->add($oldImage);
                    }
                } elseif ($page == 'edit_blog') {
                    $image = BlogEditorImage::where('comment_id', null)->where('path', $image_full_path)->first();
                    if ($image) {
                        $image->comment_id = $commentObject->id;
                        $image->update();
                        $newCommentImages->add($image);
                    }
                    $oldImage = BlogEditorImage::where('comment_id', $commentObject->id)->where('path', $image_full_path)->first();
                    if ($oldImage) {
                        $newCommentImages->add($oldImage);
                    }
                } elseif ($page == 'edit_affilate' || $page == 'edit_affilate_body_2') {
                    $image = AffilateEditorImage::where('comment_id', null)->where('path', $image_full_path)->first();
                    if ($image) {
                        $image->comment_id = $commentObject->id;
                        $image->update();
                        $newCommentImages->add($image);
                    }
                    $oldImage = AffilateEditorImage::where('comment_id', $commentObject->id)->where('path', $image_full_path)->first();
                    if ($oldImage) {
                        $newCommentImages->add($oldImage);
                    }
                } elseif ($page == 'admin_edit_product_comment') {
                    $image = ProductCommentEditorImage::where('comment_id', null)->where('path', $image_full_path)->first();
                    if ($image) {
                        $image->comment_id = $commentObject->id;
                        $image->update();
                        $newCommentImages->add($image);
                    }
                    $oldImage = ProductCommentEditorImage::where('comment_id', $commentObject->id)->where('path', $image_full_path)->first();
                    if ($oldImage) {
                        $newCommentImages->add($oldImage);
                    }
                }
            }
        }
        if (
            $page == 'comment' || $page == 'admin_edit_comment'
            || $page == 'show_question' || $page == 'admin_edit_qanswer' || $page == 'admin_qanswers'
        ) {
            $deletedImages = CategoryCommentEditorImage::where('comment_id', $commentObject->id)
                ->whereNotIn('_id', $newCommentImages->pluck('id'))->get();
            foreach ($deletedImages as  $dimage) {
                $dimage->delete();
            }
        } elseif ($page == 'edit_question_admin' || $page == 'edit_question') {
            $deletedImages = QuestionEditorImage::where('comment_id', $commentObject->id)
                ->whereNotIn('_id', $newCommentImages->pluck('id'))->get();
            foreach ($deletedImages as  $dimage) {
                $dimage->delete();
            }
        } elseif ($page == 'edit_blog') {
            $deletedImages = BlogEditorImage::where('comment_id', $commentObject->id)
                ->whereNotIn('_id', $newCommentImages->pluck('id'))->get();
            foreach ($deletedImages as  $dimage) {
                $dimage->delete();
            }
        } elseif ($page == 'admin_edit_product_comment') {
            $deletedImages = ProductCommentEditorImage::where('comment_id', $commentObject->id)
                ->whereNotIn('_id', $newCommentImages->pluck('id'))->get();
            if (!$deletedImages->isEmpty()) {
                $disk = Storage::disk('ftp');
                foreach ($deletedImages as $dimage) {
                    $disk->delete($dimage->path);
                    $dimage->delete();
                }
            }
        } elseif ($page == 'edit_affilate' || $page == 'edit_affilate_body_2') {
            return $newCommentImages;
        }
    }

    public function updateImageAffilate($affilate_images, $affilate)
    {
        $deletedImages = AffilateEditorImage::where('comment_id', $affilate->id)
            ->whereNotIn('_id', $affilate_images->pluck('id'))->get();
        foreach ($deletedImages as $dimage) {
            $dimage->delete();
        }
    }

    public function changeTempEditorLazyImg($comment)
    {
        // Check if the comment has an editor property
        if (isset($comment->editor)) {
            // Use a regular expression to find the image tags with data-src
            $pattern = '/<img\s+([^>]*?)data-src=["\']([^"\']+)["\']([^>]*)>/i';

            // Replacement pattern to change data-src to src
            $replacement = '<img $1src="$2"$3>';

            // Update the editor content
            $comment->editor = preg_replace($pattern, $replacement, $comment->editor);

            // Optionally, remove lazy-load class from any img tags if necessary
            $comment->editor = preg_replace('/<img\s+([^>]*?)class=["\']([^"\']*?)lazy-load([^"\']*?)["\']([^>]*)>/i', '<img $1$3$4>', $comment->editor);
        }

        return $comment;
    }

    public function changeTempEditorLazyImgBlog($blog)
    {
        // Check if the comment has an editor property
        if (isset($blog->content)) {
            // Use a regular expression to find the image tags with data-src
            $pattern = '/<img\s+([^>]*?)data-src=["\']([^"\']+)["\']([^>]*)>/i';

            // Replacement pattern to change data-src to src
            $replacement = '<img $1src="$2"$3>';

            // Update the editor content
            $blog->content = preg_replace($pattern, $replacement, $blog->content);

            // Optionally, remove lazy-load class from any img tags if necessary
            $blog->content = preg_replace('/<img\s+([^>]*?)class=["\']([^"\']*?)lazy-load([^"\']*?)["\']([^>]*)>/i', '<img $1$3$4>', $blog->content);
        }

        return $blog;
    }

    public function changeTempEditorLazyImgAffilate($affilate)
    {
        $pattern = '/<img\s+([^>]*?)data-src=["\']([^"\']+)["\']([^>]*)>/i';
        $replacement = '<img $1src="$2"$3>';
        if (isset($affilate->body)) {
            $affilate->body = preg_replace($pattern, $replacement, $affilate->body);
            $affilate->body = preg_replace('/<img\s+([^>]*?)class=["\']([^"\']*?)lazy-load([^"\']*?)["\']([^>]*)>/i', '<img $1$3$4>', $affilate->body);
        }
        if (isset($affilate->body2)) {
            $affilate->body2 = preg_replace($pattern, $replacement, $affilate->body2);
            $affilate->body2 = preg_replace('/<img\s+([^>]*?)class=["\']([^"\']*?)lazy-load([^"\']*?)["\']([^>]*)>/i', '<img $1$3$4>', $affilate->body2);
        }
        return $affilate;
    }
}
