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
                        <img id="item-img-0" fetchpriority="high" class="my-3"
                            onclick="clickGalleryImg('item-img-0','item-gallery')" src="{{ asset($item->image(0)) }}"
                            title="{{ $item->full_title ?? $item->title }}"
                            alt="عکس {{ $item->full_title ?? $item->title }}">
                    </div>
                @else
                    <img id="page-img" class="mb-3 mt-4" fetchpriority="high" src="{{ asset($category->image()) }}"
                        title="{{ $category->title }}" alt="{{ $category->title }}">
                @endif
            @endif
            @include('mainPart.gallery')
        </div>

        <div id="comImageModal" class="imgslider-modal">
            <span id="close-com-img">&times;</span>
            <img class="imgslider-modal-content" id="com-img">
        </div>

    </div>

    <div class="row bg-wht justify-content-center" id="forum-line-height">
        <div class="col-12 col-md-10">
            <div class="mt-2" id="page-title-box">
                @if (isset($category))
                    <h1 class="mt-4 text-right" id="page-title">{{ $meta_title }}</h1>
                    <p class="textarea-preline text-right mt-2">{{ $meta_desc }}</p>
                @else
                    <h1 class="mt-4 text-right" id="page-title">نظرات کاربران</h1>
                    <p class="text-right mt-2">از تجربیات شخصی ، پیشنهادات و نظرات درباره مسائل روزمره خود بنویسید.
                    </p>
                @endif
            </div>
            @if (isset($category) && $category->getAnim())
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <img alt="about page" src="{{ $category->anim() }}">
                        @if (!empty($category->tips) && is_array($category->tips))
                            <span id="cat-abt-title"></span>
                            <p id="cat-abt-desc"></p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- @endif --}}

            @include('item.top-users')

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

            @if (isset($ircats))
                <div class="col-12 text-center mt-4">
                    {{-- <span id="itempr-div-title">فروشگاه {{ $item->full_title ?? $item->title }}</span> --}}
                    <div class="bslider mt-2" id="ircat-slider">
                        @foreach ($ircats as $ircat)
                            <div class="bslider-item ircat-slider-item">
                                <a class="suggest-item" id="slidera-{{ $ircat->id }}" draggable="false"
                                    href="{{ $item->withParentsAdvertiseUrl($ircat->slug) }}">
                                    <img class="ircat-slider-item-img" draggable="false" src="{{ $ircat->thumb() }}">
                                    <span>
                                        {{ $ircat->full_title ?? $ircat->title }}
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (isset($item) && !empty($item->top_users))
                @include('item.item_top_users', [
                    'top_users' => $item->top_users,
                ])
            @else
                @if (isset($item) && $item->feature_id == '6682148710cf783aeb0ef6ce')
                    @include('item.telegram')
                @endif
            @endif
            @include('mainPart.mainPage.add-to-home')

            @if (isset($item) && isset($item->tags_array))
                <div id="item-tags-box">
                    <span id="item-tags-title">بحث های مهم</span>
                    <span id="item-tags-body">جستجوی سریع در بحث ها و مشکلات پرتکرار</span>
                    <div id="item-tags">
                        <span class="item-tag-selected"
                            onclick="selectItemTag('{{ $category->id }}','{{ $item->id }}','null')"
                            id="item-tag-null">همه نظرات
                            <img id="item-tag-tick-icon" alt="tick icon"
                                src="{{ $ftp_path . 'files/other/images/tick-18.png' }}">
                        </span>
                        @foreach (collect($item->tags_array)->sortBy('priority') as $itag)
                            @if (!isset($itag['parent_id']))
                                <span class="item-tag"
                                    onclick="selectItemTag('{{ $category->id }}','{{ $item->id }}','{{ $itag['id'] }}')"
                                    id="item-tag-{{ $itag['id'] }}">{{ $itag['title'] }} </span>
                            @endif
                        @endforeach
                    </div>
                    <div id="item-tags-2">
                    </div>
                    <div id="com-tags-loading">
                        <img src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
                        <span id="com-tags-loading-txt">در حال جستجو</span>
                    </div>
                    <div id="com-tags-loading-msg">
                        <span id="com-tags-loading-msg-txt"></span>
                    </div>
                </div>
            @endif

            @if ($hasComments == 0)
                @include('question.comment-items', [
                    'firstItems' => 1,
                    'comments' => $comments,
                    'page' => 'comment',
                ])
            @else
                @include('question.comment-items', [
                    'firstItems' => 1,
                    'comments' => $comments,
                    'page' => 'comment',
                ])
                <button class="btn btn-lg btn-dark w-100 mt-4" onclick="loadMorePosts()" id="load-more-com-btn">نمایش
                    نظرات
                    بیشتر ...</button>
                <div id="loadMore">
                    <span>در حال بارگیری نظرات بیشتر</span>
                    <br>
                    <img src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
                </div>
            @endif

            {{-- @if (isset($category))
                @if ($meta_desc_editor)
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="text-right" id="pdesctor">{!! $meta_desc_editor !!}</div>
                        </div>
                    </div>
                @endif
            @endif --}}


            <div class="row">
                <div class="col-12 my-3">
                    @include('mainPart.mainPage.breadc', ['page' => 'comment'])
                </div>
            </div>

            {{-- @if (isset($hot_pages))
                <div class="row mt-4 justify-content-center">
                    @include('mainPart.hot-pages')
                </div>
            @endif --}}

        </div>
    </div>
@endsection

@section('script')
    <script>
        send_comment_after_login = 0;
        const page = 'comment';
        const index_route = "{{ route('question.index') }}";
        const is_rtable_page = 0;

        let csrf_t = "{{ csrf_token() }}";
        let nextPageUrl = '{{ isset($nextPageUrl) ? $nextPageUrl : null }}';
        let dontLoadMore = 0;
        let loadingGif = '<img src="{{ $ftp_path . 'files/other/images/loading.gif' }}">';

        let product_ids = {!! isset($affilates) ? json_encode($affilates->pluck('id')->toArray()) : '[]' !!};
        const cat_tips = @json($category->tips ?? []);
    </script>


    <script type="text/javascript"
        src="{{ asset('mixassets/js/category/comment/index.min.js') . '?lm=' . filemtime('mixassets/js/category/comment/index.min.js') }}">
    </script>
@endsection
