<!DOCTYPE html>
<html>

<head>
    <title>{{ $video->title }}</title>
    <meta name="title" content="{{ $video->title }}">
    <meta name="description" content="{{ $video->description }}">
    <link rel="canonical"
        href="{{ route('video.show', ['category_slug' => $video->category->slug, 'video_slug' => $video->slug, 'random_id' => $video->random_id]) }}" />

    @if ($video->google_index == 1)
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex">
    @endif
    <style>
        #video-s {
            width: 100%;
            height: 290px;
            object-fit: contain
        }

        #video-img {
            width: 100%;
            object-fit: scale-down;
            border-radius: 8px;
        }
        body{
            overflow: hidden;
        }
    </style>
</head>

<body>
    @if ($video->hasVideoFile())
        <video controls poster="{{ asset($video->image()) }}" id="video-s">
            <source src="{{ $video->videoPath() }}" type="video/mp4">
        </video>
    @else
        <img id="video-img" alt="{{ $video->title }}" title="{{ $video->title }}" src="{{ asset($video->image()) }}">
    @endif
</body>

</html>
