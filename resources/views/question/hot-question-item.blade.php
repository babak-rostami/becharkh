@if (isset($pin_question) && $pin_question->slug2)
    <a class="hop-item" href="{{ route('question.show', $pin_question->slug2) }}">
        @if ($pin_question->getImage())
            <img class="lazy-load hop-img" data-src="{{ $pin_question->image() }}" alt="{{ $pin_question->title }}">
        @else
            <img class="lazy-load rcir-glow" data-src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
        @endif
        <span class="hop-title">{{ $pin_question->sug_title ?? $pin_question->title }}</span>
        <span class="hop-body">{{ $pin_question->answer }}</span>
    </a>
@endif
