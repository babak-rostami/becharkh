@if (isset($category))
    <div class="text-right">
        <a class="breadcr" href="{{ route('home') }}">بچرخ</a>
        <span class="breadcr-devider">></span>
        @switch($page)
            @case('comment')
                <a class="breadcr" href="{{ route('question.index') . '?s=1' }}">نظرات کاربران</a>
                <span class="breadcr-devider">></span>
                @if (!isset($item))
                    <span>{{ $category->full_title ?? $category->title }}</span>
                @else
                    <a class="breadcr"
                        href="{{ route('question.index', $category->slug) . '?s=1' }}">{{ $category->full_title ?? $category->title }}</a>
                    @if (isset($item->parent))
                        <span class="breadcr-devider">></span>
                        <a class="breadcr" href="{{ $item->parent->withParentsCommentUrl() }}">{{ $item->parent->title }}</a>
                    @endif
                    <span class="breadcr-devider">></span>
                    <span>{{ $item->full_title ?? $item->title }}</span>
                @endif
            @break

            @case('forum')
                <a class="breadcr" href="{{ route('question.index') }}">انجمن</a>
                <span class="breadcr-devider">></span>
                @if (!isset($item))
                    <span>{{ $category->full_title ?? $category->title }}</span>
                @else
                    <a class="breadcr"
                        href="{{ route('question.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
                    @if (isset($item->parent))
                        <span class="breadcr-devider">></span>
                        <a class="breadcr" href="{{ $item->parent->withParentsForumUrl() }}">{{ $item->parent->title }}</a>
                    @endif
                    <span class="breadcr-devider">></span>
                    <span>{{ $item->full_title ?? $item->title }}</span>
                @endif
            @break

            @case('advertise')
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('blog-index')
                <a class="breadcr" href="{{ route('blog.index') }}">آموزشی</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('blog.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_blog')
                <a class="breadcr" href="{{ route('blog.index') }}">آموزشی</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('blog.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_question')
                <a class="breadcr" href="{{ route('question.index') }}">انجمن</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('question.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_advertise')
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_product')
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

        @endswitch
    </div>
@else
    <div class="text-right">
        <a class="breadcr" href="{{ route('home') }}">بچرخ</a>
        @switch($page)
            @case('show_blog')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('blog.index') }}">آموزشی</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('blog.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_question')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('question.index') }}">انجمن</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('question.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_advertise')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_product')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break
        @endswitch
    </div>
@endif
