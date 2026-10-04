@if (count($hotVideos) > 0)
    <div class="col-12 px-0">
        <div class="row mb-3 radius-10 mx-0">
            @foreach ($hotVideos as $key => $hv)
                @if ($key == 0)
                    <div class="col-12 text-right mb-3">
                        <iframe class="shadow-sm p-0 m-0 radius-10" src="{{ route('video.embedb.show', $hv->slug2) }}"
                            style="border:none;" width="100%" height="400" allowfullscreen></iframe>

                        <a id="s-video-url" class="decor-none" @if (!$hv->google_index) rel="nofollow" @endif
                            href="{{ route('video.show', $hv->slug2) }}">
                            <span id="s-video-title">{{ $hv->title }}</span>
                        </a>
                        <?php            $hva = $hv->advertise(); ?>
                        @if ($hva != null && isset($hva->site_link))
                            <?php                $has_show = 1; ?>
                        @else
                            <?php                $has_show = 0; ?>
                        @endif
                        <div id="shop-div" @if ($has_show == 0) style="display: none" @endif>
                            <a id="s-v-pr-link" rel="nofollow" target="_blank" class="text-decoration-none"
                                href="@if ($has_show) {{ $hva->site_link }} @endif">
                                <img loading="lazy" src="{{ asset('files/other/images/b-shop.webp') }}" alt="shop">
                                <span class="font-600">سفارش محصول</span>
                                <img loading="lazy" id="go-shop-img" src="{{ asset('files/other/images/next-light.png') }}">
                            </a>
                            <hr>
                            @if ($hv->isVideoFromYoutue())
                                <div id="s-v-pr-u-dash">
                                    <img loading="lazy" id="s-video-shop-user-img" src="{{ asset('files/other/images/profile.png') }}">
                                    <span id="sug-video-user-span">کاربر مهمان</span>
                                    <span id="sug-video-time-span">{{ jdate($hv->created_at)->ago() }}</span>
                                </div>
                            @else
                                <a id="s-v-pr-u-dash" rel="nofollow" class="decor-none"
                                    href="{{ route('user.dashboard', $hv->user->username) }}">
                                    <img loading="lazy" id="s-video-shop-user-img" src="{{ asset($hv->user->thumb()) }}">
                                    <span id="sug-video-user-span">{{ $hv->user->username }}</span>
                                    <span id="sug-video-time-span">{{ jdate($hv->created_at)->ago() }}</span>
                                </a>
                            @endif
                        </div>
                        <div class="row align-items-center mb-2" id="no-shop-div" @if ($has_show) style="display: none" @endif>
                            @if ($hv->isVideoFromYoutue())
                                <div id="s-v-u-dash" class="mr-3">
                                    <img loading="lazy" id="s-video-user-img" src="{{ asset('files/other/images/profile.png') }}">
                                    <span id="sug-video-user-span2">کاربر مهمان</span>
                                    <span id="sug-video-time-span2">{{ jdate($hv->created_at)->ago() }}</span>
                                </div>
                            @else
                                <a id="s-v-u-dash" rel="nofollow" title="{{ $hv->user->username }}" class="decor-none mr-3"
                                    href="{{ route('user.dashboard', $hv->user->username) }}">
                                    <img loading="lazy" id="s-video-user-img" src="{{ asset($hv->user->thumb()) }}">
                                    <span id="sug-video-user-span2">{{ $hv->user->username }}</span>
                                    <span id="sug-video-time-span2">{{ jdate($hv->created_at)->ago() }}</span>
                                </a>
                            @endif
                        </div>
                        @if ($hv->hotComment() != null)
                            <div id="sug-vid-com-div">
                                <span class="font-600">نظرات</span>
                                <span id="sug-video-com-count-span">{{ $hv->comment_count ?? 0 }}</span>
                                <br>
                                <img loading="lazy" id="sug-vid-com-img" src="{{ asset($hv->hotComment()->user->thumb()) }}"
                                    alt="user image">
                                <span id="sug-vid-com-text">{{ Str::limit($hv->hotComment()->body, 80) }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
        <div class="swiper sugVidsSwiper mb-3">
            <div class="swiper-wrapper">
                @foreach ($hotVideos as $key => $hv)
                    @if ($key > 0)
                        <div class="swiper-slide radius-10">
                            <a class="decor-none" @if (!$hv->google_index) rel="nofollow" @endif
                                href="{{ route('video.show', $hv->slug2) }}">
                                <img class="s-videos-img" loading="lazy" src="{{ $hv->thumb() }}" alt="{{ $hv->title }}">
                                <span class="sug-video-vid-span">ویدیو</span>
                                <span class="s-videos-title">{{ Str::limit($hv->title, 50) }}</span>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <script>
        function changeIframeInfo(title, v_time, v_url, user_name, user_dash, user_thumb, prod_url) {
            $("#s-video-url").attr('href', v_url);
            $("#s-video-title").text(title);
            $("#sug-vid-com-div").hide();
            if (prod_url == 0) {
                $("#shop-div").hide();
                $("#no-shop-div").show();
                if (user_dash == '0') {
                    $("#s-v-u-dash").off().removeAttr('href');
                    $("#s-video-user-img").attr('src', "{{ asset('files/other/images/profile.png') }}");
                    $("#sug-video-user-span2").text("کاربر مهمان");
                } else {
                    $("#s-v-u-dash").attr('href', user_dash);
                    $("#s-video-user-img").attr('src', user_thumb);
                    $("#sug-video-user-span2").text(user_name);
                }
                $("#sug-video-time-span2").text(v_time);
            } else {
                $("#shop-div").show();
                $("#no-shop-div").hide();
                $("#s-v-pr-link").attr('href', prod_url);
                if (user_dash == '0') {
                    $("#s-v-u-dash").off().removeAttr('href');
                    $("#s-video-user-img").attr('src', "{{ asset('files/other/images/profile.png') }}");
                    $("#sug-video-user-span2").text("کاربر مهمان");
                } else {
                    $("#s-v-pr-u-dash").attr('href', user_dash);
                    $("#s-video-shop-user-img").attr('src', user_thumb);
                    $("#sug-video-user-span").text(user_name);
                    $("#sug-video-time-span").text(v_time);
                }
            }
        }
    </script>

@endif