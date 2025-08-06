<div id="replies-box-{{ $comment->id }}">
    @foreach ($replies as $reply)
        <div class="col-12 px-3 py-2 mr-2 radius-10 mt-1 comment-box text-right reply-div"
            id="reply-box-{{ $reply->id }}">
            @include('modals.userdash', [
                'dashuser' => $reply->user,
                'lazyload' => 0,
                'itemid' => $reply->id,
            ])
            @if (isset($reply->reply_name))
                <span class="rep-name">پاسخ به {{ $reply->reply_name }}</span>
            @endif
            <span class="cm-box-time">{{ jdate($reply->created_at)->ago() }}</span>
            <p class="textarea-preline mt-2">{{ $reply->body }}</p>
            <button type="button" class="comment-reply-btn"
                onclick="openCCommentModal('replyto','{{ $comment->category_id }}','{{ $comment->id }}','{{ $reply->id }}')">
                پاسخ
                <img alt="reply icon" class="mr-1" src="{{ asset('files/other/images/reply.png') }}">
            </button>
            <span class="like-icon" onclick="likeCategoryComment('{{ $reply->id }}')">
                <span id="category-comment-like-count-{{ $reply->id }}">
                    {{ $reply->like_count ?: 0 }}
                </span>
                <img alt="like icon" class="like-com-image"
                    src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                    id="like-com-image-{{ $reply->id }}">
            </span>
            <span class="dislike-icon" onclick="unlikeCategoryComment('{{ $reply->id }}')">
                <span id="category-comment-unlike-count-{{ $reply->id }}">
                    {{ $reply->unlike_count ?: 0 }}
                </span>
                <img alt="dislike icon" id="unlike-com-image-{{ $reply->id }}" class="unlike-com-image"
                    src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
            </span>
            @if ($is_admin)
                <hr>
                <a target="_blank" class="btn btn-warning"
                    href="{{ route('admin.category.comment.edit', $reply->id) }}">ویرایش</a>
            @endif
        </div>
    @endforeach
</div>
