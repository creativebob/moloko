@if ($staff->isNotEmpty())
<section>
	<h2 class="h2">Наша команда</h2>
    <ul class="grid-x grid-padding-x small-up-2 medium-up-3 large-up-4 align-center" data-equalizer data-equalize-by-row="true">
        @foreach($staff as $staffer)
        <li class="cell text-center wrap-staffer" data-equalizer-watch>

            <div class="wrap-photo">
                <img
                src="{{ getPhotoPath($staffer->user) }}"
                alt="{{ $staffer->position->name ?? '' }}"
                width="440"
                height="292"
                >
            </div>

            <span class="staffer-name">{{ $staffer->user->name }}</span>
            <span class="staffer-position">{{ $staffer->position->name }}</span>
        </li>
        @endforeach
    </ul>
</section>
@endif

