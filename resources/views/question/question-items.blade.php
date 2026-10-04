@php
    $affnum = 0;
    $count = 0;
@endphp
@foreach ($questions as $count => $question)
    @if (isset($affilates) && $count != 0 && $count % 4 == 0)
        @if ($affilates->slice($affnum, 1)->first() != null)
            <div class="col-12 text-right py-2 px-0 mt-3">
                @include('affilate.show-box', [
                    'affilate' => $affilates->slice($affnum, 1)->first(),
                    'page' => 'comment',
                    'show_link' => 1,
                ])
                @php $affnum += 1 @endphp
            </div>
        @endif
    @endif
    <div class="col-12 col-md-6 text-right my-2">
        <a class="p-2 hop-item" href="{{ route('question.show', $question->slug2) }}">
            @if ($question->getImage())
                <img class="hop-img" loading="lazy" src="{{ $question->image() }}" alt="{{ $question->title }}">
            @endif

            <h2 class="hop-title my-2">{{ $question->sug_title ?? $question->title }}</h2>

            @if ($question->answer)
                <span class="hop-body">{{ $question->answer }}
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
@while ($affnum != -1)
    @if ($affnum != -1 && isset($affilates) && $affilates->slice($affnum, 1)->first() != null)
        <div class="col-12 text-right py-2 px-0 mt-3">
            @include('affilate.show-box', [
                'affilate' => $affilates->slice($affnum, 1)->first(),
                'page' => 'comment',
                'show_link' => 1,
            ])
            @php $affnum += 1 @endphp
        </div>
    @else
        @php $affnum = -1 @endphp
    @endif
@endwhile