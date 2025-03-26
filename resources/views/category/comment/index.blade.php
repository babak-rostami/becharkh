@extends('index')

@section('title')
    @if (isset($meta_title) && $meta_title != null)
        {{ $meta_title }}
    @else
        نظرات کاربران {{ isset($category) ? $category->title : '' }}
    @endif
@endsection

@section('style')

    @if (isset($meta_title) && $meta_title != null)
        <meta name="title" content="{{ $meta_title }}">
        <meta name="description" content="{{ $meta_desc }}">
    @else
        <meta name="title" content="نظرات کاربران {{ isset($category) ? $category->title : '' }}">
        <meta name="description" content="بحث و گفتگو و نظرات کاربران در زمینه های مختلف از شیر مرغ تا جون آدمیزاد">
    @endif

    @if (isset($item))
        <link rel="canonical" href="{{ $item->withParentsCommentUrl() }}">
    @else
        @if (isset($category))
            <link rel="canonical" href="{{ route('question.index', $category->slug) }}">
        @else
            <link rel="canonical" href="{{ route('question.index') }}">
        @endif
    @endif

    <meta name="robots" content="index, follow">

    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    {{-- <script src="{{ $ftp_path . 'library/ckeditor/ckeditor.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/ckfinder.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/de.js' }}"></script> --}}

    <link
        href="{{ asset('mixassets/css/category/comment/index.min.css') . '?lm=' . filemtime('mixassets/css/category/comment/index.min.css') }}"
        rel="stylesheet" type="text/css" />

    @if (isset($category) && isset($hasComments) && $hasComments == 1 && count($comments) >= 2)
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "QAPage",
          "mainEntity": {
            "@type": "Question",
            "name": "{{ $meta_title }}",
            "text": "{{ $meta_desc }}",
            "answerCount": {{count($comments)}},
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{$acceptedAnswer->body}}",
                "upvoteCount": {{$acceptedAnswer->like_count ?? 0}},
                "datePublished": "{{$acceptedAnswer->created_at->format('Y-m-d\TH:i:sP')}}"
                ,"author": {
                    "@type": "Person",
                    "name": "{{$acceptedAnswer->user->name}}",
                    "url": "{{ route('user.dashboard', $acceptedAnswer->user->username) }}"
                }
            },
            "suggestedAnswer": [
                @php($ifc = 1)
                @foreach($comments as $k => $c)
                    @if($k>5)
                        @break
                    @endif
                    @if($c->id != $acceptedAnswer->id)
                    @if($ifc == 0)
                    ,
                    @endif
                    @php($ifc = 0)
                    {
                        "@type": "Answer",
                        "text": "{{$c->body}}",
                        "upvoteCount": {{$c->like_count ?? 0}},
                        "datePublished": "{{$c->created_at->format('Y-m-d\TH:i:sP')}}"
                        ,"author": {
                            "@type": "Person",
                            "name": "{{$c->user->name}}",
                            "url": "{{ route('user.dashboard', $c->user->username) }}"
                        }
                    }
                    @endif
                @endforeach
            ]
          }
        }
        </script>
    @endif
@endsection

