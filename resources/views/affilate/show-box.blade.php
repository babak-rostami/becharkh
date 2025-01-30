@if (!isset($page) || (isset($page) && $page != 'advertise' && $page != 'show_product'))
    {{-- <span id="affilb-box-title">پیشنهاد خرید </span>
    <div id="affil-actions">
        <span class="btn btn-sm btn-outline-dark" href="" data-toggle="modal" data-dismiss="modal"
            data-target="#new-afp">پیشنهاد جدید</span>
        <span class="btn btn-sm btn-outline-primary"
            onclick="sharePage('{{ route('product.show', $affilate->slug) }}')">ذخیره کردن</span>
        @include('mainPart.mainPage.share-page')
    </div> --}}
    <div class="modal fade" id="new-afp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <form action="{{ route('suggest.product') }}" method="post" role="form">
                                @csrf

                                <span id="sug-af-form-title">پیشنهاد خرید شما چیه؟</span>
                                <span id="sug-af-form-desc">فقط محصولات با کیفیت تایید میشود</span>
                                <textarea id="sug-af-form-body" name="body" class="form-control"
                                    placeholder="لینک محصول و توضیحات خود را اینجا بنویسید..."
                                    oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                                <input type="submit" value="ثبت" class="btn btn-success w-100">
                                <p id="sug-af-form-desc2">
                                    از کیفیت محصولی کاملاً رضایت دارید؟
                                    لینک خرید آن را با ما به اشتراک بگذارید.
                                    لینک را از فروشگاه‌های معتبر مثل دیجی‌کالا ارسال کنید. 🌟
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if (isset($affilate->video_id) && isset($page) && $page == 'show_product')
    <iframe class="shadow-sm p-0 m-0 mt-3 radius-10"
        src="{{ route('video.embedb.show', ['category_slug' => $affilate->video->category->slug, 'video_slug' => $affilate->video->slug, 'random_id' => $affilate->video->random_id]) }}"
        style="border:none;" width="100%" height="292px" allowfullscreen></iframe>
@endif

@if (
    (isset($affilate->video_id) && !isset($page)) ||
        (isset($affilate->video_id) && isset($page) && $page != 'show_product'))
    @if ($affilate->google_index)
        <a target="_blank" href="{{ route('product.show', $affilate->slug) }}"
            id="product-vimg-div-{{ $affilate->id }}">
            <span class="video-label">ویدیو</span>
            <img src="{{ $affilate->video->image() }}" alt="{{ $affilate->title }}">
        </a>
    @else
        <h2 id="product-vimg-div-{{ $affilate->id }}" onclick="jslink(route('product.show', $affilate->slug) , 1)">
            <span class="video-label">ویدیو</span>
            <img src="{{ $affilate->video->image() }}" alt="{{ $affilate->title }}">
        </h2>
    @endif
@endif

<div id="affilb-body-{{ $affilate->id }}"
    class="shadow-sm px-2 py-3 radius-10 {{ isset($page) && $page == 'advertise' ? 'my-4' : 'my-2' }}">
    {!! $affilate->body !!}</div>

@if (!isset($page) || (isset($page) && $page != 'show_product'))
    @if (isset($affilate->link) || isset($affilate->product_link))
        <button id="affilb-link-{{ $affilate->id }}" onclick="jsurl('{{ route('slink', $affilate->id) }}',1)">
            <span class="font-600">مشاهده قیمت و مشخصات</span>
            <img class="lazy-load spb-arrow" data-src="{{ $ftp_path . 'files/other/images/next-light.png' }}"
                alt="shop">
        </button>
    @endif
@endif
@include('mainPart.gallery')
