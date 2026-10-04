@extends('index')

@section('title')
    گفتگو با {{ $user2->username }}
@endsection

@section('style')
    <meta name="robots" content="noindex">

    <link href="{{ asset('assets/css/user-chat/show.css') . '?lm=' . filemtime('assets/css/user-chat/show.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center mb-4">
        <div class="col-12">

            <div class="card" id="chat2">
                <div class="card-header d-flex justify-content-between align-items-center p-2" id="chat-header">
                    <span class="mb-0">
                        <a target="_blank" class="decor-none" href="{{ route('user.dashboard', $user2->username) }}">
                            <img class="chat-user-img" src="{{ asset($user2->thumb()) }}">
                            <span class="chat-user-name">{{ $user2->username }}</span>
                        </a>
                    </span>
                    <a href="{{ route('user.messages') }}" class="decor-none">
                        <img src="{{ asset('files/other/images/next-light.png') }}">
                    </a>
                </div>
                <div class="card-body" id="chat_div" data-mdb-perfect-scrollbar="true">
                    @if ($chat->messages->count() > 0)
                        @foreach ($chat->messages as $m)
                            @if ($m->sender_id == $user->id)
                                <div class="row justify-content-start">
                                    <div class="mr-3">
                                        <p class="this-user-message">
                                            {{ $m->message }}
                                        </p>
                                        <span class="message-time float-right">{{ jdate($m->created_at)->ago() }}
                                        </span>

                                    </div>
                                </div>
                            @else
                                <div class="row justify-content-end">
                                    <div class="ml-2">
                                        <p class="that-user-message">
                                            {{ $m->message }}
                                        </p>
                                        <span class="message-time float-left">
                                            {{ jdate($m->updated_at)->ago() }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="row justify-content-center" id="message-not-exist">
                            <div class="col-10 col-md-8 bg-wht text-center shadow-sm radius-10 p-3">
                                <p id="message-no-msg-title">پیامی وجود ندارد</p>
                                <p>اولین پیام را ارسال کنید</p>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="d-flex justify-content-start p-3" id="chat-footer">
                    <img src="{{ asset($user->image()) }}" class="chat-user-img">

                    <div class="w-100">
                        <div class="form-group">
                            <textarea class="form-control mx-1" name="message" id="user-message-input"
                                placeholder="پیام خود را بنویسید..."></textarea>
                        </div>

                        <button class="w-100 btn btn-sm btn-primary" id="send-message-btn" onclick="sendMessage()"
                            type="button">ارسال
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        const chat_csrf_token = "{{ csrf_token() }}";
        const this_user_id = "{{ $user->id }}";
        const chat_id = "{{ $chat->id }}";
        const user_send_message_route = "{{ route('user.message.send', $chat->id) }}";
    </script>
    <script src="{{ asset('assets/js/user-chat/show.js') . '?lm=' . filemtime('assets/js/user-chat/show.js') }}"></script>
@endsection