<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>'; ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>1</priority>
    </url>
    <url>
        <loc>{{ url('/ads') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/forum') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/forum?s=1') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/blogs') }}</loc>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>
</urlset>
