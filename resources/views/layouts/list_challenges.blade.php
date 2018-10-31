
				{{-- Запускаем если пришли задачи --}}
				@if(!empty($list_challenges))

					{{-- Перебераем по дням --}}
					@foreach($list_challenges as $date => $challenge_date)

						{{-- Если есть дата --}}
						@if(!empty($challenge_date))

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

											<div id="task-challenge-{{$challenge->id}}" class="task-content @if($challenge->deadline_date < Carbon\Carbon::now()) deadline-active @endif">
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span>
												<span class="task-set">{{ $challenge->challenge_type->name or ''}} ({{ $challenge->appointed->name or ''}})</span>

												@if($challenge->priority_id == 2)<span class="priority_2">Молния</span>@endif

												<p class="task-target">{{ $challenge->description or ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number or '' }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->challenges->name }}</li>
													{{-- <li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->challenges->phone) }}</li> --}}
													<li><span class="task-data">Чек: </span>{{ num_format($challenge->challenges->badget, 0) }}</li>
													<li><span class="task-data">Товар: </span>

												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>

										@endforeach								
									@endif
								</li>

						@endif
					@endforeach
				@endif