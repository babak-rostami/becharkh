@extends('index')


@section('title')
    {{ $blog->title }}
@endsection


@section('style')
    <meta name="title" content="{{ $blog->title }}">

    <link rel="canonical" href="{{ Request::fullUrl() }}">

    @if ($blog->google_index == 1)
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif

    <link href="{{ asset('mixassets/css/blog/show.min.css') . '?lm=' . filemtime('mixassets/css/blog/show.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />


    <script type="application/ld+json">
            {
              "@context": "http://schema.org",
              "@type": "BlogPosting",
              "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{{ route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) }}"
              },
              "headline": "{{$blog->title}}",
              "image": [
                "{{asset($blog->image())}}"
              ],
              "datePublished": "{{$blog->created_at->format('Y-m-d\TH:i:sP')}}",
              "dateModified": "{{$blog->updated_at->format('Y-m-d\TH:i:sP')}}",
              "author": {
                "@type": "Person",
                "name": "{{$blog->user->name}}",
                "url": "{{ route('user.dashboard', $blog->user->username) }}"
              },
              @if($blog->short_description != null)
              "description": "{{$blog->short_description}}"
              @endif
            }
            </script>
@endsection


@section('content')
    @if (session('success'))
        <p class="alert alert-success text-center m-0">{{ session('success') }}</p>
    @endif

    <div class="row justify-content-center bg-wht">

        <div class="col-12 text-center mb-3">
            @include('mainPart.mainPage.cat-slider', [
                'page' => 'show_blog',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])
        </div>

        <div class="col-12 col-md-8 bg-wht radius-10 mt-2 text-right shadow-sm px-3">
            <main>
                <h1 id="page-title" class="mt-3 mb-4 font-weight-bold text-center">{{ $blog->title }}</h1>
                <p class="mt-4">{{ $blog->short_description }}</p>

                @if ($blogVideo)
                    <iframe class="mb-3" src="{{ route('video.embedb.show', $blogVideo->slug2) }}" style="border:none;"
                        width="100%" height="300px" allowfullscreen></iframe>
                @else
                    <img id="blog-img" class="radius-10 mb-3" alt="{{ $blog->title }}" title="{{ $blog->title }}"
                        src="{{ asset($blog->image()) }}">
                @endif

                @include('mainPart.mainPage.pages-tabs', [
                    'page' => 'show_blog',
                    'item' => isset($item) ? $item : null,
                    'user' => isset($user) ? $user : null,
                    'is_follow' => isset($is_follow) ? $is_follow : null,
                ])

                @include('category.rcats')

                <div class="row mt-4">
                    @include('item.top-users')
                </div>

                <div class="bg-wht radius-10 py-3 blog-post-content px-2">
                    {!! $blog->content !!}
                </div>

                @if (isset($affilate))
                    @include('affilate.show-box')
                @endif

                {{-- <div class="mt-3">
                    @include('modals.userdash', [
                        'dashuser' => $blog->user,
                        'lazyload' => 1,
                        'itemid' => $blog->id,
                    ])
                </div>
                <br>
                @if ($user)
                    <a onclick="likeBlog(1)" id="like-btn" class="btn-outline-light">
                    </a>
                    <div class="d-inline-block position-relative">
                        <span id="like-count-span" class="badge badge-success">{{ $blog->like_count ?? 0 }}</span>
                        <span id="unlike-count-span" class="badge badge-danger">{{ $blog->unlike_count ?? 0 }}</span>
                    </div>
                    <a onclick="likeBlog(0)" id="unlike-btn" class="btn-outline-dark">
                    </a>
                @else
                    <a href="" data-toggle="modal" data-target="#login_user" id="like-btn"
                        onclick="setActionForAfterAuth(null, null)" class="btn-outline-light">
                    </a>
                    <div class="d-inline-block position-relative">
                        <span id="like-count-span" class="badge badge-success">{{ $blog->like_count ?? 0 }}</span>
                        <span id="unlike-count-span" class="badge badge-danger">{{ $blog->unlike_count ?? 0 }}</span>
                    </div>
                    <a href="" data-toggle="modal" data-target="#login_user" id="unlike-btn"
                        onclick="setActionForAfterAuth(null, null)" class="btn-outline-dark">
                    </a>
                @endif
                <img class="mr-3 comment-span lazy-load" data-src="{{ $ftp_path . 'files/other/images/comment2.png' }}">
                @if ($blog->comment_count)
                    <span class="mr-1">
                        {{ $blog->comment_count . '+' }} نظر
                    </span>
                @else
                    <span class="mr-1">
                        0 نظر</span>
                    </span>
                @endif

                <div class="position-relative d-inline-block">
                    <img class="mr-3 share-span lazy-load" data-src="{{ $ftp_path . 'files/other/images/share.png' }}">
                    <div class="share-box hide-share">

                        <div class="row">
                            <div class="col text-center cur-p" onclick="sentPageToTelegram()">
                                <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/telegram.png' }}">
                                <br>
                                <span>تلگرام</span>
                            </div>
                            <div class="col text-center cur-p" onclick="copyToClipboard()">
                                <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/chain.png' }}">
                                <br>
                                <span>کپی لینک</span>
                            </div>
                            <div class="col text-center cur-p" onclick="sentPageToWhatsapp()">
                                <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/whatsapp.png' }}">
                                <br>
                                <span>واتساپ</span>
                            </div>
                            <div class="col-12 mt-3 text-center">
                                <input class="form-control w-100" type="text" id="page-url-for-clipboard" readonly
                                    value="{{ Request::url() }}">
                                <p id="copy-clipboard-done-span">آدرس صفحه کپی شد</p>
                            </div>
                        </div>

                    </div>
                </div> --}}

                <div class="row align-items-center mt-5" id="commentsSection">
                    <div class="col-12">
                        <h3 class="text-center">نظر شما چیه؟</h3>
                        <div class="row mt-3 align-items-center radius-10 shadow-sm p-2 mx-1 comment-box">
                            <div class="col-1 px-0 text-center">
                                @if (auth('user')->check())
                                    <img class="lazy-load blog-com-user-img"
                                        data-src="{{ asset(auth('user')->user()->thumb()) }}">
                                @else
                                    <img class="lazy-load blog-com-user-img"
                                        data-src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">
                                @endif
                            </div>
                            <div class="col-11">
                                <form action="{{ route('blog.comment.store') }}" method="POST" role="form"
                                    id="blog_comment_form">
                                    @csrf
                                    <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                                    <div class="row align-items-center">
                                        <div class="col-9 col-sm-10 px-0">
                                            <textarea required class="w-100 comment-texterea p-3" name="body" placeholder="دیدگاه خود را بنویسید...">{{ old('body') }}</textarea>
                                            @error('body')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-3 col-sm-2 text-center px-0">
                                            @if ($user)
                                                <button type="submit" class="btn btn-primary">
                                                    ارسال
                                                </button>
                                            @else
                                                <a href="" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#login_user"
                                                    onclick="setActionForAfterAuth('comment', 'blog_comment_form')">
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

                @if ($blog->comments->isEmpty())
                    <div class="row justify-content-center mt-5 p-3">
                        <div class="col-11 text-center">
                            <span class="nocm-title">هنوز نظری ثبت نشده است</span>
                            <br>
                            <span>نظر خود را به اشتراک بگذارید</span>
                            <hr>
                        </div>
                    </div>
                @else
                    @foreach ($blog->comments as $comment)
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
                                <p class="comment-text">{{ $comment->body }}</p>

                                <a class="comment-reply-btn" href="" data-toggle="modal"
                                    data-target="#reply-{{ $comment->id }}">پاسخ<img class="mr-1 lazy-load"
                                        data-src="{{ asset('files/other/images/reply.png') }}"></a>

                                <span class="like-icon" onclick="likeBlogComment('{{ $comment->id }}')">
                                    <span
                                        id="blog-comment-like-count-{{ $comment->id }}">{{ $comment->like_count ? $comment->like_count : 0 }}</span>
                                    <img class="lazy-load"
                                        data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                                </span>
                                <span class="dislike-icon" onclick="unlikeBlogComment('{{ $comment->id }}')">
                                    <span
                                        id="blog-comment-unlike-count-{{ $comment->id }}">{{ $comment->unlike_count ? $comment->unlike_count : 0 }}</span>
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
                                    <span
                                        class="reply-user-color">{{ isset($reply->replyto) ? '@' . (isset($reply->replyto->user_id) ? $reply->replyto->user->username : $reply->replyto->name) : '' }}</span>

                                    <a class="comment-reply-btn" href="" data-toggle="modal"
                                        data-target="#replyto-{{ $reply->id }}">پاسخ<img class="mr-1 lazy-load"
                                            data-src="{{ asset('files/other/images/reply.png') }}"></a>

                                    <span class="like-icon" onclick="likeBlogComment('{{ $reply->id }}')">
                                        <span
                                            id="blog-comment-like-count-{{ $reply->id }}">{{ $reply->like_count ? $reply->like_count : 0 }}</span>
                                        <img class="lazy-load"
                                            data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                                    </span>
                                    <span class="dislike-icon" onclick="unlikeBlogComment('{{ $reply->id }}')">
                                        <span
                                            id="blog-comment-unlike-count-{{ $reply->id }}">{{ $reply->unlike_count ? $reply->unlike_count : 0 }}</span>
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

                                            <form action="{{ route('blog.comment.store') }}"
                                                id="replytoForm-{{ $reply->id }}" method="POST" role="form">
                                                @csrf

                                                <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <input type="hidden" name="reply_id" value="{{ $reply->id }}">


                                                <div class="row p-3">
                                                    <div class="col-1 px-0 text-center">
                                                        @if (auth('user')->check())
                                                            <img class="lazy-load blog-com-user-img"
                                                                data-src="{{ asset(auth('user')->user()->thumb()) }}">
                                                        @else
                                                            <img class="lazy-load blog-com-user-img"
                                                                data-src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">
                                                        @endif
                                                    </div>
                                                    <div class="col-8 col-lg-9 px-0">
                                                        <textarea required class="w-100 comment-texterea" name="body" placeholder="دیدگاه خود را بنویسید..."></textarea>
                                                    </div>
                                                    <div class="col-3 col-lg-2 text-center px-0">
                                                        @if ($user)
                                                            <button type="submit" class="btn btn-primary">
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

                                        <form action="{{ route('blog.comment.store') }}"
                                            id="replyForm-{{ $comment->id }}" method="POST" role="form">
                                            @csrf

                                            <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                                            <div class="row p-3">
                                                <div class="col-1 px-0 text-center">
                                                    @if (auth('user')->check())
                                                        <img class="lazy-load blog-com-user-img"
                                                            data-src="{{ asset(auth('user')->user()->thumb()) }}">
                                                    @else
                                                        <img class="lazy-load blog-com-user-img"
                                                            data-src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">
                                                    @endif
                                                </div>
                                                <div class="col-8 col-lg-9 px-0">
                                                    <textarea required class="w-100 comment-texterea" name="body" placeholder="دیدگاه خود را بنویسید..."></textarea>

                                                </div>
                                                <div class="col-3 col-lg-2 text-center px-0">
                                                    @if ($user)
                                                        <button type="submit" class="btn btn-primary">
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
            </main>

            <div class="row justify-content-center mt-3">
                <div class="col-12">
                    <h4 class="text-center mt-4">سوال های کاربران</h4>
                </div>
                <div class="mt-4" id="q-b-width">
                    @include('question.suggest-forums')
                </div>
                {{-- @include('video.suggest-box') --}}
            </div>

            <div class="row">
                @foreach ($blogs as $b)
                    @if ($b->id != $blog->id)
                        <div class="col-12 col-sm-6 related-post-hover mb-3">
                            <a class="text-decoration-none related-post-a"
                                href="{{ route('blog.show', ['category_slug' => $b->category->slug, 'slug' => $b->slug, 'random_id' => $b->random_id]) }}">
                                <div class="row">
                                    <div class="col-auto mx-1">
                                        <img class="s-blog-img lazy-load" data-src="{{ asset($b->thumb()) }}">
                                    </div>
                                    <div class="col pr-0">
                                        <span class="sug-blogs-title">{{ $b->title }}</span>
                                        <br>
                                        <span class="cm-time-style">{{ $b->user->username }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>

            @if (isset($hot_pages))
                <div class="row mt-4 justify-content-center">
                    @include('mainPart.hot-pages')
                </div>
            @endif

        </div>

    </div>
@endsection


@section('script')
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    {{-- @if ($user)
        <script>
            let is_like = "{{ $userIsLike }}";
            let is_unlike = "{{ $userIsUnLike }}";
        </script>
    @else
        <script>
            let is_like = 0;
            let is_unlike = 0;
        </script>
    @endif --}}
    <script>
        // send comment after auth
        page = 'show_blog';
        send_comment_after_login = 0;
        //end send comment after auth

        const yplayer = new Plyr('#affilb-video');
        // const blog_like_route = "{{ route('blog.like') }}";
        const blog_id = "{{ $blog->id }}";
        // const unlike_img = '{{ $ftp_path . 'files/other/images/b-unlike.webp' }}';
        // const no_unlike_img = '{{ $ftp_path . 'files/other/images/w-unlike.webp' }}';
        // const like_img = '{{ $ftp_path . 'files/other/images/red-like.png' }}';
        // const no_like_img = '{{ $ftp_path . 'files/other/images/white-like.png' }}';

        const follow_item_route = '{{ route('follow.item') }}';
        const blog_comment_like_route = "{{ route('blog.comment.like') }}";
        const blog_show_csrf = "{{ csrf_token() }}";

        let product_ids = {!! isset($affilate) ? json_encode([$affilate->id]) : '[]' !!};
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/blog/show.min.js') . '?lm=' . filemtime('mixassets/js/blog/show.min.js') }}"></script>
@endsection
