@foreach ($hotQuestions as $hq)
    <div class="col-12 s-q-item text-right px-3 py-2 mb-3 radius-10">
        <a @if ($hq->google_index == 0) rel="nofollow" @endif
            href="{{ route('question.show', ['category' => $hq->category->slug, 'slug' => $hq->slug, 'random' => $hq->random_id]) }}"
            class="text-decoration-none text-dark">
            <div>
                <img class="sug-q-img lazy-load" data-src="{{ asset($hq->user->thumb()) }}" alt="user image">
                <span class="sug-q-username">{{ $hq->user->username }}</span>

                @if ($hq->like_count > 0)
                    <span class="float-left">
                        <span>{{ $hq->like_count }}</span>
                        <img class="sug-q-like-icon lazy-load"
                            data-src="{{ $ftp_path . 'files/other/images/like-finger.svg' }}">
                    </span>
                @endif
            </div>
            <h2 class="sug-q-title my-3">{{ $hq->title }}</h2>
            @if ($hq->answer)
                <span class="sug-q-c-shortans">-{{ $hq->answer }}
                </span>
            @endif
            @if (isset($hq->items_title))
                <div>
                    @foreach ($hq->items_title as $title)
                        <span class="badge badge-light">{{ $title }}</span>
                    @endforeach
                </div>
            @endif
        </a>
    </div>
@endforeach
<div class="col-12 text-center my-4 px-0 py-3">
    <h3>شما هم سوالی دارید؟</h3>
    <a class="btn btn-danger w-100 my-4" rel="nofollow" href="{{ route('question.create') }}">
        ثبت سوال در انجمن
        <img src="{{ $ftp_path . 'files/other/images/w-add.png' }}">
    </a>
</div>
