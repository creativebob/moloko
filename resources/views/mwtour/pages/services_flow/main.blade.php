<div class="grid-x">
	<main class="cell small-12 main-content">
		<h1>{{ $serviceFlow->process->process->name }}</h1>
		<span>{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at) }} дней</span> <span>{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at->subDay()) }} ночей</span>

		<div class="grid-x">
			<div class="cell small-12 medium-8 tour-main-block">


				{{-- Фотографии для блока ниже выдергиваем точечно: первая, вторая и третья. Ссылки на остальные будем грузить в следующий блок --}}

				<div class="grid-x wrap-gallery gallery">
					<div class="cell small-8 wrap-one-photo">
						<a data-fancybox="gallery" href="/img/mwtour/tours/1.jpg">
							<img src="/img/mwtour/tours/1.jpg">
						</a>
					</div>
					<div class="cell small-4">
						<div class="grid-x">
							<div class="cell small-12 wrap-second-photo">
								<a data-fancybox="gallery" href="/img/mwtour/tours/2.jpg">
									<img src="/img/mwtour/tours/1.jpg">
								</a>
							</div>
							<div class="cell small-12 wrap-third-photo">
								<a data-fancybox="gallery" href="/img/mwtour/tours/3.jpg">
									<img src="/img/mwtour/tours/1.jpg">
								</a>
							</div>
						</div>
					</div>
				</div>


				{{-- А здесь циклом формируем ссылки на фото начиная с четвертой, если такие есть (Они не отображаються, просмотреть можно только через FancyBox) --}}

				<a data-fancybox="gallery" href="/img/mwtour/tours/2.jpg"></a>
				<a data-fancybox="gallery" href="/img/mwtour/tours/2.jpg"></a>
				<a data-fancybox="gallery" href="/img/mwtour/tours/3.jpg"></a>


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

			<div class="cell small-12 medium-4 tour-extra-block">
				<div class="grid-x">

					<div class="cell small-12 wrap-extra-info">
				    	<span class="price">{{ num_format($serviceFlow->process->prices->first()->price, 0) }}  руб./чел.</span>
				    	<div class="wrap-button-center">
				    		<a href="#" class="button" data-open="modal-call">Бронировать</a>
				    	</div>

				    	@include('mwtour.layouts.headers.includes.modal_call')

				    	<ul class="list-extra-info">
				    		<li>
					    		<span class="h4">Уровень: </span>
					    		<span>{{ $serviceFlow->process->process->metrics }}</span>
					    	</li>
					    	<li>
						    	<h4>В стоимость включено:</h4>
						    	<ul>
						    		<li>Двухразовое питание</li>
									<li>Трансфер</li>
									<li>Проживание</li>
									<li>Снаряжение</li>
						    	</ul>
					    	</li>
					    	<li>
					    		<h4>Проживание:</h4>
					    		<p>Комфортные деревянные домики с холодильником, чайником и мягкими кроватями</p>
					    	</li>
					    	<li>
					    		<h4>Место отправления:</h4>
					    		<span>Иркутск, Автовокзал</span>
					    	</li>
					    	<li>
					    		<h4>Дата и время:</h4>
					    		<span>{{ $serviceFlow->start_at->format('d F') }}, {{ $serviceFlow->start_at->format('H:i') }}</span>
					    	</li>
				    	</ul>

					</div>

                    @if($serviceFlow->process->process->positions->isNotEmpty())
					<div class="cell small-12 wrap-extra-info">
						<h4>Команда тура</h4>
						<ul class="grid-x grid-padding-x small-up-4 align-left" data-equalizer data-equalize-by-row="true">
                            @foreach($serviceFlow->process->process->positions as $position)
                                <li class="cell text-center wrap-staffer" data-equalizer-watch>
                                    <div class="wrap-photo">
                                        <img src="{{ getPhotoPath($position->staff->first()->user) }}"
                                             alt="{{ $position->name ?? '' }}"
                                             width="440"
                                             height="292"
                                        >
                                    </div>

                                    {{-- <span class="staffer-name">{{ $position->staff->first()->user->name }}</span>
                                    <span class="staffer-position">{{ $position->name }}</span> --}}
                                </li>
                                @endforeach
						</ul>
					</div>
                    @endif
                    
				</div>				
			</div>

	</main>
</div>
