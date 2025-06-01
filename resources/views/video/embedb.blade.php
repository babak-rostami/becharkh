<!DOCTYPE html>
<html>

<head>
    <title>{{ $video->title }}</title>
    <meta name="title" content="{{ $video->title }}">
    <meta name="description" content="{{ $video->description }}">
    <link rel="canonical" href="{{ route('video.show', $video->slug2) }}" />

    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    {{-- <script src="{{ asset('admin_c/assets/js/libs/jquery-3.1.1.min.js') }}"></script> --}}

    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> --}}

    <link href="{{ asset('assets/css/video/embedb.css') . '?lm=' . filemtime('assets/css/video/embedb.css') }}"
        rel="stylesheet" type="text/css" />
</head>

<body>
    @if (isset($video->video_path))
        <video preload="none" id="video-s" playsinline controls data-poster="{{ asset($video->image()) }}">
            <source src="{{ $video->videoPath() }}" type="video/mp4" />
        </video>
    @else
        <img id="video-img" alt="{{ $video->title }}" title="{{ $video->title }}" src="{{ asset($video->image()) }}">
    @endif

    {{-- <div id="n-v-box">
        <div class="row justify-content-center h-100 align-items-center">
            <div class="col-12 text-center" id="close-rv-box">
                <img id="close-rv" onclick="closeRV()" src="{{ $ftp_path . 'files/other/images/x.png' }}">
            </div>
        </div>
    </div> --}}


    {{-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script> --}}

    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        const player = new Plyr('#video-s');
        // function changeMainPageVideoInfo(title, v_time, v_url, user_name, user_dash, user_thumb, prod_url) {
        //     parent.changeIframeInfo(title, v_time, v_url, user_name, user_dash, user_thumb, prod_url)
        // }
    </script>

</body>

</html>
