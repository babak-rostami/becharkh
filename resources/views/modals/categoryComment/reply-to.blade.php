<div class="modal fade" id="replyto-{{ $reply->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <form action="{{ route('category.comment.store') }}" id="rep2_form-{{ $reply->id }}" method="POST"
                    role="form">
                    @csrf

                    <input type="hidden" name="category_id" value="{{ $comment->category_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <input type="hidden" name="reply_id" value="{{ $reply->id }}">

                    <div class="form-group">
                        <textarea required class="form-control comment-modal-input" id="rep2-input-{{ $reply->id }}" name="body"
                            placeholder="دیدگاه خود را بنویسید..."></textarea>
                    </div>

                    @if (isset($user))
                        <button type="button" class="btn btn-outline-primary w-100"
                            onclick="sendCommentBtnAction(this,'rep2-input-{{ $reply->id }}','rep2_form-{{ $reply->id }}')">ثبت
                            نظر</button>
                    @else
                        <button type="button" class="btn btn-outline-primary w-100" data-dismiss="modal"
                            data-toggle="modal" data-target="#login_user"
                            onclick="setActionForAfterAuth('comment', 'rep2_form-{{ $reply->id }}')">ثبت نظر
                        </button>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
