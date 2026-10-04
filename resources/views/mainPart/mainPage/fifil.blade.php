@if (isset($category))
    <div class="text-right mt-3" id="fifil-box">
        @foreach ($features as $cfea)
            @if (!isset($cfea->parent_id) && $cfea->is_in_filter_rtable == 1)
                <span id="fifil-f-{{ $cfea->slug }}"
                    onclick="fifilClickFeature('{{ $cfea->id }}','{{ $cfea->title }}')" class="fifil-feature">
                    <span id="fifil-ftit-{{ $cfea->slug }}">{{ $cfea->title }}</span>
                    <img src="{{ $ftp_path . 'files/other/images/dropdown.png' }}">
                </span>
            @endif
        @endforeach
    </div>
@endif

<div class="modal fade" id="fifilter-modal" tabindex="-1" role="dialog" aria-labelledby="fifilterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-right">
                <div class="row" id="fifilter-ibox">
                    <div class="col-12">
                        <span id="fifilter-ibox-ftitle"></span>
                        <span id="fifilter-ibox-back" onclick="fifilBack()">بازگشت</span>
                        <input type="text" oninput="fifilterSearch()" class="form-control mb-2"
                            placeholder="جستجو کنید..." id="fifilter-ibox-search">
                        <div id="fifilter-ibox-items"></div>
                    </div>
                </div>
                <div class="row" id="fifilter-ibox-loading">
                    <div class="col-12 text-center">
                        <span id="fifilter-ibox-click"></span>
                        <img src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
