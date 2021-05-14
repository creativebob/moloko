<div class="grid-x">

    <main class="cell small-12 main-content">

        <nav aria-label="Вы здесь:" role="navigation">
            <ul class="breadcrumbs">
                <li><a href="/">Наши туры</a></li>
                <li class="gray-dark">
                    <span class="show-for-sr">Current: </span> {{ $serviceFlow->process->process->name }}
                </li>
            </ul>
        </nav>

        <h1>{{ $serviceFlow->process->process->name }}</h1>
        <div class="wrap-count-day">
            <span>{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at) + 1 }} дней</span> | <span>{{ $serviceFlow->start_at->diffInDays($serviceFlow->finish_at) }} ночей</span>
        </div>
        <div class="grid-x">
            <div class="cell small-12 medium-auto tour-main-block">

                {{-- Фотографии для блока ниже выдергиваем точечно: первая, вторая и третья. Ссылки на остальные будем грузить в следующий блок --}}
                @isset($serviceFlow->process->process->album)
                    <div class="grid-x wrap-gallery gallery">

                        <div class="cell small-12 medium-8 wrap-one-photo">
                            @if($serviceFlow->process->process->album->photos->get(0))
                                <a data-fancybox="gallery"
                                   href="{{ getPhotoInAlbumPath($serviceFlow->process->process->album->photos->get(0), 'large') }}">
                                    <img
                                        src="{{ getPhotoInAlbumPath($serviceFlow->process->process->album->photos->get(0), 'large') }}">
                                </a>
                                @if($serviceFlow->process->process->album->photos->count() > 3)
                                    <div class="wrap-count-photo">
                                        <a data-fancybox="gallery"
                                           href="{{ getPhotoInAlbumPath($serviceFlow->process->process->album->photos->get(0), 'large') }}">
                                            <span>{{ $serviceFlow->process->process->album->photos->count() }} фото</span>
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="cell small-12 medium-4">
                            <div class="grid-x">

                                <div class="cell small-6 medium-12 wrap-second-photo">
                                    @if($serviceFlow->process->process->album->photos->get(1))
                                        <a data-fancybox="gallery"
                                           href="{{ getPhotoInAlbumPath($serviceFlow->process->process->album->photos->get(1), 'large') }}">
                                            <img
                                                src="{{ getPhotoInAlbumPath($serviceFlow->process->process->album->photos->get(1)) }}">
                                        </a>
                                    @endif
                                </div>

                                <div class="cell small-6 medium-12 wrap-third-photo">
                                    @if($serviceFlow->process->process->album->photos->get(2))
                                        <a data-fancybox="gallery"
                                           href="{{ getPhotoInAlbumPath($serviceFlow->process->process->album->photos->get(2), 'large') }}">
                                            <img
                                                src="{{ getPhotoInAlbumPath($serviceFlow->process->process->album->photos->get(2)) }}">
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- А здесь циклом формируем ссылки на фото начиная с четвертой, если такие есть (Они не отображаються, просмотреть можно только через FancyBox) --}}

                            @foreach($serviceFlow->process->process->album->photos as $photo)
                                @if($loop->iteration > 3)
                                    <a data-fancybox="gallery" href="{{ getPhotoInAlbumPath($photo) }}"></a>
                                @endif
                            @endforeach
                        </div>

                    </div>
                @endisset

				<div class="grid-x">
					<div class="cell small-12 wrap-text-content">

		                {!! $serviceFlow->process->process->content !!}
		            </div>

            		<h2 class="h2-tour">Программа тура:</h2>
	                @if ($serviceFlow->events->isNotEmpty())
						<ul class="accordion events-list" data-accordion data-allow-all-closed="true">
		                    @foreach($serviceFlow->events as $eventFlow)
							<li class="accordion-item" data-accordion-item>
								<a href="#" class="accordion-title">
									<h2 class="h2-second">День {{ $loop->index + 1 }}</h2>
									<span class="small-text">{{ $eventFlow->start_at->translatedFormat('j F, l') }}</span>
									<p>{{ $eventFlow->process->process->description }}</p>
								</a>
								<div class="accordion-content" data-tab-content>
		                        	{!! $eventFlow->process->process->content !!}

		                        	@if(isset($eventFlow->process->process->photo))
		                                <div class="wrap-event-photo">
		                       				<img src="{{ getPhotoPathPlugEntity($eventFlow->process) }}" alt="{{ $eventFlow->process->process->name }}" 
		                       				title="" @if(isset($eventFlow->process->process->photo)) width="530" height="246" @endif class="service_photo">
		                                </div>
                                    @endif

		                        </div>
							</li>
		                    @endforeach
						</ul>
	                @endif
		        </div>

		        @if($serviceFlow->process->process->video)
		            <div class="cell small-12 wrap-video-block">
		                <div class="video-wrap">
		                    {!! $serviceFlow->process->process->video !!}
		                </div>
		            </div>
		        @endif

                <h2 class="h2-tour">Список рекомендованной одежды и снаряжения:</h2>
				<ul class="accordion gear" data-accordion data-allow-all-closed="true">
				  <li class="accordion-item" data-accordion-item>
				    <a href="#" class="accordion-title">Одежда</a>
				    <div class="accordion-content" data-tab-content>
				    	<ul>
				    		<li>Куртка-ветровка непромокаемая (штормовка, с капюшоном) – 1 шт</li>
							<li>Куртка теплая (можно легкую пуховку или сноубордическую) - 1 шт</li>
							<li>Плащ-дождевик – 1 шт</li>
							<li>Свитер и\или кофта флисовая – 1 шт</li>
							<li>Футболки – 3 шт</li>
							<li>Термобелье - 1 комплект</li>
							<li>Брюки из ветрозащитной ткани – 1 шт</li>
							<li>Брюки повседневные – 1-2 шт</li>
							<li>Шорты – 1 шт</li>
							<li>Носки тонкие – 3 пары</li>
							<li>Носки плотные треккинговые для восхождений – 1 пара</li>
							<li>Летний головной убор (панама, кепка, бейсболка, бандана) – 1 шт</li>
							<li>Купальный костюм – 1 шт</li>
				    	</ul>
				    </div>
				  </li>
				  <li class="accordion-item" data-accordion-item>
				    <a href="#" class="accordion-title">Обувь</a>
				    <div class="accordion-content" data-tab-content>
				    	<ul>
				    		<li>Треккинговые ботинки</li>
							<li>Дополнительная пара обуви для прогулки в лагере</li>
							<li>Обувь для душа (сланцы)</li>

				    	</ul>
				    </div>
				  </li>
				  <li class="accordion-item" data-accordion-item>
				    <a href="#" class="accordion-title">Личные вещи</a>
				    <div class="accordion-content" data-tab-content>
				    	<ul>
						<li>Паспорт</li>
						<li>Деньги</li>
						<li>Термос - очень пригодится</li>
						<li>Индивидуальная аптечка</li>
						<li>Солнцезащитный крем</li>
						<li>Предметы личной гигиены</li>
						<li>Банное</li>
						<li>Очки солнцезащитные</li>

				    	</ul>
				    </div>
				  </li>
				  <li class="accordion-item" data-accordion-item>
				    <a href="#" class="accordion-title">Снаряжение</a>
				    <div class="accordion-content" data-tab-content>
				    	<ul>
						<li>Рюкзак штурмовой объёмом 35-45 литров для спортивных занятий и радиальных выходов</li>
						<li>Хоба (по-другому сидушка туриста)</li>
						<li>Крем от солнца (фактор защиты SPF не менее 45)</li>
						<li>Термос или бутылочка для воды (желательно) объёмом 0,5-0,7 литра</li>

				    	</ul>
				    </div>
				  </li>
				</ul>

			</div>

			<div class="cell small-12 medium-shrink tour-extra-block">
				<div class="grid-x wrap-ei">
					<div class="cell small-12 wrap-extra-info">

						<div class="grid-x">
							<div class="cell small-12" id="select-tour">
								@include('mwtour.pages.services_flow.includes.price')
							</div>
						</div>

						@include('mwtour.layouts.headers.includes.modal_call', ['flowId' => $serviceFlow->id])

				    	<ul class="list-extra-info">
                            @if($serviceFlow->process->metrics->isNotEmpty())
                                @if($serviceFlow->process->metrics->firstWhere('alias', 'level'))
                                    <li>
                                        <span class="h4">{{ $serviceFlow->process->metrics->firstWhere('alias', 'level')->name }}: </span>
                                        <span class="">{{ $serviceFlow->process->metrics->firstWhere('alias', 'level')->values->firstWhere('id', $serviceFlow->process->metrics->firstWhere('alias', 'level')->pivot->value)->value }}</span>
                                    </li>
                                @endif
                            @endif
					    	<li>
					    		<h4>Дата и время:</h4>
					    		<span><span class="icon icon-clock"></span>{{ $serviceFlow->start_at->translatedFormat('j F') }}, {{ $serviceFlow->start_at->format('H:i') }}</span>
					    	</li>
					    	<li>
					    		<h4>Место отправления:</h4>
					    		<span><span class="icon icon-geopoint"></span>
					    		{{ $serviceFlow->location->city->name}}@if($serviceFlow->location->address), {{ $serviceFlow->location->address }}@endif</span>
					    	</li>				    	
					    	<li>
					    		<h4>Проживание:</h4>
					    		<p>На протяжении всей программы ночуем на туристических базах. Размещение в 3-х, 6-ти местных номерах. Тёплый душ, туалет на территории.</p>
					    	</li>
				    	</ul>

					</div>

                    @if($serviceFlow->staff->isNotEmpty())
                        <div class="cell small-12 wrap-extra-info">

                            <h2 class="h2-tour">Команда тура</h2>
                            {{-- <h3>Организаторы:</h3> --}}
                            <ul class="grid-x grid-padding-x small-up-3 align-left" data-equalizer data-equalize-by-row="true">
                                @foreach($serviceFlow->staff as $staffer)
                                    <li class="cell text-center wrap-staffer" data-equalizer-watch>
                                        <div class="wrap-photo">
                                            <img src="{{ getPhotoPath($staffer->user) }}"
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

                            {{-- @if($serviceFlow->clients->count() >= $serviceFlow->capacity_min) --}}
		                        <hr>
		                        <div class="participants">
		                        	<h3>Участники:</h3>
		                        	<span class="wrap-indicator">
		                            	<span class="fact-participants">{{ $serviceFlow->clients->count() }}</span>
		                            	<span> / </span>
		                            	<span class="max-participants">{{ $serviceFlow->capacity_max }}</span>
		                            </span>
		                        </div>
		                    {{-- @endif --}}

		                    <div class="wrap-recruiting">

		                    	@if($serviceFlow->clients->count() < $serviceFlow->capacity_max)
		                    		<span class="recruiting-status active">Идет набор...</span>
		                    	@else
		                    		<span class="recruiting-status finish">Набор завершён</span>
		                    	@endif
	                       		<img src="{{ getPhotoPathPlugEntity($serviceFlow->process) }}" alt="{{ $serviceFlow->process->process->name }}" 
	                       		title="" @if(isset($serviceFlow->process->process->photo)) width="530" height="246" @endif class="service_photo">
		                	</div>

                        </div>
                    @endif

                        <div class="cell small-12 wrap-extra-info wrap-price-include">

                            <h2 class="h2-tour">Что включено в стоимость?</h2>
							<ul class="tabs" data-tabs id="example-tabs">
							  <li class="tabs-title is-active"><a href="#panel1" aria-selected="true">Включено</a></li>
							  <li class="tabs-title"><a data-tabs-target="panel2" href="#panel2">Не включено</a></li>
							</ul>

							<div class="tabs-content" data-tabs-content="example-tabs">
								<div class="tabs-panel is-active" id="panel1">
									<ul>
										<li>Трансфер по всем локациям программы</li>
										<li>Прокат снаряжения</li>
										<li>Билеты на посещение национального парка</li>
										<li>Аренда sup-бордов/байдарок или катамаранов</li>
										<li>Питание в кафе во время дороги туда и обратно</li>
										<li>Проживание по программе в местах ночлега</li>
										<li>Медицинская аптечка – групповая</li>
										<li>Трехразовое питание</li>
										<li>Экскурсия на катере</li>
										<li>Баня по-байкальски</li>
										<li>Работа гида – организатора</li>
										<li>Организаторские сборы и сопровождение 24/7</li>
									</ul>
								</div>
								<div class="tabs-panel" id="panel2">
									<ul>
										<li>Трансфер до Иркутска и обратно</li>
										<li>Проживание в Иркутске в гостиницах или хостеле, но мы модем помочь вам с размещением и выборов гостиницы</li>
										<li>Катание на квадроциклах и велосипедах</li>
									</ul>
								</div>
							</div>
                        </div>

                </div>
            </div>
        </div>

        <div class="grid-x show-for-small-only">
            <div class="cell small-12">
                @include('mwtour.pages.services_flow.includes.price')
            </div>
        </div>

        {{-- <section class="grid-x grid-padding-x align-center">
            <div class="cell small-12 large-8">
                @include('mwtour.pages.common.forms.question', ['msg' => "У клиента вопрос!\r\n"])
            </div>
        </section> --}}
    </main>
</div>
