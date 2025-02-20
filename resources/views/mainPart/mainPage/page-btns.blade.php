@if (isset($category))
    @switch($page)
        @case('comment')
            <button type="button" id="goToCommentForm" class="btn btn-lg btn-primary new-btn-second">
                نظر جدید
                <img src="{{ $ftp_path . 'files/other/images/write-16.png' }}" class="wrcom-img">
            </button>
            <a class="btn btn-dark new-btn-first" target="_blank" rel="nofollow"
                href="{{ isset($category) ? $category->newQuestionUrl($category->slug) : route('question.create') }}">
                سوال +
            </a>
        @break

        @case('show_question')
            <button type="button" id="goToCommentForm" class="btn btn-lg btn-info new-btn-second">
                نظر جدید
                <img src="{{ $ftp_path . 'files/other/images/write-16.png' }}" class="wrcom-img">
            </button>
            <a class="btn btn-dark new-btn-first" target="_blank" rel="nofollow"
                href="{{ isset($category) ? $category->newQuestionUrl($category->slug) : route('question.create') }}">
                سوال جدید +
            </a>
        @break
    @endswitch
@endif
