<div class="col-12" id="filter_search_box">
</div>

<div class="col-12 py-2 text-right top-filter-box my-2">
    @if (isset($category))
        <span class="cat-item-selected-bread-mobile">
            <span data-toggle="modal" data-target="#categoryChildren" onclick="showCatChildrenModal(0)"
                class="cat-item-bread-title">{{ $category->title }}</span>
            <span>
                @switch($page)
                    @case('comment')
                        <a class="decor-none" href="{{ route('question.index') . '?s=1' }}">
                            <img class="x-filter-mobile" src="{{ $ftp_path . 'files/other/images/w-close.webp' }}">
                        </a>
                    @break

                    @case('forum')
                        <a class="decor-none" href="{{ route('question.index') }}">
                            <img class="x-filter-mobile" src="{{ $ftp_path . 'files/other/images/w-close.webp' }}">
                        </a>
                    @break

                    @case('advertise')
                        <a class="decor-none" href="{{ route('ads.index') }}">
                            <img class="x-filter-mobile" src="{{ $ftp_path . 'files/other/images/w-close.webp' }}">
                        </a>
                    @break

                    @case('blog-index')
                        <a class="decor-none" href="{{ route('blog.index') }}">
                            <img class="x-filter-mobile" src="{{ $ftp_path . 'files/other/images/w-close.webp' }}">
                        </a>
                    @break
                @endswitch
            </span>
        </span>
        <div class="feature-filter-box" id="filter_box_sm">
        </div>
    @else
        <span class="cat-item-bread-mobile" onclick="showCatChildrenModal(0)" data-toggle="modal"
            data-target="#categoryChildren">
            انتخاب موضوع
            <img src="{{ $ftp_path . 'files/other/images/b-add.png' }}">
        </span>
    @endif
</div>

<div class="modal fade" id="categoryChildren" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <div class="row mx-2 mb-3">
                    <div class="col-12 px-0">
                        <button type="button" class="btn btn-outline-dark w-100" data-dismiss="modal"
                            aria-label="Close">بستن
                        </button>
                    </div>
                </div>

                <div class="row mx-2" id="show_categories_list_ch_modal">

                </div>

            </div>
        </div>
    </div>
</div>