@section('content')
    <div class="row bg-wht justify-content-center">

        <div class="col-12">
            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 text-center">

            {{-- @include('mainPart.mainPage.fifil') --}}
            @include('mainPart.mainPage.cat-slider', [
                'page' => 'comment',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])


            @if (isset($category))
                @if (isset($item->images))
                    <div id="item-gallery">
                        @foreach ($item->images as $key => $img)
                            <img id="item-img-{{ $key }}" class="my-3 lazy-load"
                                onclick="clickGalleryImg('item-img-{{ $key }}','item-gallery')"
                                data-src="{{ asset($item->image($key)) }}" title="{{ $item->full_title ?? $item->title }}"
                                alt="عکس {{ $item->full_title ?? $item->title }}">
                        @endforeach
                    </div>
                @else
                    <img id="page-img" class="mb-3 mt-4" src="{{ asset($category->image()) }}"
                        title="{{ $category->title }}" alt="{{ $category->title }}">
                @endif
            @else
                <img class="mb-3 mt-4" src="{{ $ftp_path . 'files/other/images/cat-comments.png' }}" title="نظرات کاربران"
                    alt="نظرات کاربران">
            @endif

        </div>

        <div id="comImageModal" class="imgslider-modal">
            <span id="close-com-img">&times;</span>
            <img class="imgslider-modal-content" id="com-img">
        </div>

    </div>

    <div class="row bg-wht justify-content-center" id="forum-line-height">
        <div class="col-12 col-md-10">
            @include('mainPart.mainPage.pages-tabs', [
                'page' => 'comment',
                'item' => isset($item) ? $item : null,
                'user' => isset($user) ? $user : null,
                'is_follow' => isset($is_follow) ? $is_follow : null,
            ])

            @if (isset($category))
                <h1 class="mt-4 text-right" id="page-title">{{ $meta_title }}</h1>
                <p class="textarea-preline text-right mt-2">{{ $meta_desc }}</p>
            @else
                <h1 class="mt-4 text-right" id="page-title">نظرات کاربران</h1>
                <p class="text-right mt-2">از تجربیات شخصی ، پیشنهادات و نظرات درباره مسائل روزمره خود بنویسید.
                </p>
            @endif

            <div class="row">
                @include('item.top-users')
            </div>

            @include('mainPart.mainPage.page-btns', ['page' => 'comment'])

            @include('category.rcats', ['page' => 'comment'])

            @if (isset($category))
                <div class="row align-items-center justify-content-center mt-3">
                    <div class="col-12 text-right">
                        @include('mainPart.comment-box', ['page' => 'comment'])
                    </div>
                </div>
            @else
                <div id="edImageModal" class="ed-imgslider-modal">
                    <span id="close-ed-img">&times;</span>
                    <img class="ed-imgslider-modal-content" id="ed-img">
                </div>
            @endif

            @if (isset($category))
                <div class="row justify-content-center">
                    <div class="col-12 text-center">
                        @if ($hasComments == 0)
                            <div id="nocoms-box">
                                <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/uarrow.gif' }}"
                                    alt="arrow top">
                                <span id="nocoms-title">شروع گفتگو</span>
                                <span id="nocoms-decs">نظر خود را بنویسید</span>
                            </div>
                        @else
                            <div id="nocoms-box">
                                <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/uarrow.gif' }}"
                                    alt="arrow top">
                                <span id="nocoms-decs">نظر خود را اینجا بنویسید</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if (isset($pin_questions))
                <div class="row" id="pin-qs-box">
                    <div class="col-12 text-center">
                        @foreach ($pin_questions as $pin_question)
                            @if ($pin_question->slug2)
                                <a class="hop-item" href="{{ route('question.show', $pin_question->slug2) }}">
                                    @if ($pin_question->getImage())
                                        <img class="lazy-load hop-img" data-src="{{ $pin_question->image() }}"
                                            alt="{{ $pin_question->title }}">
                                    @else
                                        <img class="lazy-load rcir-glow"
                                            data-src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
                                    @endif
                                    <span class="hop-title">{{ $pin_question->title }}</span>
                                    <span class="hop-body">{{ $pin_question->body }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($hasComments == 0)
                <div class="row justify-content-center">
                    @if (isset($affilate))
                        <div class="col-12 text-right py-2 px-0">
                            @include('affilate.show-box')
                        </div>
                    @endif
                </div>
                @include('question.comment-items', [
                    'firstItems' => 1,
                    'comments' => $comments,
                ])
            @else
                @include('question.comment-items', ['firstItems' => 1, 'comments' => $comments])
                <button class="btn btn-primary w-100 mt-4" onclick="loadMorePosts()" id="load-more-com-btn">نمایش نظرات
                    بیشتر</button>
                <div id="loadMore">
                    <span>در حال بارگیری نظرات بیشتر</span>
                    <br>
                    <img src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
                </div>
            @endif

            @if (isset($category))
                @if ($meta_desc_editor)
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="text-right" id="pdesctor">{!! $meta_desc_editor !!}</div>
                        </div>
                    </div>
                @endif
            @endif


            @include('mainPart.mainPage.breadc', ['page' => 'comment'])

            @if (isset($hot_pages))
                <div class="row mt-4">
                    @include('mainPart.hot-pages')
                </div>
            @endif

        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        // send comment after auth
        send_comment_after_login = 0;
        //end send comment after auth        
        const page = 'comment';
        const yplayer = new Plyr('#affilb-video');
        const index_route = "{{ route('question.index') }}";
        const is_rtable_page = 0;

        var csrf_t = "{{ csrf_token() }}";
        let nextPageUrl = '{{ isset($nextPageUrl) ? $nextPageUrl : null }}';
        let category_comment_like_route = '{{ route('category.comment.like') }}';
        let dontLoadMore = 0;
        let follow_item_route = '{{ route('follow.item') }}';
        var loadingGif = '<img src="{{ $ftp_path . 'files/other/images/loading.gif' }}">';

        let editor_img_upload_route;
        setTimeout(function() {
            editor_img_upload_route =
                "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'comment']) }}";
        }, 500);

        const route_surop_choose = "{{ route('surop.choose') }}";

        let product_ids = {!! isset($affilate) ? json_encode([$affilate->id]) : '[]' !!};

        //for fifil
        // const fifil_load_items_route = "{{ route('fifil.load.items') }}";
        // let features = @json($features ?? []);
        // features = features.map(function(feature) {
        //     return {
        //         id: feature._id,
        //         title: feature.title,
        //         slug: feature.slug,
        //         p_id: feature.parent_id,
        //     };
        // });
        // let selected_items = @json($selected_items ?? []);
        // selected_items = selected_items.map(function(item) {
        //     return {
        //         id: item._id,
        //         title: item.title,
        //         p_id: item.parent_id ?? null,
        //         f_id: item.feature_id ?? null,
        //     };
        // });
        //end for fifil
    </script>


    <script type="text/javascript"
        src="{{ asset('mixassets/js/category/comment/index.min.js') . '?lm=' . filemtime('mixassets/js/category/comment/index.min.js') }}">
    </script>
@endsection
