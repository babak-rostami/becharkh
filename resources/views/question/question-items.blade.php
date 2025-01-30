<?php $count = 0; ?>
@foreach ($questions as $question)
    <?php $count += 1; ?>
    @if ($count == 3)
        @if (isset($affilate))
            <div class="col-12 text-right py-2 mb-3">
                @include('affilate.show-box')
            </div>
        @endif
    @endif
    <div class="col-12 text-right my-2">
        <a class="px-3 py-2 question-box"
            href="{{ route('question.show', ['category' => $question->category->slug, 'slug' => $question->slug, 'random' => $question->random_id]) }}">
            <div>
                <img class="q-user-image lazy-load" data-src="{{ asset($question->user->thumb()) }}" alt="User Image" />
                <span class="question-username">{{ $question->user->username }}</span>

                @if ($question->like_count > 0)
                    <span class="like-icon float-left">
                        <span>{{ $question->like_count }}</span>
                        <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                    </span>
                @endif
            </div>

            <h2 class="q-item-title my-4">{{ $question->title }}</h2>

            @if ($question->answer)
                <span class="c-shortans">-{{ $question->answer }}
                </span>
            @endif

            @if (isset($question->items_title))
                <div>
                    @foreach ($question->items_title as $title)
                        <span class="badge badge-light">{{ $title }}</span>
                    @endforeach
                </div>
            @endif
        </a>
    </div>
@endforeach
@if ($count < 3)
    @if (isset($affilate))
        <div class="col-12 text-right py-2 mb-3">
            @include('affilate.show-box')
        </div>
    @endif
@endif
