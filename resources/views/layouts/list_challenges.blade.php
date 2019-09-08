
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
												{{-- <h5 class="task-content-head">{{ $challenge->challenge_type->name ?? ''}}</h5>--}}
												<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span>
												<span class="task-set">{{ $challenge->challenge_type->name ?? ''}} ({{ $challenge->appointed->name ?? ''}})</span>

												@if($challenge->priority_id == 2)<span class="priority_2">Молния</span>@endif

												<p class="task-target">{{ $challenge->description ?? ''}}</p>
												<ul class="task-list">
													<li><span class="task-data">№: </span><a href="/admin/leads/{{ $challenge->subject_id }}/edit">{{ $challenge->subject->case_number ?? '' }}</a></li>
													<li><span class="task-data">Клиент: </span>{{ $challenge->subject->name ?? '' }}</li>
													{{-- <li><span class="task-data">Телефон: </span>{{ decorPhone($challenge->subject->phone) }}</li> --}}
													<li><span class="task-data">Чек: </span>{{ $challenge->subject ? num_format($challenge->subject->badget, 0) : 0 }}</li>

													{{-- <li><span class="task-data">Товар: </span>{{ $challenge->subject->choice->name ?? ''}}</li> --}}

												</ul>

												{{--<a href="#" class="task-button button">ГОТОВО</a>--}}
											</div>

										@endforeach								
									@endif
								</li>

						@endif
					@endforeach
				@endif