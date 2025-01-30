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
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    <link href="{{ asset('assets/css/video/show.css') . '?lm=' . filemtime('assets/css/video/show.css') }}" rel="stylesheet"
        type="text/css" />
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
{{-- "duration": "PT1M54S", --}}


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
                    @if (isset($affilate))
                        <a href="{{ route('product.show', $affilate->slug) }}">
                            {{ $video->title }}
                        </a>
                    @else
                        {{ $video->title }}
                    @endif
                </h1>

                <p id="video-desc">{{ $video->description }}</p>

                @if (isset($affilate))
                    {{-- <a id="affilb-link" rel="nofollow" target="_blank" class="text-decoration-none d-inline-block mb-4"
                        href="{{ route('slink', $affilate->id) }}">
                        <span class="font-600">مشاهده و خرید محصول</span>
                        <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/next-light-w.png' }}"
                            alt="shop">
                    </a> --}}
                    @if (isset($affilate->link) || isset($affilate->product_link))
                        <button id="affilb-link-{{ $affilate->id }}" class="product-aflink"
                            onclick="jsurl('{{ route('slink', $affilate->id) }}',1)">
                            <span>مشاهده قیمت و ثبت سفارش</span>
                            <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/next-light-w.png' }}"
                                alt="shop">
                        </button>
                    @endif
                @elseif(isset($video->pr_link))
                    <button id="affilb-link-{{ $video->id }}" class="product-aflink"
                        onclick="jsurl('{{ $video->pr_link }}',1)">
                        <span>مشاهده قیمت و ثبت سفارش</span>
                        <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/next-light-w.png' }}"
                            alt="shop">
                    </button>
                @endif

                {{-- <div class="mt-4">
                    <div class="position-relative d-inline-block">
                        <img class="mr-3 share-span" src="{{ $ftp_path . 'files/other/images/share.png' }}">
                        <div class="share-box hide-share">

                            <div class="row">
                                <div class="col text-center cur-p" onclick="sentPageToTelegram()">
                                    <img src="{{ $ftp_path . 'files/other/images/telegram.png' }}">
                                    <br>
                                    <span>تلگرام</span>
                                </div>
                                <div class="col text-center cur-p" onclick="copyToClipboard()">
                                    <img src="{{ $ftp_path . 'files/other/images/chain.png' }}">
                                    <br>
                                    <span>کپی لینک</span>
                                </div>
                                <div class="col text-center cur-p" onclick="sentPageToWhatsapp()">
                                    <img src="{{ $ftp_path . 'files/other/images/whatsapp.png' }}">
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
                    </div>
                </div> --}}
                <hr>
            </main>

            <div class="row">
                @foreach ($videos as $v)
                    @if ($v->id != $video->id)
                        <div class="col-12 col-sm-6 related-post-hover mb-3">
                            <a class="text-decoration-none related-post-a"
                                @if (!$v->google_index) rel="nofollow" @endif
                                href="{{ route('video.show', ['category_slug' => $v->category->slug, 'video_slug' => $v->slug, 'random_id' => $v->random_id]) }}">
                                <div class="row">
                                    <div class="col-auto mx-1">
                                        <img class="s-blog-img lazy-load" data-src="{{ asset($v->thumb()) }}">
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
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        const player = new Plyr('#video-s');
    </script>

    <script type="text/javascript"
        src="{{ asset('assets/js/video/show.js') . '?lm=' . filemtime('assets/js/video/show.js') }}"></script>
@endsection
