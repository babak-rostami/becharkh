@extends('index')

@section('title')
    {{ $question->title }}
@endsection

@section('style')
    <meta name="title" content="{{ $question->title }}">

    @if ($question->body != null)
        <meta name="description" content="{{ $question->body }}">
    @endif

    <link rel="canonical" href="{{ Request::url() }}">

    @if ($question->google_index == 1)
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif

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

            @if ($question->close != 1)
                @include('mainPart.comment-box', ['page' => 'show_question'])
                @include('mainPart.mainPage.page-btns', ['page' => 'show_question'])
            @else
                <p class="alert alert-dark text-center">این گفتگو پاسخ داده شد و بسته شد.</p>
            @endif

            @if (isset($item))
                <div class="text-center py-4 px-2 mt-3" id="itempl-div"
                    style="--itempl-div-bg-url: url('{{ $item->image() }}')">
                    <span id="itempl-title">{{ $item->full_title ?? $item->title }}</span>
                    <span id="itempl-desc">گروه حل مشکلات و تبادل تجربیات کاربران در مورد
                        {{ $item->full_title ?? $item->title }}</span>
                    <a class="btn btn-lg btn-light mt-3" href="{{ $item->withParentsCommentUrl() }}">ورود به گروه
                        <img loading="lazy" src="{{ $ftp_path . 'files/other/images/left-arrow.png' }}" alt="next">
                    </a>
                </div>
            @endif

            {{-- پاسخ ها --}}

            @include('question.comment-items', [
                'comments' => $answers,
                'page' => 'question',
            ])
            {{-- @include('question.answers') --}}
            @include('mainPart.gallery')
            @include('category.rcats')

            @if (!$questions->isEmpty())
                @foreach ($questions as $ques)
                    <a class="questions-box" href="{{ route('question.show', $ques->slug2) }}">
                        @if ($ques->getImage())
                            <img class="lazy-load hop-img" data-src="{{ $ques->image() }}" alt="{{ $ques->title }}">
                        @endif
                        <h2 class="sq-item-title">{{ $ques->sug_title ?? $ques->title }}</h2>
                        @if (isset($ques->answer))
                            <span class="c-shortans">-{{ $ques->answer }}
                            </span>
                        @endif
                    </a>
                @endforeach
            @endif

        </div>
    </div>
@endsection

@section('script')
    <script>
        const page = 'show_question';
        const q_show_csrf = "{{ csrf_token() }}";
        const question_id = "{{ $question->id }}";
        let send_comment_after_login = 0;

        let nextPageUrl = '{{ isset($nextPageUrl) ? $nextPageUrl : null }}';
        let dontLoadMore = 0;

        let product_ids = {!! isset($affilates) ? json_encode($affilates->pluck('id')->toArray()) : '[]' !!};
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/show.min.js') . '?lm=' . filemtime('mixassets/js/forum/show.min.js') }}">
    </script>
@endsection
