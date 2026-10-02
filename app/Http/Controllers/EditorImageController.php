<?php

namespace App\Http\Controllers;

use App\Models\AffilateEditorImage;
use App\Models\BlogEditorImage;
use App\Models\CategoryCommentEditorImage;
use App\Models\ProductCommentEditorImage;
use App\Models\ProductEditorImage;
use App\Models\QuestionAnswerEditorImage;
use App\Models\QuestionEditorImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class EditorImageController extends Controller
{

    public function upload(Request $request, $page)
    {
        set_time_limit(360);
        if ($request->hasFile('upload')) {
            $disk = Storage::disk('ftp');
            $file = $request->file('upload');
            $image_name = strtolower(Str::random(12));
            $filename =  time() . '-' . $image_name . '.webp';
            $resizedImage = Image::make($file)->encode('webp', 90);

            if (
                $page == 'comment' || $page == 'admin_edit_comment' || $page == 'admin_create_comment'
                || $page == 'show_question' || $page == 'admin_edit_qanswer' || $page == 'admin_qanswers'
            ) {
                $image = new CategoryCommentEditorImage();
                $path = 'comeditor/images/1/';
            } else if ($page == 'edit_question_admin' || $page == 'create_question_admin' || $page == 'edit_question' || $page == 'create_question') {
                $image = new QuestionEditorImage();
                $path = 'question/images/1/';
            } else if ($page == 'create_blog' || $page == 'edit_blog') {
                $image = new BlogEditorImage();
                $path = 'blog/images/1/';
            } else if ($page == 'create_affilate' || $page == 'edit_affilate') {
                $image = new AffilateEditorImage();
                $path = 'product/images/1/';
            } else if ($page == 'show_product' || $page == 'admin_create_product_comment' || $page == 'admin_edit_product_comment') {
                $image = new ProductCommentEditorImage();
                $path = 'productc/images/1/';
            }

            $disk->put($path . $filename, (string) $resizedImage);
            $url = $path . $filename;
            $image->path = $url;
            $image->comment_id = null;
            $image->save();

            return response()->json(['filename' => $filename, 'uploaded' => 1, 'url' => "https://dl.becharkh.com/user_files/" . $url]);
        }
    }
}
