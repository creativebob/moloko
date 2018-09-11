<div class="tabs-panel" id="lost">
<ul class="for_scroll my-task">
		{{-- Запускаем если пришли задачи --}}
		@if(!empty($challenges))

			{{-- Перебераем по дням  --}}
			@foreach($challenges as $date => $challenge_date)

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

									<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
										{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
										<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
										<p class="task-target">{{ $challenge->description or ''}}</p>
										<ul class="task-list">
											<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
											<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
											<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
											<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
											<li><span class="task-data">Товар: </span>Откатные ворота</li>
											{{-- <li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li> --}}
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
</div>


<div class="tabs-panel is-active" id="today">
	<ul class="for_scroll my-task">
		{{-- Запускаем если пришли задачи --}}
		@if(!empty($challenges))

			{{-- Перебераем по дням  --}}
			@foreach($challenges as $date => $challenge_date)

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

									<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
										{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
										<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
										<p class="task-target">{{ $challenge->description or ''}}</p>
										<ul class="task-list">
											<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
											<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
											<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
											<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
											<li><span class="task-data">Товар: </span>Откатные ворота</li>
											{{-- <li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li> --}}
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
</div>


<div class="tabs-panel" id="tomorrow">
<ul class="for_scroll my-task">
		{{-- Запускаем если пришли задачи --}}
		@if(!empty($challenges))

			{{-- Перебераем по дням  --}}
			@foreach($challenges as $date => $challenge_date)

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

									<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
										{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
										<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">{{ $challenge->challenge_type->name or ''}}</span>
										<p class="task-target">{{ $challenge->description or ''}}</p>
										<ul class="task-list">
											<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></li>
											<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
											<li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li>
											<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
											<li><span class="task-data">Товар: </span>Откатные ворота</li>
											{{-- <li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li> --}}
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
</div>
