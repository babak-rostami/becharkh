@extends('index')

@section('title')
    @if (isset($meta_title) && $meta_title != null)
        {{ $meta_title }}
    @else
        مجله {{ isset($category) ? $category->title : '' }}
    @endif
@endsection

@section('style')
    @if (isset($meta_title) && $meta_title != null)
        <meta name="title" content="{{ $meta_title }}">
        <meta name="description" content="{{ $meta_desc }}">
    @else
        <meta name="title" content="مجله {{ isset($category) ? $category->title : '' }}">
        <meta name="description" content="مطالب آموزشی">
    @endif

    @if (isset($item))
        <link rel="canonical" href="{{ $item->withParentsBlogUrl() }}">
    @else
        @if (isset($category))
            <link rel="canonical" href="{{ route('blog.index', $category->slug) }}">
        @else
            <link rel="canonical" href="{{ route('blog.index') }}">
        @endif
    @endif

    <meta name="robots" content="index, follow">

    <link href="{{ asset('mixassets/css/blog/index.min.css') . '?lm=' . filemtime('mixassets/css/blog/index.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row bg-wht justify-content-center">

        <div class="col-12">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 text-center">

            @include('mainPart.mainPage.cat-slider', [
                'page' => 'blog-index',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])

            @if (isset($category))
                @if (isset($item->images))
                    <div id="item-gallery">
                        @foreach ($item->images as $key => $img)
                            <img id="item-img-{{ $key }}" class="my-3 lazy-load"
                                onclick="clickGalleryImg('item-img-{{ $key }}','item-gallery')"
                                data-src="{{ asset($item->image($key)) }}" title="{{ $item->full_title ?? $item->title }}"
                                alt="عکس {{ $item->full_title ?? $item->title }}">
                        @endforeach
                    </div>
                @else
                    <img id="page-img" class="mb-3 mt-4" src="{{ asset($category->image()) }}"
                        title="{{ $category->title }}" alt="{{ $category->title }}">
                @endif
            @else
                <h1 class="mt-4" id="page-title">مجله</h1>
                <img class="mb-3" src="{{ $ftp_path . 'files/other/images/cat-comments.png' }}" title="مجله"
                    alt="مجله">
            @endif
        </div>

        <div class="col-12 col-md-10">
            @include('mainPart.mainPage.pages-tabs', [
                'page' => 'blog-index',
                'item' => isset($item) ? $item : null,
                'user' => isset($user) ? $user : null,
                'is_follow' => isset($is_follow) ? $is_follow : null,
            ])

            @if (isset($meta_title) && $meta_title != null)
                <h1 class="mt-4 text-right" id="page-title">{{ $meta_title }}</h1>
            @else
                <h1 class="mt-4 text-right" id="page-title">مجله</h1>
            @endif
            @if (isset($title))
                <p class="text-right">مطالب آموزشی نوشته شده در مورد {{ $title }}</p>
            @endif

            @include('category.rcats', ['page' => 'blog-index'])

            {{-- <h4 class="text-center mt-5 mb-4">میخواهید محتوایی آموزشی بنویسید؟</h4>
            <a
                @if ($user) href="{{ route('user.new.post') }}" class="btn btn-danger w-100"
                    @else href="" class="btn btn-danger w-100" data-toggle="modal" data-dismiss="modal"
                    data-target="#login_user" @endif>
                نوشتن مطلب جدید
            </a> --}}

            <div class="row mt-4">
                @if ($blogs->count() > 0)
                    @foreach ($blogs as $blog)
                        <div class="col-6 col-md-4 px-0 my-2">
                            <a class="decor-none"
                                href="{{ route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) }}">

                                <div class="row mx-1 bg-wht radius-10 border-gray shadow-sm">
                                    <div class="col-12 text-center">
                                        <img class="blog-image" alt="{{ $blog->title }}" title="{{ $blog->title }}"
                                            src="{{ $blog->thumb() }}">
                                        @if ($blog->video())
                                            <div class="play-icon">
                                                <img src="{{ $ftp_path . 'files/other/images/play-white.png' }}">
                                            </div>
                                        @endif
                                        <img class="blog-user-profile" src="{{ asset($blog->user->thumb()) }}">
                                    </div>
                                    <div class="col-12 mt-4 pb-2 text-right">
                                        <span class="mt-2 blog-title">{{ $blog->title }}</span>
                                        <hr>
                                        <span
                                            class="text-gray">{{ $blog->category->full_title ?? $blog->category->title }}</span>
                                    </div>
                                </div>

                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center bg-wht p-3 radius-10 shadow-sm">
                        <img src="{{ $ftp_path . 'files/other/images/checklist.png' }}">
                        <br>
                        <span>مطلبی پیدا نشد</span>
                    </div>
                @endif
            </div>

            @include('mainPart.mainPage.breadc', ['page' => 'blog-index'])

        </div>


        <div class="col-12 mt-4 text-center order-2 order-lg-3 pr-0 pr-md-2 paginate-div" style="overflow-x: auto">
            {{ $blogs->links() }}
        </div>

    </div>
@endsection


@section('script')
    <script>
        const page = 'blog-index';
        const index_route = "{{ route('blog.index') }}";
        //for fifil
        // const fifil_load_items_route = "{{ route('fifil.load.items') }}";
        // let features = @json($features ?? []);
        // features = features.map(function(feature) {
        //     return {
        //         id: feature._id,
        //         title: feature.title,
        //         slug: feature.slug,
        //         p_id: feature.parent_id,
        //     };
        // });
        // let selected_items = @json($selected_items ?? []);
        // selected_items = selected_items.map(function(item) {
        //     return {
        //         id: item._id,
        //         title: item.title,
        //         p_id: item.parent_id ?? null,
        //         f_id: item.feature_id ?? null,
        //     };
        // });
        //end for fifil
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/blog/index.min.js') . '?lm=' . filemtime('mixassets/js/blog/index.min.js') }}">
    </script>
@endsection
