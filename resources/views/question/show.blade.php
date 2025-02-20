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
                    @if ($question->getImage())
                        <img id="pquestion-title" src="{{ $question->image() }}" alt="{{ $question->title }}" title="{{ $question->title }}">
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
                    @if (isset($question->editor))
                        <div class="question-editor">{!! $question->editor !!}</div>
                    @endif

                    @include('survey.surshow', [
                        'object' => $question,
                    ])

                    {{-- @if (auth('user')->check())
                        <a href="" id="like-btn"
                            class="btn {{ $isLike ? 'btn-outline-success' : 'btn-outline-danger' }} my-3">
                            <span id="like-count-span">{{ $question->like_count ?? 0 }}</span>
                            <span id="like-span">{{ $isLike ? 'لایک شده' : 'لایک' }}</span>
                            <i class="bi bi-check-circle-fill"></i>
                        </a>
                    @else
                        <a href="" data-toggle="modal" data-target="#login_user"
                            onclick="setActionForAfterAuth(null,null)" class="btn btn-outline-danger my-3">
                            <span>{{ $question->like_count ?? 0 }}</span>
                            <span>لایک</span>
                            <i class="bi bi-check-circle-fill"></i>
                        </a>
                    @endif --}}

                    {{-- <div class="position-relative d-inline-block">
                        <button type="button" class="btn share-span">
                            اشتراک گذاری
                            <img class="lazy-load" data-src="{{ asset('files/other/images/share.png') }}">
                        </button>
                        <div class="share-box hide-share">
                            <div class="row">
                                <div class="col text-center cur-p" onclick="sentPageToTelegram()">
                                    <img class="lazy-load" data-src="{{ asset('files/other/images/telegram.png') }}">
                                    <br>
                                    <span>تلگرام</span>
                                </div>
                                <div class="col text-center cur-p" onclick="copyToClipboard()">
                                    <img class="lazy-load" data-src="{{ asset('files/other/images/chain.png') }}">
                                    <br>
                                    <span>کپی لینک</span>
                                </div>
                                <div class="col text-center cur-p" onclick="sentPageToWhatsapp()">
                                    <img class="lazy-load" data-src="{{ asset('files/other/images/whatsapp.png') }}">
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

                </div>

            </div>

            <h3 class="text-center mt-4">نظر شما چیه؟</h3>

            @include('mainPart.comment-box', ['page' => 'show_question'])

            <div class="row mt-4">
                @include('item.top-users')
            </div>

            @include('mainPart.mainPage.page-btns', ['page' => 'show_question'])

            {{-- پاسخ ها --}}
            @foreach ($answers as $key => $answer)
                @if ($key == 2)
                    @if (isset($affilate))
                        @include('affilate.show-box')
                    @endif
                @endif
                <div class="row bg-wht align-items-center py-3 mx-0 mt-3 answer-box">
                    <div class="col-12">
                        @include('modals.userdash', [
                            'dashuser' => $answer->user,
                            'lazyload' => 1,
                            'itemid' => $answer->id,
                        ])

                        @if (isset($answer->editor))
                            <div class="cedshow">
                                {!! $answer->editor !!}
                            </div>
                        @else
                            <p class="ml-2 mt-4 font-18 textarea-preline">{{ $answer->body }}</p>
                        @endif

                        <a class="comment-reply-btn" href="" data-toggle="modal"
                            data-target="#answer_to_answer-{{ $answer->id }}">پاسخ<img class="mr-1 lazy-load"
                                data-src="{{ asset('files/other/images/reply.png') }}"></a>

                        <span class="like-icon" onclick="like('{{ $answer->id }}')">
                            <span
                                id="like-answer-count-{{ $answer->id }}">{{ $answer->like_count ? $answer->like_count : 0 }}</span>
                            <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                        </span>
                        <span class="dislike-icon" onclick="unlike('{{ $answer->id }}')">
                            <span
                                id="unlike-answer-count-{{ $answer->id }}">{{ $answer->unlike_count ? $answer->unlike_count : 0 }}</span>
                            <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
                        </span>
                    </div>
                </div>
                {{-- مودال ریپلای به پاسخ --}}
                <div class="modal fade" id="answer_to_answer-{{ $answer->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="answer_to_answer_modal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row p-2 radius-10">
                                    <div class="col-12">
                                        <form class="mt-2" action="{{ route('question.answer.store') }}" method="POST"
                                            role="form" id="qa-form-{{ $answer->id }}">
                                            @csrf

                                            <input type="hidden" name="question_id" value="{{ $question->id }}">
                                            <input type="hidden" name="parent_id" value="{{ $answer->id }}">
                                            <textarea name="body" class="qa-form-ta" placeholder="نظر خود را بنویسید..."></textarea>


                                            @if (auth('user')->check())
                                                <button type="submit" class="btn btn-primary w-100">ارسال
                                                    نظر</button>
                                            @else
                                                <button type="button" class="btn btn-primary w-100" data-toggle="modal"
                                                    data-dismiss="modal" data-target="#login_user"
                                                    onclick="setActionForAfterAuth('answer', 'qa-form-{{ $answer->id }}')">ارسال
                                                    نظر</button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($answer->replies as $reply)
                    <div class="row py-3 radius-10 mr-3 ml-0 mt-1 reply-box">
                        <div class="col-12">
                            @include('modals.userdash', [
                                'dashuser' => $reply->user,
                                'lazyload' => 1,
                                'itemid' => $reply->id,
                            ])

                            <p class="ml-2 mt-4 font-18 textarea-preline">{{ $reply->body }}</p>

                            <a class="comment-reply-btn" href="" data-toggle="modal"
                                data-target="#answer_to_reply-{{ $reply->id }}">پاسخ<img class="mr-1 lazy-load"
                                    data-src="{{ asset('files/other/images/reply.png') }}"></a>

                            <span class="like-icon" onclick="like('{{ $reply->id }}')">
                                <span
                                    id="like-answer-count-{{ $reply->id }}">{{ $reply->like_count ? $reply->like_count : 0 }}</span>
                                <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                            </span>
                            <span class="dislike-icon" onclick="unlike('{{ $reply->id }}')">
                                <span
                                    id="unlike-answer-count-{{ $reply->id }}">{{ $reply->unlike_count ? $reply->unlike_count : 0 }}</span>
                                <img class="lazy-load"
                                    data-src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
                            </span>
                        </div>
                        {{-- مودال ریپلای به پاسخ --}}
                        <div class="modal fade" id="answer_to_reply-{{ $reply->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="answer_to_reply_modal" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="row p-2 radius-10">
                                            <div class="col-12">
                                                <form class="mt-2" action="{{ route('question.answer.store') }}"
                                                    method="POST" role="form" id="qa-form-{{ $reply->id }}">
                                                    @csrf

                                                    <input type="hidden" name="question_id"
                                                        value="{{ $question->id }}">
                                                    <input type="hidden" name="parent_id"
                                                        value="{{ $reply->parent_id }}">
                                                    <input type="hidden" name="reply_id" value="{{ $reply->id }}">
                                                    <textarea name="body" class="qa-form-ta" placeholder="نظر خود را بنویسید..."></textarea>

                                                    @if (auth('user')->check())
                                                        <button type="submit" class="btn btn-primary w-100">ارسال
                                                            نظر</button>
                                                    @else
                                                        <button type="button" class="btn btn-primary w-100"
                                                            data-toggle="modal" data-dismiss="modal"
                                                            data-target="#login_user"
                                                            onclick="setActionForAfterAuth('answer', 'qa-form-{{ $reply->id }}')">ارسال
                                                            نظر</button>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

            @if ($answers->isEmpty() || count($answers) < 3)
                @if (isset($affilate))
                    @include('affilate.show-box')
                @endif
            @endif

            @include('category.rcats')

            @if ($questions->count() > 1)
                <div class="row mx-0">
                    <div class="col-12 my-4 px-0">
                        <h3 class="text-center">شما هم سوالی دارید؟</h3>
                        <a class="btn btn-danger w-100 my-3" href="{{ route('question.create') }}">
                            ثبت سوال در انجمن
                            <img class="lazy-load" data-src="{{ asset('files/other/images/w-add.png') }}"
                                alt="add">
                        </a>
                    </div>

                    @foreach ($questions as $ques)
                        <div class="col-12 py-2 shadow-sm mb-2 radius-10 sq-box text-right">
                            <a class="bold-font-title text-decoration-none text-dark"
                                href="{{ route('question.show', ['category' => $ques->category->slug, 'slug' => $ques->slug, 'random' => $ques->random_id]) }}">
                                <div>
                                    <img class="sq-user-image lazy-load" data-src="{{ asset($ques->user->thumb()) }}"
                                        alt="User Image" />
                                    <span class="text-gray font-14">{{ $ques->user->username }}</span>
                                    @if ($ques->like_count > 0)
                                        <span class="sug-q-like-icon float-left">
                                            <span>{{ $ques->like_count }}</span>
                                            <img class="lazy-load"
                                                data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                                        </span>
                                    @endif
                                </div>
                                <h2 class="sq-item-title">{{ $ques->title }}</h2>
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
                    <a href="{{ route('question.index') }}" class="btn btn-primary w-100">ورود به صفحه انجمن</a>
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
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        const yplayer = new Plyr('#affilb-video');
        const page = 'show_question';
        const q_show_csrf = "{{ csrf_token() }}";
        const q_like_route = "{{ route('question.like') }}";
        const q_answer_like_route = "{{ route('question.answer.like') }}";
        const question_id = "{{ $question->id }}";
        var send_comment_after_login = 0;

        const follow_item_route = '{{ route('follow.item') }}';

        let editor_img_upload_route;
        setTimeout(function() {
            editor_img_upload_route =
                "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'show_question']) }}";
        }, 500);

        const route_surop_choose = "{{ route('surop.choose') }}";

        let product_ids = {!! isset($affilate) ? json_encode([$affilate->id]) : '[]' !!};
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/show.min.js') . '?lm=' . filemtime('mixassets/js/forum/show.min.js') }}">
    </script>
@endsection
