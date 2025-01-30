<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach ($questions as $question)
        <url>
            <loc>
                {{ urldecode(route('question.show', ['category' => $question->category->slug, 'slug' => $question->slug, 'random' => $question->random_id])) }}
            </loc>
            <lastmod>{{ gmdate('Y-m-d\TH:i:s+00:00', strtotime($question->updated_at)) }}</lastmod>
            <changefreq>hourly</changefreq>
            <priority>0.9</priority>
        </url>
    @endforeach
</urlset>
