@if ($feedbacks->isNotEmpty())
<section>
	{{-- <h2 class="h2">Наша команда</h2> --}}
    <ul class="grid-x small-up-2 medium-up-3 large-up-3 align-center list-staffers" data-equalizer data-equalize-by-row="true">
        @foreach($feedbacks as $feedback)
        <li class="cell text-center wrap-staffer" data-equalizer-watch>

            <div class="wrap-photo">
                <img
                src="{{ getPhotoPath($feedback) }}"
                alt="{{ $feedback->person ?? '' }}"
                width="128"
                height="128"
                >
            </div>

            <span class="staffer-name">{{ $feedback->person }}</span><br>
            <span class="staffer-position">{{ $feedback->job }}</span>
        </li>
        @endforeach
    </ul>
</section>
@endif

