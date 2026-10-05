@php
    $affnum = 0;
    $count = 0;
@endphp
@foreach ($comments as $count => $comment)
    @if (isset($affilates) && ($count + 1) % 4 == 0)
        @if ($affilates->slice($affnum, 1)->first() != null)
            <div class="col-12 text-right py-2 px-0 mt-2">
                @include('affilate.show-box-api', [
                    'affilate' => $affilates->slice($affnum, 1)->first(),
                    'page' => 'comment',
                    'show_link' => 1,
                ])
                @php $affnum += 1 @endphp
            </div>
        @endif
    @endif
    <div class="col-12 px-3 pb-3 pt-2 radius-10 mt-4 shadow-sm comment-box text-right"
        id="comment-box-{{ $comment->id }}">
        @include('modals.userdash', [
            'dashuser' => $comment->user,
            'lazyload' => 0,
            'itemid' => $comment->id,
        ])
        <span class="cm-box-time">{{ jdate($comment->created_at)->ago() }}</span>
        @if (isset($comment->iimages))
            <div class="com-img-box">
                @foreach ($comment->iimages as $iimg)
                    <img alt="comment image {{ $iimg['id'] }}"
                        onclick="clickGalleryImg('iimg-{{ $comment->id }}-{{ $iimg['id'] }}','comment')"
                        id="iimg-{{ $comment->id }}-{{ $iimg['id'] }}" class="com-img"
                        src="{{ $ftp_path . $iimg['path'] }}">
                @endforeach
            </div>
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
            <img class="like-com-image" src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                id="like-com-image-{{ $comment->id }}">
        </span>

        <span class="dislike-icon" onclick="unlikeCategoryComment('{{ $comment->id }}')">
            <span
                id="category-comment-unlike-count-{{ $comment->id }}">{{ $comment->unlike_count ? $comment->unlike_count : 0 }}</span>
            <img class="unlike-com-image" src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}"
                id="unlike-com-image-{{ $comment->id }}">
        </span>

        <hr>

        <button class="comment-reply-btn"
            onclick="openCCommentModal('reply','{{ isset($comment->question_id) ? $comment->question_id : $comment->category_id }}','{{ $comment->id }}')">پاسخ<img
                class="mr-1" src="{{ $ftp_path . 'files/other/images/reply.png' }}">
        </button>

        <span class="copy-comment" id="copy-comment-{{ $comment->id }}" onclick="copyComment('{{ $comment->id }}')">
            ذخیره
            <img src="{{ $ftp_path . 'files/other/images/copy-18.png' }}" alt="copy" class="copy-com-image"
                id="copy-com-image-{{ $comment->id }}">
        </span>

        <span class="share-comment" id="share-comment-{{ $comment->id }}"
            onclick="shareComment('{{ $comment->id }}')">
            ارسال
            <img src="{{ $ftp_path . 'files/other/images/share-18.png' }}" alt="share" class="share-com-image"
                id="share-com-image-{{ $comment->id }}">
        </span>

        @if ($comment->replies_count)
            <button class="show-replies-btn" id="show-replies-btn-{{ $comment->id }}"
                onclick="loadCommentReplies('{{ $comment->id }}')">
                نمایش {{ $comment->replies_count }} پاسخ به این نظر ...
            </button>

            <button class="toggle-replies-btn" id="toggle-replies-btn-{{ $comment->id }}"
                onclick="toggleReplies('{{ $comment->id }}')">
                پنهان کردن پاسخ‌ها
            </button>
        @endif
    </div>
@endforeach
@while ($affnum != -1)
    @if ($affnum != -1 && isset($affilates) && $affilates->slice($affnum, 1)->first() != null)
        <div class="col-12 text-right py-2 px-0 mt-2">
            @include('affilate.show-box-api', [
                'affilate' => $affilates->slice($affnum, 1)->first(),
                'page' => 'comment',
                'show_link' => 1,
            ])
            @php $affnum += 1 @endphp
        </div>
    @else
        @php $affnum = -1 @endphp
    @endif
@endwhile
