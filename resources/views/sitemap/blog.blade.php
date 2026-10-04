<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($blogs as $blog)
        <url>
            <loc>
                {{ urldecode(route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) ) }}
            </loc>
            <lastmod>{{gmdate('Y-m-d\TH:i:s+00:00',strtotime($blog->updated_at))}}</lastmod>
            <changefreq>hourly</changefreq>
            <priority>0.9</priority>
            <image:image>
                <image:loc>
                    {{ asset($blog->image()) }}
                </image:loc>
                <image:caption>{{$blog->short_description}}</image:caption>
                <image:title>{{ $blog->title }}</image:title>
            </image:image>
        </url>
    @endforeach
</urlset>
