@extends('index')


@section('title')
    {{ $video->title }}
@endsection


@section('style')
    <meta name="title" content="{{ $video->title }}">

    @if ($video->google_index == 1)
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex">
    @endif

    <link href="{{ asset('mixassets/css/video/show.min.css') . '?lm=' . filemtime('mixassets/css/video/show.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link rel="canonical" href="{{ url()->current() }}" />

    <script type="application/ld+json">
                {
                  "@context": "https://schema.org",
                  "@type": "VideoObject",
                  "name": "{{$video->title}}",
                  "description": "{{$video->description}}",
                  "thumbnailUrl": [
                    "{{asset($video->image())}}"
                   ],
                  "uploadDate": "{{$video->created_at->format('Y-m-d\TH:i:sP')}}",
                  @if($video->isVideoFromYoutue())
                      "contentUrl": "{{$format_vp}}",
                  @else
                      "contentUrl": "{{asset($video->videoPath())}}",
                  @endif
                  "embedUrl": "{{url()->current()}}",
                  "interactionStatistic": {
                      "@type": "InteractionCounter",
                      "interactionType": { "@type": "WatchAction" },
                      "userInteractionCount": {{$video->seen_count}}
                    }
                }
            </script>
@endsection


@section('content')
    @if (session('success'))
        <p class="alert alert-success text-center m-0">{{ session('success') }}</p>
    @endif

    <div class="row justify-content-around">

        <div class="col-12 col-md-8 bg-wht radius-10 mt-2 text-right shadow-sm px-3">
            <main>
                @if (isset($video->video_path))
                    <video id="video-s" playsinline controls data-poster="{{ asset($video->image()) }}">
                        <source src="{{ $video->videoPath() }}" type="video/mp4" />
                    </video>
                @else
                    <img id="video-img" alt="{{ $video->title }}" title="{{ $video->title }}"
                        src="{{ asset($video->image()) }}">
                @endif

                <h1 id="page-title">
                    {{ $video->title }}
                </h1>

                <p id="video-desc">{{ $video->description }}</p>

                @if (isset($affilate))
                    <a class="btn btn-dark w-100 mb-2" href="{{ route('product.show', $affilate->slug) }}">
                        مشاهده محصول و نظرات
                    </a>
                    @if (isset($affilate->link) || isset($affilate->product_link))
                        <button id="affilb-link-{{ $affilate->id }}" class="product-aflink"
                            onclick="jsurl('{{ route('slink', $affilate->id) }}',1)">
                            <span>مشاهده قیمت و ثبت سفارش</span>
                            <img loading="lazy" src="{{ $ftp_path . 'files/other/images/next-light-w.png' }}" alt="shop">
                        </button>
                    @endif
                @elseif(isset($question))
                    <a class="btn btn-dark w-100 mb-2" href="{{ route('question.show', $question->slug2) }}">
                        مشاهده مطلب و نظرات
                    </a>
                @elseif(isset($video->pr_link))
                    <button id="affilb-link-{{ $video->id }}" class="product-aflink" onclick="jsurl('{{ $video->pr_link }}',1)">
                        <span>مشاهده قیمت و ثبت سفارش</span>
                        <img loading="lazy" src="{{ $ftp_path . 'files/other/images/next-light-w.png' }}" alt="shop">
                    </button>
                @endif
                <hr>
            </main>

            <div class="row">
                @foreach ($videos as $v)
                    @if ($v->id != $video->id)
                        <div class="col-12 col-sm-6 related-post-hover mb-3">
                            <a class="text-decoration-none related-post-a" @if (!$v->google_index) rel="nofollow" @endif
                                href="{{ route('video.show', $v->slug2) }}">
                                <div class="row">
                                    <div class="col-auto mx-1">
                                        <img class="s-blog-img" loading="lazy" src="{{ asset($v->thumb()) }}">
                                    </div>
                                    <div class="col pr-0">
                                        <span class="sug-blogs-title">{{ $v->title }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>

    </div>
@endsection


@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/video/show.min.js') . '?lm=' . filemtime('mixassets/js/video/show.min.js') }}">
        </script>
    <script>
        const player = new Plyr('#video-s');
    </script>
@endsection