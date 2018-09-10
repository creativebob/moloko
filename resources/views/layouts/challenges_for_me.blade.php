@if(!empty($challenges))
			@foreach($challenges as $date => $challenge_date)

				@if(!empty($challenge_date))

					<li class="challenge_date">
						<div class="task-head">
							<div class="sprite-16 icon-task"></div>
							<div class="task-date">
							@if($date == Carbon\Carbon::now()->format('d.m.Y'))
								Сегодня
							@elseif($date == Carbon\Carbon::now()->addDays(1)->format('d.m.Y'))
								Завтра
							@elseif($date == Carbon\Carbon::now()->addDays(-1)->format('d.m.Y'))
								Вчерашние
							@else
								{{ $date }}
							@endif

							</div>
							<div class="task-count">{{ $challenge_date->count() }}</div>
							<hr />
						</div>

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
			@endforeach
		@endif