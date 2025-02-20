<div class="modal fade" id="ccomReplyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <form action="{{ route('category.comment.store') }}" id="ccom-rep-form" method="POST" role="form">
                    @csrf

                    <input type="hidden" name="category_id" id="ccom-rep-category-id">
                    <input type="hidden" name="parent_id" id="ccom-rep-parent-id">
                    <input type="hidden" name="reply_to_id" id="ccom-rep-replyto-id">

                    <div class="form-group">
                        <textarea required class="form-control comment-modal-input" id="ccom-rep-input" name="body"
                            placeholder="دیدگاه خود را بنویسید..."></textarea>
                    </div>

                    @if (isset($user))
                        <button type="button" class="btn btn-outline-primary w-100"
                            onclick="sendCommentBtnAction(this,'ccom-rep-input','ccom-rep-form')">ثبت
                            نظر</button>
                    @else
                        <button type="button" class="btn btn-outline-primary w-100" data-dismiss="modal"
                            data-toggle="modal" data-target="#login_user"
                            onclick="setActionForAfterAuth('comment', 'ccom-rep-form')">ثبت نظر
                        </button>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
