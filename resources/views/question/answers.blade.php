@php
    $affnum = 0;
    $pqnum = 0;
    $ans_count = 0;
    $show_sugs = 0;
@endphp
@foreach ($answers as $ans_count => $answer)
    {{-- @if ($ans_count == 0 || $ans_count == 5 || $ans_count == 10)
    @if (isset($pin_questions) && $pin_questions->slice($pqnum, 1)->first() != null)
    <div class="col-12 text-right py-2 px-0 mt-4">
        @include('question.hot-question-item', [
        'pin_question' => $pin_questions->slice($affnum, 1)->first(),
        ])
        @php $pqnum += 1 @endphp
    </div>
    @endif
    @endif --}}
    @if (($ans_count + 1) % 4 == 0)
        @if ($affilates->slice($affnum, 1)->first() != null)
            <div class="col-12 text-right py-2 px-0">
                @include('affilate.show-box', [
                    'affilate' => $affilates->slice($affnum, 1)->first(),
                    'page' => 'show_question',
                    'show_link' => 1,
                ])
                @php $affnum += 1 @endphp
            </div>
        @endif
    @endif
    <div class="row py-3 mx-0 mt-3 answer-box" id="answer-box-{{ $answer->id }}">
        <div class="col-12">
            @include('modals.userdash', [
                'dashuser' => $answer->user,
                'lazyload' => 1,
                'itemid' => $answer->id,
            ])
            <span class="cm-box-time">{{ jdate($answer->created_at)->ago() }}</span>
            @if (isset($answer->editor2))
                <div class="cedshow">
                    {!! $answer->editor2 !!}
                </div>
            @else
                @if (isset($answer->editor))
                    <div class="cedshow">
                        {!! $answer->editor !!}
                    </div>
                @else
                    <p class="ml-2 mt-4 font-18 textarea-preline">{{ $answer->body }}</p>
                @endif
            @endif

            @if ($question->close != 1)
                <button type="button" class="comment-reply-btn"
                    onclick="openAnsReplyModal('replyto','{{ $question->id }}','{{ $answer->id }}')">پاسخ<img class="mr-1"
                        loading="lazy" src="{{ asset('files/other/images/reply.png') }}">
                </button>
            @endif

            <span class="like-icon" onclick="like('{{ $answer->id }}')">
                <span id="like-ans-count-{{ $answer->id }}">{{ $answer->like_count ? $answer->like_count : 0 }}</span>
                <img class="like-ans-image" loading="lazy" id="like-ans-image-{{ $answer->id }}"
                    src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
            </span>
            <span class="dislike-icon" onclick="unlike('{{ $answer->id }}')">
                <span id="unlike-ans-count-{{ $answer->id }}">{{ $answer->unlike_count ? $answer->unlike_count : 0 }}</span>
                <img class="unlike-ans-image" loading="lazy" id="unlike-ans-image-{{ $answer->id }}"
                    src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
            </span>
        </div>
    </div>
    @foreach ($answer->replies as $reply)
        <div class="row py-3 radius-10 mr-3 ml-0 mt-1 reply-box" id="reply-box-{{ $reply->id }}">
            <div class="col-12">
                @include('modals.userdash', [
                    'dashuser' => $reply->user,
                    'lazyload' => 1,
                    'itemid' => $reply->id,
                ])
                @if (isset($reply->reply_name))
                    <span class="rep-name">پاسخ به {{ $reply->reply_name }}</span>
                @endif
                <span class="cm-box-time">{{ jdate($reply->created_at)->ago() }}</span>
                <p class="mt-4 font-18 textarea-preline">{{ $reply->body }}</p>
                @if ($question->close != 1)
                    <button type="button" class="comment-reply-btn"
                        onclick="openAnsReplyModal('replyToRep','{{ $question->id }}','{{ $answer->id }}','{{ $reply->id }}')">پاسخ<img
                            class="mr-1" loading="lazy" src="{{ asset('files/other/images/reply.png') }}">
                    </button>
                @endif
                <span class="like-icon" onclick="like('{{ $reply->id }}')">
                    <span id="like-ans-count-{{ $reply->id }}">{{ $reply->like_count ? $reply->like_count : 0 }}</span>
                    <img class="like-ans-image" loading="lazy" id="like-ans-image-{{ $reply->id }}"
                        src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                </span>
                <span class="dislike-icon" onclick="unlike('{{ $reply->id }}')">
                    <span id="unlike-ans-count-{{ $reply->id }}">{{ $reply->unlike_count ? $reply->unlike_count : 0 }}</span>
                    <img class="unlike-ans-image" loading="lazy" id="unlike-ans-image-{{ $reply->id }}"
                        src="{{ $ftp_path . 'files/other/images/dislike-finger.svg' }}">
                </span>
            </div>
        </div>
    @endforeach
@endforeach
@include('modals.question.reply')

@while ($affnum != -1 && $pqnum != -1)
    @if ($affnum != -1 && isset($affilates) && $affilates->slice($affnum, 1)->first() != null)
        <div class="col-12 text-right py-2 px-0">
            @include('affilate.show-box', [
                'affilate' => $affilates->slice($affnum, 1)->first(),
                'page' => 'show_question',
                'show_link' => 1,
            ])
            @php $affnum += 1 @endphp
        </div>
    @else
        @php $affnum = -1 @endphp
    @endif
    {{-- @if ($pqnum != -1 && isset($pin_questions) && $pin_questions->slice($pqnum, 1)->first() != null)
    <div class="col-12 text-right py-2 px-0 mt-4">
        @include('question.hot-question-item', [
        'pin_question' => $pin_questions->slice($affnum, 1)->first(),
        ])
        @php $pqnum += 1 @endphp
    </div>
    @else
    @php $pqnum = -1 @endphp
    @endif --}}
@endwhile