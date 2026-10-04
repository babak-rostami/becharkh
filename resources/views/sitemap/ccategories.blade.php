<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach ($items as $item)
        <url>
            <loc>
                {{ urldecode($item->withParentsCommentUrl()) }}
            </loc>
            <lastmod>{{ gmdate('Y-m-d\TH:i:s+00:00', strtotime($item->updated_at)) }}</lastmod>
            <changefreq>hourly</changefreq>
            <priority>0.9</priority>
            @if (isset($item->images))
                <image:image>
                    <image:loc>
                        {{ $item->image() }}
                    </image:loc>
                    <image:title>نظرات کاربران - {{ $item->full_title ?? $item->title }}</image:title>
                </image:image>
            @endif
        </url>
    @endforeach
</urlset>
