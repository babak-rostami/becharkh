<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($details as $detail)
        <url>
            <loc>
                {{ urldecode(route('car.detail.show',['brand_slug'=>$detail->brand->nameEn , 'model_slug'=>$detail->model->nameEn])) }}
            </loc>
            <lastmod>{{gmdate('Y-m-d\TH:i:s+00:00',strtotime($detail->updated_at))}}</lastmod>
            <changefreq>hourly</changefreq>
            <priority>0.9</priority>
            <image:image>
                <image:loc>
                    {{ asset('files/carmodel/images/'.$detail->image) }}
                </image:loc>
                <image:caption>{{ "بررسی مشخصات فنی ". $detail->brand->title." " .$detail->model->title . " ". $detail->production_year . " ".$detail->cylinder}}</image:caption>
                <image:title>{{ "بررسی مشخصات فنی ". $detail->brand->title." " .$detail->model->title}}</image:title>
            </image:image>
        </url>
    @endforeach
</urlset>
