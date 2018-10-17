@php 
	$last_challenges_count = 0;
	$today_challenges_count = 0;
	$tomorrow_challenges_count = 0;

	$last_challenges_count_from = 0;
	$today_challenges_count_from = 0;
	$tomorrow_challenges_count_from = 0;
@endphp


<!-- Задачи мне -->
<div class="tabs-panel is-active" id="task-panel2">

	<div class="grid-x tabs-wrap">
		<div class="small-12 cell">
			<ul class="tabs-period-task" data-tabs id="tabs-period-forme">
				<li class="tabs-title">
					<span class="tab-challanges-count" id="last-challenges-count"></span>
					<a href="#last" id="link_last">Прошлые</a>
				</li>
				<li class="tabs-title is-active">
					<span class="tab-challanges-count" id="today-challenges-count"></span>
					<a href="#today" id="link_today" aria-selected="true">Сегодня</a>
				</li>
				<li class="tabs-title">
					<span class="tab-challanges-count" id="tomorrow-challenges-count"></span>
					<a href="#tomorrow" id="link_tomorrow">Будущие</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="tabs-content" data-tabs-content="tabs-period-forme">

		<div class="tabs-panel" id="last">
			<ul class="for_scroll my-task">

				{{-- Запускаем если пришли задачи --}}
				@if(!empty($list_challenges['for_me']))

					{{-- Перебераем по дням  --}}
					@foreach($list_challenges['for_me'] as $date => $challenge_date)

						{{-- Если есть дата  --}}
						@if(!empty($challenge_date))

							{{-- Если дата прошедших дней --}}
							@if(Carbon\Carbon::createFromFormat('d.m.Y', $date) < Carbon\Carbon::today())

								<li class="challenge_date">
									<div class="task-head">
										<div class="sprite-16 icon-task"></div>
										<div class="task-date">
											@if($date == Carbon\Carbon::now()->format('d.m.Y'))
												Сегодня: {{ $date }}
											@elseif($date == Carbon\Carbon::now()->addDays(1)->format('d.m.Y'))
												Завтра: {{ $date }}
											@elseif($date == Carbon\Carbon::now()->addDays(-1)->format('d.m.Y'))
												Вчерашние: {{ $date }}
											@else
												{{ $date }}
											@endif
										</div>
										<div class="task-count">{{ $challenge_date->count() }}</div>
										<hr/>
									</div>

									{{-- Если есть задача в этой дате  --}}
									@if(!empty($challenge_date))
										@foreach($challenge_date as $challenge)

											{{-- Считаем количество задач --}}
											@php $last_challenges_count = $last_challenges_count + 1; @endphp

											<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
												<p class="task-target">{{ $challenge->description or ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
													<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
													<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
													<li><span class="task-data">Товар: </span>
													{{-- 
														{{ $challenge->challenges->choices_goods_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_services_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_raws_categories->implode('name', ', ') }}</li>
													--}}
													<li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li>
												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>

										@endforeach								
									@endif
								</li>

							@endif

						@endif
					@endforeach
				@endif
			</ul>
			<input name="last_challenges_count" type="hidden" value="{{ $last_challenges_count }}">								
		</div>

		<div class="tabs-panel is-active" id="today">
			<ul class="for_scroll my-task">
				{{-- Запускаем если пришли задачи --}}
				@if(!empty($list_challenges['for_me']))

					{{-- Перебераем по дням  --}}
					@foreach($list_challenges['for_me'] as $date => $challenge_date)

						{{-- Если есть дата  --}}
						@if(!empty($challenge_date))

							{{-- Если дата сегодняшняя --}}
							@if($date == Carbon\Carbon::now()->format('d.m.Y'))

								<li class="challenge_date">
									<div class="task-head">
										<div class="sprite-16 icon-task"></div>
										<div class="task-date">
										@if($date == Carbon\Carbon::now()->format('d.m.Y'))
											Сегодня: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(1)->format('d.m.Y'))
											Завтра: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(-1)->format('d.m.Y'))
											Вчерашние: {{ $date }}
										@else
											{{ $date }}
										@endif

										</div>
										<div class="task-count">{{ $challenge_date->count() }}</div>
										<hr />
									</div>

									{{-- Если есть задача в этой дате  --}}
									@if(!empty($challenge_date))
										@foreach($challenge_date as $challenge)

											{{-- Считаем количество задач --}}
											@php $today_challenges_count = $today_challenges_count + 1; @endphp

											<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
												<p class="task-target">{{ $challenge->description or ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
													<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
													<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
													<li><span class="task-data">Товар: </span>
													{{-- 
														{{ $challenge->challenges->choices_goods_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_services_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_raws_categories->implode('name', ', ') }}</li>
													--}}
													<li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li>
												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>

										@endforeach
									@endif
								</li>

							@endif

						@endif
					@endforeach
				@endif
			</ul>
			<input name="today_challenges_count" type="hidden" value="{{ $today_challenges_count }}">				
		</div>

		<div class="tabs-panel" id="tomorrow">
			<ul class="for_scroll my-task">
				{{-- Запускаем если пришли задачи --}}
				@if(!empty($list_challenges['for_me']))

					{{-- Перебераем по дням  --}}
					@foreach($list_challenges['for_me'] as $date => $challenge_date)

						{{-- Если есть дата  --}}
						@if(!empty($challenge_date))

							{{-- Если дата будущая --}}
							@if(Carbon\Carbon::createFromFormat('d.m.Y', $date) > Carbon\Carbon::tomorrow())

								<li class="challenge_date">
									<div class="task-head">
										<div class="sprite-16 icon-task"></div>
										<div class="task-date">
										@if($date == Carbon\Carbon::now()->format('d.m.Y'))
											Сегодня: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(1)->format('d.m.Y'))
											Завтра: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(-1)->format('d.m.Y'))
											Вчерашние: {{ $date }}
										@else
											{{ $date }}
										@endif

										</div>
										<div class="task-count">{{ $challenge_date->count() }}</div>
										<hr />
									</div>

									{{-- Если есть задача в этой дате  --}}
									@if(!empty($challenge_date))
										@foreach($challenge_date as $challenge)

											{{-- Считаем количество задач --}}
											@php $tomorrow_challenges_count = $tomorrow_challenges_count + 1; @endphp

											<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
												<p class="task-target">{{ $challenge->description or ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
													<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
													<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
													<li><span class="task-data">Товар: </span>
													{{-- 
														{{ $challenge->challenges->choices_goods_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_services_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_raws_categories->implode('name', ', ') }}</li>
													--}}
													<li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li>
												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>
										@endforeach
									@endif
								</li>

							@endif

						@endif
					@endforeach
				@endif
			</ul>
			<input name="tomorrow_challenges_count" type="hidden" value="{{ $tomorrow_challenges_count }}">										
		</div>

	</div>
</div>

<!-- Я поставил -->
<div class="tabs-panel" id="task-panel1">

	<div class="grid-x tabs-wrap">
		<div class="small-12 cell">
			<ul class="tabs-period-task" data-tabs id="tabs-period-fromme">
				<li class="tabs-title">
					<span class="tab-challanges-count" id="last-challenges-count-from"></span>
					<a href="#last_from">Прошлые</a>
				</li>
				<li class="tabs-title is-active">
					<span class="tab-challanges-count" id="today-challenges-count-from"></span>
					<a href="#today_from" aria-selected="true">Сегодня</a>
				</li>
				<li class="tabs-title">
					<span class="tab-challanges-count" id="tomorrow-challenges-count-from"></span>
					<a href="#tomorrow_from">Будущие</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="tabs-content" data-tabs-content="tabs-period-fromme">

		<div class="tabs-panel" id="last_from">
			<ul class="for_scroll my-task">

				{{-- Запускаем если пришли задачи --}}
				@if(!empty($list_challenges['from_me']))

					{{-- Перебераем по дням  --}}
					@foreach($list_challenges['from_me'] as $date => $challenge_date)

						{{-- Если есть дата  --}}
						@if(!empty($challenge_date))

							{{-- Если дата прошедших дней --}}
							@if(Carbon\Carbon::createFromFormat('d.m.Y', $date) < Carbon\Carbon::today())

								<li class="challenge_date">
									<div class="task-head">
										<div class="sprite-16 icon-task"></div>
										<div class="task-date">
										@if($date == Carbon\Carbon::now()->format('d.m.Y'))
											Сегодня: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(1)->format('d.m.Y'))
											Завтра: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(-1)->format('d.m.Y'))
											Вчерашние: {{ $date }}
										@else
											{{ $date }}
										@endif

										</div>
										<div class="task-count">{{ $challenge_date->count() }}</div>
										<hr />
									</div>

									{{-- Если есть задача в этой дате  --}}
									@if(!empty($challenge_date))
										@foreach($challenge_date as $challenge)

											{{-- Считаем количество задач --}}
											@php $last_challenges_count_from = $last_challenges_count_from + 1; @endphp

											<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
												<p class="task-target">{{ $challenge->description or ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
													<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
													<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
													<li><span class="task-data">Товар: </span>
													{{-- 
														{{ $challenge->challenges->choices_goods_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_services_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_raws_categories->implode('name', ', ') }}</li>
													--}}
													<li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li>
												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>


										@endforeach
									@endif

								</li>

							@endif

						@endif
					@endforeach
				@endif
			</ul>
			<input name="last_challenges_count_from" type="hidden" value="{{ $last_challenges_count_from }}">										
		</div>


		<div class="tabs-panel is-active" id="today_from">
			<ul class="for_scroll my-task">
				{{-- Запускаем если пришли задачи --}}
				@if(!empty($list_challenges['from_me']))

					{{-- Перебераем по дням  --}}
					@foreach($list_challenges['from_me'] as $date => $challenge_date)

						{{-- Если есть дата  --}}
						@if(!empty($challenge_date))

							{{-- Если дата сегодняшняя --}}
							@if($date == Carbon\Carbon::now()->format('d.m.Y'))

								<li class="challenge_date">
									<div class="task-head">
										<div class="sprite-16 icon-task"></div>
										<div class="task-date">
										@if($date == Carbon\Carbon::now()->format('d.m.Y'))
											Сегодня: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(1)->format('d.m.Y'))
											Завтра: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(-1)->format('d.m.Y'))
											Вчерашние: {{ $date }}
										@else
											{{ $date }}
										@endif

										</div>
										<div class="task-count">{{ $challenge_date->count() }}</div>
										<hr />
									</div>

									{{-- Если есть задача в этой дате  --}}
									@if(!empty($challenge_date))
										@foreach($challenge_date as $challenge)

											{{-- Считаем количество задач --}}
											@php $today_challenges_count_from = $today_challenges_count_from + 1; @endphp

											<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
												<p class="task-target">{{ $challenge->description or ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
													<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
													<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
													<li><span class="task-data">Товар: </span>
													{{-- 
														{{ $challenge->challenges->choices_goods_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_services_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_raws_categories->implode('name', ', ') }}</li>
													--}}
													<li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li>
												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>

										@endforeach
									@endif
								</li>


							@endif


						@endif
					@endforeach
				@endif
			</ul>
			<input name="today_challenges_count_from" type="hidden" value="{{ $today_challenges_count_from }}">				
		</div>


		<div class="tabs-panel" id="tomorrow_from">
			<ul class="for_scroll my-task">
				{{-- Запускаем если пришли задачи --}}
				@if(!empty($list_challenges['from_me']))

					{{-- Перебераем по дням  --}}
					@foreach($list_challenges['from_me'] as $date => $challenge_date)

						{{-- Если есть дата  --}}
						@if(!empty($challenge_date))

							{{-- Если дата будущая --}}
							@if(Carbon\Carbon::createFromFormat('d.m.Y', $date) > Carbon\Carbon::tomorrow())

								<li class="challenge_date">
									<div class="task-head">
										<div class="sprite-16 icon-task"></div>
										<div class="task-date">
										@if($date == Carbon\Carbon::now()->format('d.m.Y'))
											Сегодня: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(1)->format('d.m.Y'))
											Завтра: {{ $date }}
										@elseif($date == Carbon\Carbon::now()->addDays(-1)->format('d.m.Y'))
											Вчерашние: {{ $date }}
										@else
											{{ $date }}
										@endif

										</div>
										<div class="task-count">{{ $challenge_date->count() }}</div>
										<hr />
									</div>

									{{-- Если есть задача в этой дате  --}}
									@if(!empty($challenge_date))
										@foreach($challenge_date as $challenge)

											{{-- Считаем количество задач --}}
											@php $tomorrow_challenges_count_from = $tomorrow_challenges_count_from + 1; @endphp

											<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
												<p class="task-target">{{ $challenge->description or ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
													<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
													<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
													<li><span class="task-data">Товар: </span>
													{{-- 
														{{ $challenge->challenges->choices_goods_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_services_categories->implode('name', ', ') }}
														{{ $challenge->challenges->choices_raws_categories->implode('name', ', ') }}</li>
													--}}
													<li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li>
												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>
										@endforeach
									@endif

						
								</li>


							@endif


						@endif
					@endforeach
				@endif


			</ul>
			<input name="tomorrow_challenges_count_from" type="hidden" value="{{ $tomorrow_challenges_count_from }}">								
		</div>
	</div>
</div>
