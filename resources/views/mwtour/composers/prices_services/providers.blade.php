@if ($providers->isNotEmpty())
    <ul class="grid-x grid-padding-x small-up-1 align-center" data-equalizer data-equalize-by-row="true">
        @foreach($providers as $provider)
            <li class="cell text-center wrap-staffer" data-equalizer-watch>

                <div class="wrap-photo">
                    <img src="{{ getPhotoPath($provider->user) }}"
                        alt="{{ $provider->position->name ?? '' }}"
                         width="440"
                         height="292"
                    >
                </div>

                <span class="staffer-name">{{ $provider->user->name }}</span>
                <span class="staffer-position">{{ $provider->position->name }}</span>
            </li>
        @endforeach
    </ul>
@endif
