@if ($staff->isNotEmpty())
<section>
	{{-- <h2 class="h2">Наша команда</h2> --}}
    <ul class="grid-x small-up-3 medium-up-3 large-up-3 align-center list-staffers" data-equalizer data-equalize-by-row="true">
        @foreach($staff as $staffer)
        <li class="cell text-center wrap-staffer" data-equalizer-watch>

            <div class="wrap-photo">
                <img
                src="{{ getPhotoPath($staffer->user) }}"
                alt="{{ $staffer->position->name ?? '' }}"
                width="128"
                height="128"
                >
            </div>

            <span class="staffer-name">{{ $staffer->user->name }}</span><br>
            <span class="staffer-position">{{ $staffer->position->name }}</span>
        </li>
        @endforeach
    </ul>
</section>
@endif

