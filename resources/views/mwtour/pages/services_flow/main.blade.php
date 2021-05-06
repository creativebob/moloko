<div class="grid-x">
	<main class="cell small-12 main-content">
		<h1>{{ $serviceFlow->process->process->name }}</h1>
		<span>{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at) }} дней</span> <span>{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at->subDay()) }} ночей</span>

		<div class="grid-x">
			<div class="cell small-8 tour-main-block">

				<div class="grid-x wrap-gallery">
					<div class="cell small-8">
						<img src="/img/mwtour/tours/1.jpg">
					</div>
					<div class="cell small-4">
						<div class="grid-x">
							<div class="cell small-12">
								<img src="/img/mwtour/tours/2.jpg">
							</div>
							<div class="cell small-12">
								<img src="/img/mwtour/tours/3.jpg">
							</div>
						</div>
					</div>
				</div>

                {!! $serviceFlow->process->process->content !!}

                @if ($serviceFlow->events->isNotEmpty())
				<ul class="events-list">
                    @foreach($serviceFlow->events as $eventFlow)
					<li>
						<h2 class="h2-second">День {{ $loop->index + 1 }}</h2>
						<span class="small-text">{{ $eventFlow->start_at->format('d F, l') }}</span>
                        {!! $eventFlow->process->process->content !!}
					</li>
                    @endforeach
				</ul>
                @endif
			</div>

			<div class="cell small-4 tour-extra-block">
				<div class="grid-x">

					<div class="cell small-12 wrap-extra-info">
				    	<span>{{ num_format($serviceFlow->process->prices->first()->price, 0) }} руб./чел.</span>
				    	<a href="#" class="button">Бронировать</a>
					</div>

                    @if($serviceFlow->process->process->positions->isNotEmpty())
					<div class="cell small-12 wrap-extra-info">
						<h4>Команда тура</h4>
						<ul class="grid-x grid-padding-x small-up-1 align-center" data-equalizer data-equalize-by-row="true">
                            @foreach($serviceFlow->process->process->positions as $position)
                                <li class="cell text-center wrap-staffer" data-equalizer-watch>
                                    <div class="wrap-photo">
                                        <img src="{{ getPhotoPath($position->staff->first()->user) }}"
                                             alt="{{ $position->name ?? '' }}"
                                             width="440"
                                             height="292"
                                        >
                                    </div>

                                    <span class="staffer-name">{{ $position->staff->first()->user->name }}</span>
                                    <span class="staffer-position">{{ $position->name }}</span>
                                </li>
                                @endforeach
						</ul>
					</div>
                    @endif
				</div>
			</div>

	</main>
</div>
