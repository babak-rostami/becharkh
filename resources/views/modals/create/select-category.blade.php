@if ($categories->count() == 1)
    <span class="label-title">دسته بندی آگهی</span>
    <span id="cat-selected-1s">{{ $categories->first()['title'] }}</span>
@else
    <span class="select-category-label">دسته بندی</span>
    <span id="modcat-select-input" data-toggle="modal" data-target="#select-category-modal"
        onclick="showCatChildrenModalForSCFCE(0)">انتخاب دسته بندی
        <img class="lazy-load float-left" data-src="{{ $ftp_path . 'files/other/images/next.png' }}">
    </span>
@endif

<div class="modal fade" id="select-category-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <div class="row mx-2 mb-3">
                    <div class="col-12 px-0">
                        <img class="cur-p" src="{{ asset('files/other/images/x-24.webp') }}" data-dismiss="modal"
                            aria-label="Close" alt="search">
                        <span class="float-left ml-2" id="search-cat-title">انتخاب دسته بندی</span>

                        <hr>
                        <div class="position-relative">
                            <input type="text" class="form-control" oninput="modcatSelectSearchCategories()"
                                id="search-categories-ch-modal-input" placeholder="جستجو کنید...">
                            <img id="modcat-select-search-img" src="{{ asset('files/other/images/search-gray.png') }}"
                                alt="search">
                        </div>
                    </div>
                </div>

                <div class="row mx-2" id="show_categories_list_ch_modal">

                </div>

            </div>
        </div>
    </div>
</div>
