<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
    @foreach ($videos as $video)
        <url>
            <loc>
                {{ route('video.show',  $video->slug2) }}
            </loc>
            <video:video>
                <video:thumbnail_loc>{{ asset($video->image()) }}</video:thumbnail_loc>
                <video:title>{{ $video->title }}</video:title>
                <video:description>
                    {{ $video->description }}
                </video:description>
                <video:content_loc>
                    {{ $video->videoPath() }}
                </video:content_loc>
            </video:video>
        </url>
    @endforeach
</urlset>
