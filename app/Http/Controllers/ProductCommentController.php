<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\Affilate;
use App\Models\MongoUser;
use App\Models\ProductComment;
use App\Models\ProductCommentEditorImage;
use App\Notifications\SiteEvent;
use App\Services\Comment\CommentEditorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProductCommentController extends Controller
{

    public function adminIndex($product_id)
    {
        $comments = ProductComment::where('product_id', $product_id)->orderBy('created_at', 'desc')->paginate(100);
        $comments->getCollection()->transform(function ($comment) {
            if (isset($comment->editor)) {
                preg_match('/<p>(.*?)<\/p>/', $comment->editor, $matches);
                $comment->body = isset($matches[1]) ? $matches[1] : null;
                return $comment;
            } else {
                return $comment;
            }
        });
        return view('affilate.comment.index', compact('comments', 'product_id'));
    }

    public function createAdmin(Request $request, $product_id)
    {
        return view('affilate.comment.create', compact('product_id'));
    }

    public function storeAdmin(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'نظر نمیتواند خالی باشد',
        ]);
        $user_id = app(UserController::class)->fakeRegisterSend($request->name, $request->username);
        $comment = new ProductComment();
        $comment->product_id = $request->product_id;
        $comment->user_id = $user_id;
        $product = Affilate::find($request->product_id);

        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;
            }
            $comment->body = $request->body;
        } else {
            $editor_service = new CommentEditorService();
            $editor_images = $editor_service->store('admin_create_product_comment', $request->body, $comment);

            $comment_count = $product->comment_count ?? 0;
            $comment_count += 1;
            $product->comment_count = $comment_count;
            $product->update();
        }
        $comment->save();

        if (!isset($request->parent_id)) {
            $editor_service->updateImageCommentId($editor_images, $comment->id);
        }

        $user = MongoUser::find($user_id);
        $this->SendEmailToParentComment($user, $product, $request);

        return redirect()->route('affilate.comments.admin', $request->product_id)->with('success', 'نظر با موفقیت ثبت شد');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'نظر خود را بنویسید',
        ]);
        if (!auth('user')->check()) {
            abort(403);
        }

        $comment = new ProductComment();

        if (!isset($request->parent_id)) {
            $editor_service = new CommentEditorService();
            $editor_images = $editor_service->store('product_comment', $request->body, $comment);
        } else {
            $comment->body = $request->body;
        }

        $user = auth('user')->user();
        $comment->user_id = $user->id;
        $product = Affilate::find($request->product_id);
        //if comment is not main comment 
        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            //if comment was reply to reply
            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;
            }
        } else {
            $comment_count = $product->comment_count ?? 0;
            $comment_count += 1;
            $product->comment_count = $comment_count;
            $product->update();
        }
        $comment->product_id = $product->id;
        $comment->save();

        if (!isset($request->parent_id)) {
            $editor_service->updateImageCommentId($editor_images, $comment->id);
        }

        $this->SendEmailToParentComment($user, $product, $request);

        $admin = Admin::first();
        $admin->notify(new SiteEvent([
            'action' => $user->username . ' نظری در محصول ' . $product->title . ' ارسال کرد',
            'route' => route('product.show', $product->slug),
        ]));

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

    public function editAdmin(Request $request, $comment_id)
    {
        $comment = ProductComment::find($comment_id);
        if (!isset($comment->parent_id)) {
            $editor_service = new CommentEditorService();
            $editor_service->changeTempEditorLazyImg($comment);
        }
        return view('affilate.comment.edit', compact('comment'));
    }

    public function updateAdmin(Request $request, $comment_id)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'نظر الزامی می باشد',
        ]);
        $comment = ProductComment::find($comment_id);

        if (!isset($comment->parent_id)) {
            $editor_service = new CommentEditorService();
            $editor_service->update('admin_edit_product_comment', $request->body, $comment);
        } else {
            $comment->body = $request->body;
        }

        $comment->update();

        return redirect()->route('affilate.comments.admin', $comment->product_id)->with('success', 'تغییرات ثبت شد');
    }

    public function deleteAdmin(Request $request)
    {
        $comment = ProductComment::find($request->comment_id);
        $comment_images = ProductCommentEditorImage::where('comment_id', $comment->id)->get();
        if (!$comment_images->isEmpty()) {
            $disk = Storage::disk('ftp');
            foreach ($comment_images as $ci) {
                $disk->delete($ci->path);
                $ci->delete();
            }
        }
        foreach ($comment->replies as $replyComment) {
            $replyComment->delete();
        }
        $comment->delete();
        return back()->with('success', 'با موفقیت حذف شد');
    }

    private function SendEmailToParentComment($user, $product, $request)
    {
        //if comment is not main comment 
        if (isset($request->parent_id)) {
            $parentComment = ProductComment::find($request->parent_id);
            $pCUser = $parentComment->user;
            //if comment was reply to reply
            if (isset($request->reply_id)) {
                $replyComment = ProductComment::find($request->reply_id);
                $rUser = $replyComment->user;
                //send NE to replyUser if reply user is not $user
                if (isset($rUser) && $user != $rUser) {
                    $this->NE($user, $rUser, $product);
                }
                //send NE to ParentUser if parent user is not $user
                if (isset($pUser) && $user != $pCUser && $pCUser != $rUser) {
                    $this->NE($user, $pCUser, $product);
                }
            }
            //if comment was reply to comment
            else {
                //send Ne to ParentUser if parent user is not user
                if (isset($user) && $user != $pCUser) {
                    $this->NE($user, $pCUser, $product);
                }
            }
        }
    }

    private function NE($fromUser, $toUser, $product)
    {
        if (isset($toUser)) {
            Mail::to($toUser->email)->send(new ReplyToCommentMail($product->title, $fromUser->username, route('product.show', $product->slug)));
        }
    }
}
