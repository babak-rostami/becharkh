@extends('index')

@section('title')
    {{ $question->title }}
@endsection

@section('style')
    <meta name="title" content="{{ $question->title }}">

    @if ($question->body != null)
        <meta name="description" content="{{ $question->body }}">
    @endif

    <link rel="canonical" href="{{ Request::fullUrl() }}">

    @if ($question->google_index == 1)
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif

    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <link href="{{ asset('mixassets/css/forum/show.min.css') . '?lm=' . filemtime('mixassets/css/forum/show.min.css') }}"
        rel="stylesheet" type="text/css" />

    @if ($acceptedAnswer != null)
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "QAPage",
          "mainEntity": {
            "@type": "Question",
            "name": "{{ $question->title }}",
            "text": "{{ $question->body }}",
            "answerCount": {{$question->answer_count ?? 0}},
            "upvoteCount": {{$question->likes_count ?? 0}},
            "datePublished": "{{$question->created_at->format('Y-m-d\TH:i:sP')}}",
            "author": {
            "@type": "Person",
            "name": "{{$question->user->name}}",
            "url": "{{ route('user.dashboard', $question->user->username) }}"
            },
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{$acceptedAnswer->body}}",
                "upvoteCount": {{$acceptedAnswer->like_count ?? 0}},
                "datePublished": "{{$acceptedAnswer->created_at->format('Y-m-d\TH:i:sP')}}"
                @if(isset($acceptedAnswer->user))
                    ,"author": {
                        "@type": "Person",
                        "name": "{{$acceptedAnswer->user->name}}",
                        "url": "{{ route('user.dashboard', $acceptedAnswer->user->username) }}"
                    }
                @endif
            },
            "suggestedAnswer": [
                @php($ifc = 1)
                @foreach($answers as $k => $a)
                    @if($k>5)
                        @break
                    @endif
                    @if($a->id != $acceptedAnswer->id)
                    @if($ifc == 0)
                    ,
                    @endif
                    @php($ifc = 0)
                    {
                        "@type": "Answer",
                        "text": "{{$a->body}}",
                        "upvoteCount": {{$a->like_count ?? 0}},
                        "datePublished": "{{$a->created_at->format('Y-m-d\TH:i:sP')}}"
                        @if(isset($a->user))
                                ,"author": {
                                    "@type": "Person",
                                    "name": "{{$a->user->name}}",
                                    "url": "{{ route('user.dashboard', $a->user->username) }}"
                                }
                        @endif
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

    <div class="row justify-content-center pb-3 bg-wht">

        <div class="col-12 text-center mb-3">
            @include('mainPart.mainPage.cat-slider', [
                'page' => 'show_blog',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])
        </div>

        <div class="col-12 col-md-10 bg-wht text-right">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif

            <div class="row">
                <div class="col-12 text-center">
                    @if ($video)
                        <iframe class="shadow-sm p-0 m-0 mt-3 radius-10"
                            src="{{ route('video.embedb.show', $video->slug2) }}" style="border:none;" width="100%"
                            height="292px" allowfullscreen></iframe>
                    @else
                        @if ($question->getImage())
                            <img id="pquestion-img" src="{{ $question->image() }}" alt="{{ $question->title }}"
                                title="{{ $question->title }}">
                        @else
                            @if (isset($item))
                                <img id="pquestion-img" src="{{ $item->image() }}" alt="{{ $question->title }}"
                                    title="{{ $question->title }}">
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            @include('mainPart.mainPage.pages-tabs', [
                'page' => 'show_question',
                'item' => isset($item) ? $item : null,
                'user' => isset($user) ? $user : null,
                'is_follow' => isset($is_follow) ? $is_follow : null,
            ])


            {{-- سوال --}}
            <div class="row radius-10 bg-wht">
                <div class="col-12 p-2 mt-3">
                    @include('modals.userdash', [
                        'dashuser' => $question->user,
                        'lazyload' => 1,
                        'itemid' => 'q' . $question->id,
                    ])
                </div>

                <div class="col-12 mt-2">
                    <h1 id="page-title" class="bold-font-title">{{ $question->title }}</h1>
                    @if ($question->editor)
                        <div class="question-editor">{!! $question->editor !!}</div>
                    @else
                        <div class="question-editor">{{ $question->body }}</div>
                    @endif

                    @include('survey.surshow', [
                        'object' => $question,
                    ])

                </div>

            </div>

            <div class="row mb-3">
                @include('item.top-users')
            </div>

            @include('mainPart.comment-box', ['page' => 'show_question'])


            @include('mainPart.mainPage.page-btns', ['page' => 'show_question'])

            {{-- پاسخ ها --}}

            @if (isset($page_intro_title) && isset($page_intro_desc))
                <div id="page-g-div" class="text-center mt-3">
                    <img class="lazy-load" id="page-g-img"
                        data-src="{{ $ftp_path . 'files/other/images/approval-36.png' }}">
                    <span id="page-g-title">{{ $page_intro_title }}</span>
                    <span id="page-g">{{ $page_intro_desc }}</span>
                </div>
            @else
                @if ($answers->isEmpty())
                    <div id="noans-box">
                        <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/uarrow.gif' }}"
                            alt="arrow top">
                        <span id="noans-title">شروع گفتگو</span>
                        <span id="noans-decs">نظر خود را بنویسید</span>
                    </div>
                @else
                    <div id="noans-box">
                        <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/uarrow.gif' }}"
                            alt="arrow top">
                        <span id="noans-decs">نظر خود را اینجا بنویسید</span>
                    </div>
                @endif
            @endif

            @include('question.answers')

            @include('category.rcats')

            {{-- <div class="row mx-0">
                <div class="col-12 my-4 px-0 text-center">
                    <span>سوال شما قبلا در انجمن پرسیده نشده است؟</span>
                    <br>
                    <img class="mt-3 lazy-load" data-src="{{ $ftp_path . 'files/other/images/darrow.gif' }}"
                        alt="arrow down">
                    <br>
                    <a class="btn btn-lg btn-primary mb-3 mt-4" href="{{ route('question.create') }}">
                        سوال جدید +
                    </a>

                </div>
            </div> --}}

            @if ($questions->count() > 1)
                <div class="row mx-0">
                    @foreach ($questions as $ques)
                        <div class="col-12 p-2 shadow-sm mb-2 radius-10 sq-box text-right">
                            <a class="bold-font-title text-decoration-none text-dark"
                                href="{{ route('question.show', $ques->slug2) }}">
                                <div>
                                    @if ($ques->getImage())
                                        <img class="lazy-load hop-img" data-src="{{ $ques->image() }}"
                                            alt="{{ $ques->title }}">
                                    @endif
                                    @if ($ques->like_count > 0)
                                        <span class="sug-q-like-icon float-left">
                                            <span>{{ $ques->like_count }}</span>
                                            <img class="lazy-load"
                                                data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                                        </span>
                                    @endif
                                </div>
                                <h2 class="sq-item-title">{{ $ques->sug_title ?? $ques->title }}</h2>
                                @if (isset($ques->answer))
                                    <span class="c-shortans">-{{ $ques->answer }}
                                    </span>
                                @endif
                                @if (isset($ques->items_title))
                                    <div>
                                        @foreach ($ques->items_title as $qi)
                                            <span class="badge badge-light">{{ $qi }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (isset($hot_pages))
                <div class="row mt-4">
                    @include('mainPart.hot-pages')
                </div>
            @endif

        </div>
    </div>
@endsection

@section('script')
    <script>
        const page = 'show_question';
        const q_show_csrf = "{{ csrf_token() }}";
        const q_like_route = "{{ route('question.like') }}";
        const q_answer_like_route = "{{ route('question.answer.like') }}";
        const question_id = "{{ $question->id }}";
        var send_comment_after_login = 0;

        const reply_df_route = '{{ route('question.answer.store.dref') }}';

        const route_surop_choose = "{{ route('surop.choose') }}";

        let product_ids = {!! isset($affilates) ? json_encode($affilates->pluck('id')->toArray()) : '[]' !!};
    </script>

    @if ($user)
        <script>
            const
                editor_img_upload_route =
                "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'show_question']) }}";
        </script>
    @endif

    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/show.min.js') . '?lm=' . filemtime('mixassets/js/forum/show.min.js') }}">
    </script>
@endsection
