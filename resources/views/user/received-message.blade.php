@extends('index')


@section('title')
    پیام های {{ auth('user')->user()->username }}
@endsection

@section('style')
    <meta name="robots" content="noindex">
    <link href="{{ asset('assets/css/user-chat/index.css') . '?lm=' . filemtime('assets/css/user-chat/index.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <div class="row justify-content-center">

        <div class="col-12 bg-wht radius-10 p-2 text-right pb-5" id="chat-box">


            <div class="mb-3 mt-2 pr-3">
                <a class="bread-item" href="{{ route('home') }}">خانه</a>
                <a class="bread-item" href="{{ route('user.dashboard.edit') }}">مدیریت حساب</a>
            </div>

            @if ($chats->count() > 0)
                @foreach ($chats as $key => $chat)
                    <a class="chat-item" href="{{ route('user.show.message', $chat->id) }}">
                        <div class="row chat-row p-3 radius-10">
                            <div class="col-12">
                                <img class="chat-item-img lazy-load" alt="{{ $chat->user2->username }} profile"
                                    data-src="{{ asset($chat->user2->image()) }}">
                                <span class="chat-item-uname">{{ $chat->user2->username }}</span>
                                <span class="chat-item-time">{{ jdate($chat->updated_at)->ago() }}</span>
                            </div>
                            <div class="col-12 text-center" dir="rtl">
                                @if (isset($chat->last_messages))
                                    <span
                                        class="chat-item-body">{{ Str::limit($chat->last_messages[count($chat->last_messages) - 1]['message'], 100, '...') }}</span>
                                @endif
                            </div>

                        </div>
                    </a>
                @endforeach
            @else
                <p class="bg-wht p-4" id="nochat">صندوق پیام شما خالی می باشد</p>
            @endif
        </div>
    </div>
@endsection
