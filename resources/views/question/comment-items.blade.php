@php
    $affnum = 0;
    $pqnum = 0;
    $count = 0;
    $show_sugs = 0;
@endphp
<div class="row mx-0" id="commentsBox">
    @foreach ($comments as $count => $comment)
        @if ($firstItems == 1)
            @if ($count == 5 || $count == 9 || $count == 14)
                @if (isset($pin_questions) && $pin_questions->slice($pqnum, 1)->first() != null)
                    <div class="col-12 text-right py-2 px-0 mt-4">
                        @include('question.hot-question-item', [
                            'pin_question' => $pin_questions->slice($pqnum, 1)->first(),
                        ])
                        @php $pqnum += 1 @endphp
                    </div>
                @endif
            @endif
            @if (isset($affilates) && ($count + 1) % 4 == 0)
                @if ($affilates->slice($affnum, 1)->first() != null)
                    <div class="col-12 text-right py-2 px-0 mt-3">
                        @include('affilate.show-box', [
                            'affilate' => $affilates->slice($affnum, 1)->first(),
                        ])
                        @php $affnum += 1 @endphp
                    </div>
                @endif
            @endif
        @endif
        <div class="col-12 px-3 pb-3 pt-2 radius-10 mt-4 shadow-sm comment-box text-right"
            id="comment-box-{{ $comment->id }}">
            @include('modals.userdash', [
                'dashuser' => $comment->user,
                'lazyload' => $firstItems,
                'itemid' => $comment->id,
            ])
            <span class="cm-box-time">{{ jdate($comment->created_at)->ago() }}</span>
            @if (isset($comment->images))
                <div class="text-center">
                    <img alt="comment image" onclick="clickCommentImg('{{ $comment->id }}')"
                        id="c-img-{{ $comment->id }}"
                        class="com-img @if ($firstItems == 1) lazy-load @endif"
                        @if ($firstItems == 1) data-src="{{ $comment->imageFile() }}" @else
                    src="{{ $comment->imageFile() }}" @endif>
                </div>
                <hr>
            @endif
            @if (isset($comment->editor))
                <div class="cedshow">
                    {!! $comment->editor !!}
                </div>
            @else
                <p class="textarea-preline mt-2 mb-4">{{ $comment->body }}</p>
            @endif

            @include('survey.surshow', [
                'object' => $comment,
            ])

            <span class="like-icon" onclick="likeCategoryComment('{{ $comment->id }}')">
                <span
                    id="category-comment-like-count-{{ $comment->id }}">{{ $comment->like_count ? $comment->like_count : 0 }}</span>
                @if ($firstItems == 1)
                    <img alt="like icon" class="lazy-load like-com-image"
                        data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                        id="like-com-image-{{ $comment->id }}">
                @else
                    <img alt="like icon" class="like-com-image"
                        src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                        id="like-com-image-{{ $comment->id }}">
                @endif
            </span>

            <span class="dislike-icon" onclick="unlikeCategoryComment('{{ $comment->id }}')">
                <span
                    id="category-comment-unlike-count-{{ $comment->id }}">{{ $comment->unlike_count ? $comment->unlike_count : 0 }}</span>
                @if ($firstItems == 1)
                    <img alt="dislike icon" class="lazy-load unlike-com-image"
                        data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}"
                        id="unlike-com-image-{{ $comment->id }}">
                @else
                    <img alt="dislike icon" class="unlike-com-image"
                        src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}"
                        id="unlike-com-image-{{ $comment->id }}">
                @endif
            </span>

            <hr>

            <button class="comment-reply-btn"
                onclick="openCCommentModal('reply','{{ $comment->category_id }}','{{ $comment->id }}')">پاسخ<img
                    alt="reply icon" class="mr-1 @if ($firstItems == 1) lazy-load @endif"
                    @if ($firstItems == 1) data-src="{{ asset('files/other/images/reply.png') }}"@else
                        src="{{ asset('files/other/images/reply.png') }}" @endif>
            </button>

            <span class="copy-comment" id="copy-comment-{{ $comment->id }}"
                onclick="copyComment('{{ $comment->id }}')">
                ذخیره
                @if ($firstItems == 1)
                    <img data-src="{{ $ftp_path . 'files/other/images/copy-18.png' }}" alt="copy"
                        class="copy-com-image lazy-load" id="copy-com-image-{{ $comment->id }}">
                @else
                    <img src="{{ $ftp_path . 'files/other/images/copy-18.png' }}" alt="copy" class="copy-com-image"
                        id="copy-com-image-{{ $comment->id }}">
                @endif
            </span>

            <span class="share-comment" id="share-comment-{{ $comment->id }}"
                onclick="shareComment('{{ $comment->id }}')">
                ارسال
                @if ($firstItems == 1)
                    <img data-src="{{ $ftp_path . 'files/other/images/share-18.png' }}" alt="share"
                        class="share-com-image lazy-load" id="share-com-image-{{ $comment->id }}">
                @else
                    <img src="{{ $ftp_path . 'files/other/images/share-18.png' }}" alt="share"
                        class="share-com-image" id="share-com-image-{{ $comment->id }}">
                @endif
            </span>

            @if ($is_admin)
                <hr>
                <a target="_blank" class="btn btn-warning"
                    href="{{ route('admin.category.comment.edit', $comment->id) }}">ویرایش</a>
                @if (isset($comment->tags))
                    <br>
                    <span class="badge badge-light">تگ ها:</span>
                    @foreach ($comment->getTags() as $com_tag)
                        <span class="badge badge-dark">{{ $com_tag->title }}</span>
                    @endforeach
                @endif
            @endif

            @if ((!isset($item) || $hasComments == 0) && $firstItems == 1)
                <hr>
                @if (isset($comment->items_title))
                    @foreach ($comment->items_title as $title)
                        <span class="badge badge-light">{{ $title }}</span>
                    @endforeach
                @endif
            @endif
        </div>
        @foreach ($comment->replies as $reply)
            <div class="col-12 px-3 py-2 mr-2 radius-10 mt-1 comment-box text-right reply-div"
                id="reply-box-{{ $reply->id }}">
                @include('modals.userdash', [
                    'dashuser' => $reply->user,
                    'lazyload' => $firstItems,
                    'itemid' => $reply->id,
                ])
                @if (isset($reply->reply_name))
                    <span class="rep-name">پاسخ به {{ $reply->reply_name }}</span>
                @endif
                <span class="cm-box-time">{{ jdate($reply->created_at)->ago() }}</span>
                <p class="textarea-preline mt-2">{{ $reply->body }}</p>
                <button type="button" class="comment-reply-btn"
                    onclick="openCCommentModal('replyto','{{ $comment->category_id }}','{{ $comment->id }}','{{ $reply->id }}')">پاسخ<img
                        alt="reply icon" class="mr-1 @if ($firstItems == 1) lazy-load @endif"
                        @if ($firstItems == 1) data-src="{{ asset('files/other/images/reply.png') }}"@else
                        src="{{ asset('files/other/images/reply.png') }}" @endif>
                </button>
                <span class="like-icon" onclick="likeCategoryComment('{{ $reply->id }}')">
                    <span
                        id="category-comment-like-count-{{ $reply->id }}">{{ $reply->like_count ? $reply->like_count : 0 }}</span>
                    @if ($firstItems == 1)
                        <img alt="like icon" class="lazy-load like-com-image"
                            data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                            id="like-com-image-{{ $reply->id }}">
                    @else
                        <img alt="like icon" class="like-com-image"
                            src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                            id="like-com-image-{{ $reply->id }}">
                    @endif
                </span>
                <span class="dislike-icon" onclick="unlikeCategoryComment('{{ $reply->id }}')">
                    <span
                        id="category-comment-unlike-count-{{ $reply->id }}">{{ $reply->unlike_count ? $reply->unlike_count : 0 }}</span>
                    <img alt="dislike icon" id="unlike-com-image-{{ $reply->id }}"
                        @if ($firstItems == 1) class="lazy-load unlike-com-image" 
                    data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}"
                    @else class="unlike-com-image"
                    src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}" @endif>
                </span>
                @if ($is_admin)
                    <hr>
                    <a target="_blank" class="btn btn-warning"
                        href="{{ route('admin.category.comment.edit', $reply->id) }}">ویرایش</a>
                @endif
            </div>
        @endforeach
    @endforeach
    @include('modals.categoryComment.reply')
    @if ($firstItems == 1)
        @while ($affnum != -1 && $pqnum != -1)
            @if ($affnum != -1 && isset($affilates) && $affilates->slice($affnum, 1)->first() != null)
                <div class="col-12 text-right py-2 px-0 mt-3">
                    @include('affilate.show-box', [
                        'affilate' => $affilates->slice($affnum, 1)->first(),
                    ])
                    @php $affnum += 1 @endphp
                </div>
            @else
                @php $affnum = -1 @endphp
            @endif
            @if ($pqnum != -1 && isset($pin_questions) && $pin_questions->slice($pqnum, 1)->first() != null)
                <div class="col-12 text-right py-2 px-0 mt-4">
                    @include('question.hot-question-item', [
                        'pin_question' => $pin_questions->slice($affnum, 1)->first(),
                    ])
                    @php $pqnum += 1 @endphp
                </div>
            @else
                @php $pqnum = -1 @endphp
            @endif
        @endwhile
    @endif
</div>
