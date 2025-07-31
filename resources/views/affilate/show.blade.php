@extends('index')

@section('title')
    {{ $product->title }}
@endsection

@section('style')
    <meta name="title" content="{{ $product->title }}">
    <link rel="canonical" href="{{ Request::fullUrl() }}">
    @if ($product->google_index == 1)
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif
    <link
        href="{{ asset('mixassets/css/affilate/show.min.css') . '?lm=' . filemtime('mixassets/css/affilate/show.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>
@endsection

@section('content')
    @if (session('success'))
        <p class="alert alert-success text-center m-0">{{ session('success') }}</p>
    @endif

    <div class="row justify-content-center bg-wht">

        @if (isset($category))
            <div class="col-12 text-center mb-3">
                @include('mainPart.mainPage.cat-slider', [
                    'page' => 'show_product',
                    'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                    'suggestCats' => isset($suggestCats) ? $suggestCats : null,
                ])
            </div>
        @endif

        <div class="col-12 col-md-8 bg-wht radius-10 mt-2 text-right shadow-sm px-3">
            <main>

                @include('mainPart.mainPage.pages-tabs', [
                    'page' => 'show_product',
                    'item' => isset($item) ? $item : null,
                    'user' => isset($user) ? $user : null,
                    'is_follow' => isset($is_follow) ? $is_follow : null,
                ])

                @include('affilate.show-box', ['affilate' => $product])
                @include('mainPart.gallery')
                
                <h1 id="page-title" class="font-weight-bold">{{ $product->title }}</h1>

                <div class="bg-wht radius-10 py-3 product-post-content px-2">
                    {!! $product->body2 !!}
                </div>

                @if (isset($product->link) || isset($product->product_link))
                    <button id="affilb-link-{{ $product->id }}" class="product-aflink"
                        onclick="jsurl('{{ route('slink', $product->id) }}',1)">
                        <span>مشاهده قیمت و ثبت سفارش</span>
                        <img class="spb-arrow" src="{{ $ftp_path . 'files/other/images/next-light.png' }}">
                    </button>
                @endif

                @include('category.rcats')

                <div class="row px-3">
                    @include('item.top-users')
                </div>

                <hr>

                <div class="row align-items-center justify-content-center">
                    <div class="col-12 text-right">
                        <span id="pcmbox-ask">دیدگاه خود را درباره این کالا بنویسید</span>
                        @include('mainPart.comment-box', ['page' => 'show_product'])
                    </div>
                </div>

                @if ($comments->isEmpty())
                    <div class="row justify-content-center mt-5 p-3">
                        <div class="col-11 text-center">
                            <span class="nocm-title">هنوز نظری ثبت نشده است</span>
                            <br>
                            <span>نظر خود را به اشتراک بگذارید</span>
                            <hr>
                        </div>
                    </div>
                @else
                    @foreach ($comments as $comment)
                        <div class="row bg-wht mx-1 py-3 radius-10 mt-3 align-items-center comment-style">
                            <div class="col-12">
                                @if (isset($comment->user_id))
                                    @include('modals.userdash', [
                                        'dashuser' => $comment->user,
                                        'lazyload' => 1,
                                        'itemid' => $comment->id,
                                    ])
                                @else
                                    <img class="comment-profile-style rounded-circle mr-2 lazy-load"
                                        data-src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">{{ $comment->name }}
                                @endif

                                <div class="cedshow">
                                    <p class="comment-text">{!! $comment->editor !!}</p>
                                </div>

                                <a class="comment-reply-btn" href="" data-toggle="modal"
                                    data-target="#reply-{{ $comment->id }}">پاسخ<img class="mr-1 lazy-load"
                                        data-src="{{ asset('files/other/images/reply.png') }}"></a>

                                <span class="like-icon" onclick="likeProductComment('{{ $comment->id }}')">
                                    <span
                                        id="product-comment-like-count-{{ $comment->id }}">{{ $comment->like_count ? $comment->like_count : 0 }}</span>
                                    <img class="lazy-load"
                                        data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                                </span>
                                <span class="dislike-icon" onclick="unlikeProductComment('{{ $comment->id }}')">
                                    <span
                                        id="product-comment-unlike-count-{{ $comment->id }}">{{ $comment->unlike_count ? $comment->unlike_count : 0 }}</span>
                                    <img class="lazy-load"
                                        data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
                                </span>
                            </div>
                        </div>

                        @foreach ($comment->replies as $reply)
                            <div class="row bg-wht ml-1 py-2 mr-4 radius-10 my-1 reply-style">
                                <div class="col-12">
                                    @if (isset($reply->user_id))
                                        @include('modals.userdash', [
                                            'dashuser' => $reply->user,
                                            'lazyload' => 1,
                                            'itemid' => $reply->id,
                                        ])
                                    @else
                                        <img class="comment-profile-style rounded-circle mr-2 lazy-load"
                                            data-src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">{{ $reply->name }}
                                    @endif
                                    <p class="comment-text">{{ $reply->body }}</p>

                                    <a class="comment-reply-btn" href="" data-toggle="modal"
                                        data-target="#replyto-{{ $reply->id }}">پاسخ<img class="mr-1 lazy-load"
                                            data-src="{{ asset('files/other/images/reply.png') }}"></a>

                                    <span class="like-icon" onclick="likeProductComment('{{ $reply->id }}')">
                                        <span
                                            id="product-comment-like-count-{{ $reply->id }}">{{ $reply->like_count ? $reply->like_count : 0 }}</span>
                                        <img class="lazy-load"
                                            data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                                    </span>
                                    <span class="dislike-icon" onclick="unlikeProductComment('{{ $reply->id }}')">
                                        <span
                                            id="product-comment-unlike-count-{{ $reply->id }}">{{ $reply->unlike_count ? $reply->unlike_count : 0 }}</span>
                                        <img class="lazy-load"
                                            data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
                                    </span>

                                    {{-- <span
                                        class="float-left text-gray cm-time-style">{{ jdate($reply->created_at)->ago() }}</span> --}}
                                </div>
                            </div>

                            <!-- Comment Reply Modal-->
                            <div class="modal fade" id="replyto-{{ $reply->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">

                                            <form action="{{ route('product.comment.store') }}"
                                                id="replytoForm-{{ $reply->id }}" method="POST" role="form">
                                                @csrf

                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <input type="hidden" name="reply_id" value="{{ $reply->id }}">

                                                <div class="row p-3">
                                                    <div class="col-1 px-0 text-center">
                                                        @if ($user)
                                                            <img class="lazy-load com-user-img"
                                                                data-src="{{ asset($user->thumb()) }}">
                                                        @else
                                                            <img class="lazy-load com-user-img"
                                                                data-src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">
                                                        @endif
                                                    </div>
                                                    <div class="col-8 col-lg-9 px-0">
                                                        <textarea required class="w-100 comment-texterea" id="body2-{{ $reply->id }}" name="body"
                                                            placeholder="دیدگاه خود را بنویسید..."></textarea>
                                                    </div>
                                                    <div class="col-3 col-lg-2 text-center px-0">
                                                        @if ($user)
                                                            <button id="reply2-btn-{{ $reply->id }}" type="button"
                                                                class="btn btn-primary"
                                                                onclick="submitCommentClick('reply2','{{ $reply->id }}')">
                                                                ارسال
                                                            </button>
                                                        @else
                                                            <a href="" class="btn btn-primary"
                                                                data-dismiss="modal" data-toggle="modal"
                                                                data-target="#login_user"
                                                                onclick="setActionForAfterAuth('comment', 'replytoForm-{{ $reply->id }}')">
                                                                ارسال
                                                            </a>
                                                        @endif

                                                    </div>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!-- Comment Reply Modal-->
                        <div class="modal fade" id="reply-{{ $comment->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">

                                        <form action="{{ route('product.comment.store') }}"
                                            id="replyForm-{{ $comment->id }}" method="POST" role="form">
                                            @csrf

                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                                            <div class="row p-3">
                                                <div class="col-1 px-0 text-center">
                                                    @if ($user)
                                                        <img class="lazy-load com-user-img"
                                                            data-src="{{ asset($user->thumb()) }}">
                                                    @else
                                                        <img class="lazy-load com-user-img"
                                                            data-src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">
                                                    @endif
                                                </div>
                                                <div class="col-8 col-lg-9 px-0">
                                                    <textarea required class="w-100 comment-texterea" id="body1-{{ $comment->id }}" name="body"
                                                        placeholder="دیدگاه خود را بنویسید..."></textarea>

                                                </div>
                                                <div class="col-3 col-lg-2 text-center px-0">
                                                    @if ($user)
                                                        <button id="reply1-btn-{{ $comment->id }}" type="button"
                                                            class="btn btn-primary"
                                                            onclick="submitCommentClick('reply1','{{ $comment->id }}')">
                                                            ارسال
                                                        </button>
                                                    @else
                                                        <a href="" class="btn btn-primary" data-dismiss="modal"
                                                            data-toggle="modal" data-target="#login_user"
                                                            onclick="setActionForAfterAuth('comment', 'replyForm-{{ $comment->id }}')">
                                                            ارسال
                                                        </a>
                                                    @endif

                                                </div>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if (isset($affilates))
                    <div class="col-12 text-right py-2 mb-4 px-0">
                        @foreach ($affilates as $affilate)
                            @include('affilate.show-box', ['page' => 'advertise'])
                        @endforeach
                    </div>
                @endif

            </main>

        </div>

    </div>
@endsection

@section('script')
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        page = 'show_product';
        const yplayer = new Plyr('#affilb-video');
        let product_ids = {!! isset($product) ? json_encode([$product->id]) : '[]' !!};
        let affilate_ids = {!! isset($affilates) ? json_encode($affilates->pluck('id')->toArray()) : '[]' !!};
        product_ids = [...new Set([...product_ids, ...affilate_ids])];
        let follow_item_route = '{{ route('follow.item') }}';

        const product_comment_like_route = "{{ route('product.comment.like') }}";
        const product_show_csrf = "{{ csrf_token() }}";

        let editor_img_upload_route;
        setTimeout(function() {
            editor_img_upload_route =
                "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'show_product']) }}";
        }, 500);
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/affilate/show.min.js') . '?lm=' . filemtime('mixassets/js/affilate/show.min.js') }}">
    </script>
@endsection
