@if (isset($comment_page) || isset($forum_page) || isset($blog_page) || isset($advertise_page))
    <div id="tabs" class="pt-1">
        @if (isset($item))
            <div id="tab-title-box">
                <span id="tab-title-box-text">{{ $item->full_title ?? $item->title }}</span>
            </div>
        @endif
        <div id="tab-actions">
            {{-- @if (isset($item))
            @if (isset($user))
                @if ($is_follow == 1)
                    <button type="button" class="btn-nfollow-item followi-btn-{{ $item->id }}"
                        onclick="followItem('{{ $item->id }}')">دنبال شده</button>
                @else
                    <button type="button" class="btn-follow-item followi-btn-{{ $item->id }}"
                        onclick="followItem('{{ $item->id }}')">دنبال کردن</button>
                @endif
            @else
                <button type="button" class="btn-follow-item" data-toggle="modal" data-target="#login_user"
                    onclick="setActionForAfterAuth(null, null)">دنبال کردن</button>
            @endif
        @endif --}}

            @switch($page)
                @case('comment')
                    <span class="active-tab">نظرات کاربران</span>
                    @if (isset($forum_page))
                        <a href="{{ $forum_page }}" class="not-active-tab">سوال ها
                            @if (isset($item) && isset($item->question_count))
                                <span>{{ $item->question_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($advertise_page))
                        <a href="{{ $advertise_page }}" class="not-active-tab">آگهی
                            @if (isset($item) && isset($item->advertise_count))
                                <span>{{ $item->advertise_count }}</span>
                            @endif
                        </a>
                    @endif
                    {{-- @if (isset($blog_page))
                    <a href="{{ $blog_page }}" class="not-active-tab">آموزشی
                        @if (isset($item) && isset($item->blog_count))
                            <span>{{ $item->blog_count }}</span>
                        @endif
                    </a>
                @endif --}}
                @break

                @case('forum')
                    <span class="active-tab">سوال ها</span>
                    @if (isset($comment_page))
                        <a href="{{ $comment_page }}" class="not-active-tab">نظرات کاربران
                            @if (isset($item) && isset($item->comment_count))
                                <span>{{ $item->comment_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($advertise_page))
                        <a href="{{ $advertise_page }}" class="not-active-tab">آگهی
                            @if (isset($item) && isset($item->advertise_count))
                                <span>{{ $item->advertise_count }}</span>
                            @endif
                        </a>
                    @endif
                    {{-- @if (isset($blog_page))
                    <a href="{{ $blog_page }}" class="not-active-tab">آموزشی
                        @if (isset($item) && isset($item->blog_count))
                            <span>{{ $item->blog_count }}</span>
                        @endif
                    </a>
                @endif --}}
                @break

                @case('advertise')
                    <span class="active-tab">آگهی</span>
                    @if (isset($comment_page))
                        <a href="{{ $comment_page }}" class="not-active-tab">نظرات کاربران
                            @if (isset($item) && isset($item->comment_count))
                                <span>{{ $item->comment_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($forum_page))
                        <a href="{{ $forum_page }}" class="not-active-tab">سوال ها
                            @if (isset($item) && isset($item->question_count))
                                <span>{{ $item->question_count }}</span>
                            @endif
                        </a>
                    @endif
                    {{-- @if (isset($blog_page))
                    <a href="{{ $blog_page }}" class="not-active-tab">آموزشی
                        @if (isset($item) && isset($item->blog_count))
                            <span>{{ $item->blog_count }}</span>
                        @endif
                    </a>
                @endif --}}
                @break

                @case('blog-index')
                    <span class="active-tab">آموزشی</span>
                    @if (isset($comment_page))
                        <a href="{{ $comment_page }}" class="not-active-tab">نظرات کاربران
                            @if (isset($item) && isset($item->comment_count))
                                <span>{{ $item->comment_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($forum_page))
                        <a href="{{ $forum_page }}" class="not-active-tab">سوال ها
                            @if (isset($item) && isset($item->question_count))
                                <span>{{ $item->question_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($advertise_page))
                        <a href="{{ $advertise_page }}" class="not-active-tab">آگهی
                            @if (isset($item) && isset($item->advertise_count))
                                <span>{{ $item->advertise_count }}</span>
                            @endif
                        </a>
                    @endif
                @break

                @case('show_blog')
                    {{-- @if (isset($blog_page))
                    <a href="{{ $blog_page }}" class="active-tab">آموزشی
                        @if (isset($item) && isset($item->blog_count))
                            <span>{{ $item->blog_count }}</span>
                        @endif
                    </a>
                @else
                    <span class="active-tab">آموزشی</span>
                @endif --}}
                    @if (isset($comment_page))
                        <a href="{{ $comment_page }}" class="not-active-tab">نظرات کاربران
                            @if (isset($item) && isset($item->comment_count))
                                <span>{{ $item->comment_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($forum_page))
                        <a href="{{ $forum_page }}" class="not-active-tab">سوال ها
                            @if (isset($item) && isset($item->question_count))
                                <span>{{ $item->question_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($advertise_page))
                        <a href="{{ $advertise_page }}" class="not-active-tab">آگهی
                            @if (isset($item) && isset($item->advertise_count))
                                <span>{{ $item->advertise_count }}</span>
                            @endif
                        </a>
                    @endif
                @break

                @case('show_question')
                    @if (isset($forum_page))
                        <a href="{{ $forum_page }}" class="active-tab">سوال ها
                            @if (isset($item) && isset($item->question_count))
                                <span>{{ $item->question_count }}</span>
                            @endif
                        </a>
                    @else
                        <span class="active-tab">سوال ها</span>
                    @endif
                    @if (isset($comment_page))
                        <a href="{{ $comment_page }}" class="not-active-tab">نظرات کاربران
                            @if (isset($item) && isset($item->comment_count))
                                <span>{{ $item->comment_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($advertise_page))
                        <a href="{{ $advertise_page }}" class="not-active-tab">آگهی
                            @if (isset($item) && isset($item->advertise_count))
                                <span>{{ $item->advertise_count }}</span>
                            @endif
                        </a>
                    @endif
                    {{-- @if (isset($blog_page))
                    <a href="{{ $blog_page }}" class="not-active-tab">آموزشی
                        @if (isset($item) && isset($item->blog_count))
                            <span>{{ $item->blog_count }}</span>
                        @endif
                    </a>
                @endif --}}
                @break

                @case('show_advertise')
                    @if (isset($advertise_page))
                        <a href="{{ $advertise_page }}" class="active-tab">آگهی
                            @if (isset($item) && isset($item->advertise_count))
                                <span>{{ $item->advertise_count }}</span>
                            @endif
                        </a>
                    @else
                        <span class="active-tab">آگهی</span>
                    @endif
                    @if (isset($comment_page))
                        <a href="{{ $comment_page }}" class="not-active-tab">نظرات کاربران
                            @if (isset($item) && isset($item->comment_count))
                                <span>{{ $item->comment_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($forum_page))
                        <a href="{{ $forum_page }}" class="not-active-tab">سوال ها
                            @if (isset($item) && isset($item->question_count))
                                <span>{{ $item->question_count }}</span>
                            @endif
                        </a>
                    @endif
                    {{-- @if (isset($blog_page))
                    <a href="{{ $blog_page }}" class="not-active-tab">آموزشی
                        @if (isset($item) && isset($item->blog_count))
                            <span>{{ $item->blog_count }}</span>
                        @endif
                    </a>
                @endif --}}
                @break

                @case('show_product')
                    @if (isset($comment_page) || isset($forum_page) || isset($blog_page))
                        @if (isset($advertise_page))
                            <a href="{{ $advertise_page }}" class="active-tab">آگهی
                                @if (isset($item) && isset($item->advertise_count))
                                    <span>{{ $item->advertise_count }}</span>
                                @endif
                            </a>
                        @else
                            <span class="active-tab">آگهی</span>
                        @endif
                    @endif
                    @if (isset($comment_page))
                        <a href="{{ $comment_page }}" class="not-active-tab">نظرات کاربران
                            @if (isset($item) && isset($item->comment_count))
                                <span>{{ $item->comment_count }}</span>
                            @endif
                        </a>
                    @endif
                    @if (isset($forum_page))
                        <a href="{{ $forum_page }}" class="not-active-tab">سوال ها
                            @if (isset($item) && isset($item->question_count))
                                <span>{{ $item->question_count }}</span>
                            @endif
                        </a>
                    @endif
                    {{-- @if (isset($blog_page))
                    <a href="{{ $blog_page }}" class="not-active-tab">آموزشی
                        @if (isset($item) && isset($item->blog_count))
                            <span>{{ $item->blog_count }}</span>
                        @endif
                    </a>
                @endif --}}
                @break

            @endswitch
        </div>
    </div>
@endif
