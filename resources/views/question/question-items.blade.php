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
        <a class="p-2 question-box"
            href="{{ route('question.show', ['category' => $question->category->slug, 'slug' => $question->slug, 'random' => $question->random_id]) }}">
            @if ($question->getImage())
                <img class="lazy-load hop-img" data-src="{{ $question->image() }}" alt="{{ $question->title }}">
            @endif

            <h2 class="q-item-title my-2">{{ $question->title }}</h2>

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
