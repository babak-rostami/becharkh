<div class="modal fade" id="reply-{{ $comment->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <form action="{{ route('category.comment.store') }}" method="POST" role="form"
                    id="rep_form-{{ $comment->id }}">
                    @csrf

                    <input type="hidden" name="category_id" value="{{ $comment->category_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <div class="form-group">
                        <textarea required class="form-control comment-modal-input" id="rep-input-{{ $comment->id }}" name="body"
                            placeholder="دیدگاه خود را بنویسید..."></textarea>
                    </div>

                    @if (isset($user))
                        <button type="button" class="btn btn-outline-primary w-100"
                            onclick="sendCommentBtnAction(this,'rep-input-{{ $comment->id }}','rep_form-{{ $comment->id }}')">ثبت
                            نظر</button>
                    @else
                        <button type="button" class="btn btn-outline-primary w-100" data-dismiss="modal"
                            data-toggle="modal" data-target="#login_user"
                            onclick="setActionForAfterAuth('comment', 'rep_form-{{ $comment->id }}')">ثبت نظر
                        </button>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
