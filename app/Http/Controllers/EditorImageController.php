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
use Intervention\Image\Facades\Image;

class EditorImageController extends Controller
{

    public function upload(Request $request, $page)
    {
        set_time_limit(360);
        if ($request->hasFile('upload')) {
            $disk = Storage::disk('ftp');
            $file = $request->file('upload');
            $image_name = strtolower(str_random(12));

            if ($page == 'comment' || $page == 'admin_edit_comment' || $page == 'admin_create_comment') {
                $image = new CategoryCommentEditorImage();
            } else if ($page == 'show_question' || $page == 'admin_edit_qanswer' || $page == 'admin_qanswers') {
                $image = new QuestionAnswerEditorImage();
            } else if ($page == 'edit_question_admin' || $page == 'create_question_admin' || $page == 'edit_question' || $page == 'create_question') {
                $image = new QuestionEditorImage();
            } else if ($page == 'create_blog' || $page == 'edit_blog') {
                $image = new BlogEditorImage();
            } else if ($page == 'create_affilate' || $page == 'edit_affilate') {
                $image = new AffilateEditorImage();
            } else if ($page == 'show_product' || $page == 'admin_create_product_comment' || $page == 'admin_edit_product_comment') {
                $image = new ProductCommentEditorImage();
            }

            $path = 'comeditor/images/1/';
            $filename =  time() . '-' . $image_name . '.webp';
            $resizedImage = Image::make($file)->encode('webp', 90);
            $disk->put($path . $filename, (string) $resizedImage);

            $url = $path . $filename;
            $image->path = $url;
            $image->comment_id = null;
            $image->save();

            return response()->json(['filename' => $filename, 'uploaded' => 1, 'url' => "https://dl.becharkh.com/user_files/" . $url]);
        }
    }
}
