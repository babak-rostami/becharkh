<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
    @foreach ($videos as $video)
        <url>
            <loc>
                {{ route('blog.show', ['category_slug' => $video->blog->category->slug, 'slug' => $video->blog->slug, 'random_id' => $video->blog->random_id]) }}
            </loc>
            <video:video>
                <video:thumbnail_loc>{{ asset($video->blog->image()) }}</video:thumbnail_loc>
                <video:title>{{ $video->blog->title }}</video:title>
                <video:description>
                    {{ $video->blog->short_description }}
                </video:description>
                <video:content_loc>
                    https://dl.becharkh.com/user_files/{{ $video->path }}
                </video:content_loc>
            </video:video>
        </url>
    @endforeach
</urlset>
