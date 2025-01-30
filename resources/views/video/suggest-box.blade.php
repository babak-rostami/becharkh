@if (count($hotVideos) > 0)
    <div class="col-12 px-0">
        <div class="row radius-10 mx-0">
            @foreach ($hotVideos as $key => $hv)
                @if ($key == 0)
                    <div class="col-12 text-right mb-3">
                        @if (isset($hv->video_path))
                            <video id="video-s" style="border:none;width:100%;height:292px" playsinline controls
                                data-poster="{{ $hv->image() }}">
                                <source src="{{ $hv->videoPath() }}" type="video/mp4" />
                            </video>
                        @else
                            <img id="video-img" alt="{{ $hv->title }}" title="{{ $hv->title }}"
                                src="{{ $hv->image() }}">
                        @endif

                        <a id="s-video-url" class="decor-none" @if (!$hv->google_index) rel="nofollow" @endif
                            href="{{ route('video.show', ['category_slug' => $hv->category->slug, 'video_slug' => $hv->slug, 'random_id' => $hv->random_id]) }}">
                            <span id="s-video-title">{{ $hv->title }}</span>
                        </a>
                        @if (isset($hv->pr_link))
                            <div id="shop-div">
                                <a id="s-v-pr-link" rel="nofollow" target="_blank" class="text-decoration-none"
                                    href="{{ $hv->pr_link }}">
                                    <span class="font-600">سفارش محصول</span>
                                    <span class="float-left">پرداخت درب منزل</span>
                                    <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/buy-24.png' }}"
                                        alt="shop">
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
        <div class="bslider mb-3" id="video-slider">
            @foreach ($hotVideos as $key => $hv)
                @if ($key > 0)
                    <div class="bslider-item video-slider-item radius-10">
                        <a id="slidera-{{ $hv->id }}" class="decor-none d-block" draggable="false"
                            @if (!$hv->google_index) rel="nofollow" @endif
                            href="{{ route('video.show', ['category_slug' => $hv->category->slug, 'video_slug' => $hv->slug, 'random_id' => $hv->random_id]) }}">
                            <img draggable="false" class="s-videos-img lazy-load" data-src="{{ $hv->thumb() }}"
                                alt="{{ $hv->title }}">
                            <span class="s-videos-title">{{ Str::limit($hv->title, 50) }}</span>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

@endif
