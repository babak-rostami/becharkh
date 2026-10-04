<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($products as $product)
        <url>
            <loc>
                {{ urldecode( route('product.show', $product->slug) ) }}
            </loc>
            <lastmod>{{gmdate('Y-m-d\TH:i:s+00:00',strtotime($product->updated_at))}}</lastmod>
            <changefreq>hourly</changefreq>
            <priority>0.9</priority>
            <image:image>
                <image:loc>
                    {{ $product->image }}
                </image:loc>
                <image:caption>{{$product->first_p}}</image:caption>
                <image:title>{{ $product->title }}</image:title>
            </image:image>
        </url>
    @endforeach
</urlset>
