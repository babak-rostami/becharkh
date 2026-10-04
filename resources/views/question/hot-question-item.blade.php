@if (isset($pin_question) && $pin_question->slug2)
    <a class="hop-item" href="{{ route('question.show', $pin_question->slug2) }}">
        @if ($pin_question->getImage())
            <img class="hop-img" loading="lazy" src="{{ $pin_question->image() }}" alt="{{ $pin_question->title }}">
        @else
            <img class="rcir-glow" loading="lazy" src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
        @endif
        <span class="hop-title">{{ $pin_question->sug_title ?? $pin_question->title }}</span>
        <span class="hop-body">{{ $pin_question->answer }}</span>
    </a>
@endif