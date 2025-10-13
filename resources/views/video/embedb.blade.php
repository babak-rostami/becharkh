<!DOCTYPE html>
<html>

<head>
    <title>{{ $video->title }}</title>
    <meta name="title" content="{{ $video->title }}">
    <meta name="description" content="{{ $video->description }}">
    <link rel="canonical" href="{{ route('video.show', $video->slug2) }}" />

    <meta name="robots" content="noindex">

    <link
        href="{{ asset('mixassets/css/video/embedb.min.css') . '?lm=' . filemtime('mixassets/css/video/embedb.min.css') }}"
        rel="stylesheet" type="text/css" />
</head>

<body>
    @if (isset($video->video_path))
        <video preload="none" id="video-s" muted playsinline controls data-poster="{{ asset($video->image()) }}">
            <source src="{{ $video->videoPath() }}" type="video/mp4" />
        </video>
    @else
        <img id="video-img" alt="{{ $video->title }}" title="{{ $video->title }}" src="{{ asset($video->image()) }}">
    @endif

    <script type="text/javascript"
        src="{{ asset('mixassets/js/video/embedb.min.js') . '?lm=' . filemtime('mixassets/js/video/embedb.min.js') }}">
    </script>

    <script>
        const player = new Plyr('#video-s');
    </script>

</body>

</html>
