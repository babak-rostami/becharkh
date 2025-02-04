<?php $count = 0; ?>
<div class="row mx-0" id="commentsBox">
    @foreach ($comments as $comment)
        <?php $count += 1; ?>
        @if ($firstItems == 1 && $count == 4)
            @if (isset($hasComments) && $hasComments == 1 && isset($affilate))
                <div class="col-12 text-right py-2 px-0">
                    @include('affilate.show-box')
                </div>
            @endif
        @endif
        <div class="col-12 px-3 pb-3 pt-2 radius-10 mt-4 shadow-sm comment-box text-right"
            id="comment-box-{{ $comment->id }}">
            @if (isset($comment->user))
                @include('modals.userdash', [
                    'dashuser' => $comment->user,
                    'lazyload' => $firstItems,
                    'itemid' => $comment->id,
                ])
            @else
                <div>
                    <img class="comment-profile-style rounded-circle mr-2 @if ($firstItems == 1) lazy-load @endif"
                        @if ($firstItems == 1) data-src="{{ asset('files/other/images/profile.png') }}"@else
                                src="{{ asset('files/other/images/profile.png') }}" @endif>{{ $comment->name }}
                </div>
            @endif
            {{-- <span class="ml-1">({{ jdate($comment->created_at)->ago() }})</span> --}}
            @if (isset($comment->images))
                <div class="text-center">
                    <img onclick="clickCommentImg('{{ $comment->id }}')" id="c-img-{{ $comment->id }}"
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

            <a class="comment-reply-btn" href="" data-toggle="modal"
                data-target="#reply-{{ $comment->id }}">پاسخ<img
                    class="mr-1 @if ($firstItems == 1) lazy-load @endif"
                    @if ($firstItems == 1) data-src="{{ asset('files/other/images/reply.png') }}"@else
                        src="{{ asset('files/other/images/reply.png') }}" @endif></a>

            <span class="like-icon" onclick="likeCategoryComment('{{ $comment->id }}')">
                <span
                    id="category-comment-like-count-{{ $comment->id }}">{{ $comment->like_count ? $comment->like_count : 0 }}</span>
                @if ($firstItems == 1)
                    <img class="lazy-load like-com-image"
                        data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                        id="like-com-image-{{ $comment->id }}">
                @else
                    <img class="like-com-image" src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                        id="like-com-image-{{ $comment->id }}">
                @endif
            </span>

            <span class="dislike-icon" onclick="unlikeCategoryComment('{{ $comment->id }}')">
                <span
                    id="category-comment-unlike-count-{{ $comment->id }}">{{ $comment->unlike_count ? $comment->unlike_count : 0 }}</span>
                @if ($firstItems == 1)
                    <img class="lazy-load unlike-com-image"
                        data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}"
                        id="unlike-com-image-{{ $comment->id }}">
                @else
                    <img class="unlike-com-image" src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}"
                        id="unlike-com-image-{{ $comment->id }}">
                @endif
            </span>

            <span class="share-comment" id="share-comment-{{ $comment->id }}"
                onclick="shareComment('{{ $comment->id }}')">
                @if ($firstItems == 1)
                    <img data-src="{{ $ftp_path . 'files/other/images/share-18.png' }}" alt="share"
                        class="share-com-image lazy-load" id="share-com-image-{{ $comment->id }}">
                @else
                    <img src="{{ $ftp_path . 'files/other/images/share-18.png' }}" alt="share" class="share-com-image"
                        id="share-com-image-{{ $comment->id }}">
                @endif
            </span>

            <span class="copy-comment" id="copy-comment-{{ $comment->id }}"
                onclick="copyComment('{{ $comment->id }}')">
                @if ($firstItems == 1)
                    <img data-src="{{ $ftp_path . 'files/other/images/copy-18.png' }}" alt="copy"
                        class="copy-com-image lazy-load" id="copy-com-image-{{ $comment->id }}">
                @else
                    <img src="{{ $ftp_path . 'files/other/images/copy-18.png' }}" alt="copy" class="copy-com-image"
                        id="copy-com-image-{{ $comment->id }}">
                @endif
            </span>

            @if (!isset($item) || $hasComments == 0)
                <hr>
                @if (isset($comment->items_title))
                    @foreach ($comment->items_title as $title)
                        <span class="badge badge-light">{{ $title }}</span>
                    @endforeach
                @endif
            @endif
        </div>
        @foreach ($comment->replies as $reply)
            <div class="col-12 px-3 py-2 mr-2 radius-10 mt-1 comment-box text-right reply-div">
                @if (isset($reply->user))
                    @include('modals.userdash', [
                        'dashuser' => $reply->user,
                        'lazyload' => $firstItems,
                        'itemid' => $reply->id,
                    ])
                @else
                    <img class="comment-profile-style rounded-circle mr-2 @if ($firstItems == 1) lazy-load @endif"
                        @if ($firstItems == 1) data-src="{{ asset('files/other/images/profile.png') }}"@else
                                src="{{ asset('files/other/images/profile.png') }}" @endif>{{ $reply->name }}
                @endif
                {{-- <span class="ml-1">({{ jdate($reply->created_at)->ago() }})</span> --}}
                <p class="textarea-preline mt-2">{{ $reply->body }}</p>

                <a class="comment-reply-btn" href="" data-toggle="modal"
                    data-target="#reply-{{ $reply->id }}">پاسخ<img
                        class="mr-1 @if ($firstItems == 1) lazy-load @endif"
                        @if ($firstItems == 1) data-src="{{ asset('files/other/images/reply.png') }}"@else
                        src="{{ asset('files/other/images/reply.png') }}" @endif></a>

                <span class="like-icon" onclick="likeCategoryComment('{{ $reply->id }}')">
                    <span
                        id="category-comment-like-count-{{ $reply->id }}">{{ $reply->like_count ? $reply->like_count : 0 }}</span>
                    @if ($firstItems == 1)
                        <img class="lazy-load like-com-image"
                            data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                            id="like-com-image-{{ $reply->id }}">
                    @else
                        <img class="like-com-image" src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}"
                            id="like-com-image-{{ $reply->id }}">
                    @endif
                </span>
                <span class="dislike-icon" onclick="unlikeCategoryComment('{{ $reply->id }}')">
                    <span
                        id="category-comment-unlike-count-{{ $reply->id }}">{{ $reply->unlike_count ? $reply->unlike_count : 0 }}</span>
                    <img id="unlike-com-image-{{ $reply->id }}"
                        @if ($firstItems == 1) class="lazy-load unlike-com-image" 
                    data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}"
                    @else class="unlike-com-image"
                    src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}" @endif>
                </span>

            </div>

            <!-- Comment Reply Modal-->
            @include('modals.categoryComment.reply-to', [
                'reply' => $reply,
                'comment' => $comment,
            ])
        @endforeach
        @include('modals.categoryComment.reply', ['comment' => $comment])
    @endforeach
    @if ($firstItems == 1)
        @if ($count < 2)
            @if (isset($hasComments) && $hasComments == 1 && isset($affilate))
                <div class="col-12 text-right py-2 px-0">
                    @include('affilate.show-box')
                </div>
            @endif
        @elseif($count >= 2 && $count < 4)
            @if (isset($hasComments) && $hasComments == 1 && isset($affilate))
                <div class="col-12 text-right py-2 px-0">
                    @include('affilate.show-box')
                </div>
            @endif
        @endif
    @endif
</div>
