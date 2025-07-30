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
        @if (isset($comment->images))
            <div class="text-center">
                <img onclick="clickCommentImg('{{ $comment->id }}')" id="c-img-{{ $comment->id }}" class="com-img"
                    src="{{ $comment->imageFile() }}">
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
            onclick="openCCommentModal('reply','{{ $comment->category_id }}','{{ $comment->id }}')">پاسخ<img
                class="mr-1" src="{{ asset('files/other/images/reply.png') }}">
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
    </div>
    @foreach ($comment->replies as $reply)
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
                onclick="openCCommentModal('replyto','{{ $comment->category_id }}','{{ $comment->id }}','{{ $reply->id }}')">پاسخ<img
                    class="mr-1" src="{{ asset('files/other/images/reply.png') }}">
            </button>
            <span class="like-icon" onclick="likeCategoryComment('{{ $reply->id }}')">
                <span
                    id="category-comment-like-count-{{ $reply->id }}">{{ $reply->like_count ? $reply->like_count : 0 }}</span>
                <img class="like-com-image" src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                    id="like-com-image-{{ $reply->id }}">
            </span>
            <span class="dislike-icon" onclick="unlikeCategoryComment('{{ $reply->id }}')">
                <span
                    id="category-comment-unlike-count-{{ $reply->id }}">{{ $reply->unlike_count ? $reply->unlike_count : 0 }}</span>
                <img id="unlike-com-image-{{ $reply->id }}" class="unlike-com-image"
                    src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
            </span>
        </div>
    @endforeach
@endforeach
@include('modals.categoryComment.reply')
@while ($affnum != -1)
    @if ($affnum != -1 && isset($affilates) && $affilates->slice($affnum, 1)->first() != null)
        <div class="col-12 text-right py-2 px-0 mt-2">
            @include('affilate.show-box-api', [
                'affilate' => $affilates->slice($affnum, 1)->first(),
            ])
            @php $affnum += 1 @endphp
        </div>
    @else
        @php $affnum = -1 @endphp
    @endif
@endwhile
