<div class="modal fade" id="qa-reply-modal" tabindex="-1" role="dialog" aria-labelledby="answer_to_reply_modal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row p-2 radius-10">
                    <div class="col-12">
                        <form class="mt-2" action="{{ route('category.comment.store') }}" method="POST"
                            role="form" id="qa-form">
                            @csrf

                            <input type="hidden" name="page" value="show_question">
                            <input type="hidden" id="qa-rep-question-id" name="object_id">
                            <input type="hidden" id="qa-rep-parent-id" name="parent_id">
                            <input type="hidden" id="qa-rep-replyto-id" name="reply_id">
                            <textarea required name="body" id="qa-rep-input" class="qa-form-ta" placeholder="نظر خود را بنویسید..."></textarea>

                            @if (auth('user')->check())
                                <button type="button" class="btn btn-outline-primary w-100 mt-2" id="qa-rep-send-btn"
                                    onclick="sendCommentBtnAction()">ثبت نظر</button>
                            @else
                                <button type="button" class="btn btn-primary w-100 mt-2"
                                    onclick="setActionForAfterAuth('reply', 'qa-form')">ثبت
                                    نظر</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
