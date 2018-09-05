<aside class="task-manager el" id="task-manager">
	<div class="grid-x tabs-wrap">
		<div class="small-12 cell">
			<ul class="tabs-list" data-tabs id="tabs">
				<li class="tabs-title is-active"><a data-tabs-target="task-panel2" href="#task-panel2" aria-selected="true">Задачи мне</a></li>
				<li class="tabs-title"><a href="#task-panel1">Я поставил</a></li>
			</ul>
		</div>
	</div>
<div class="grid-x tabs-wrap">
<div class="small-12 cell">
<div class="tabs-content" data-tabs-content="tabs">

<!-- Задачи мне -->
<div class="tabs-panel is-active" id="task-panel2">
	<ul class="for_scroll my-task">
		@php



		@endphp

		@if(!empty($challenges))
			@foreach($challenges as $date => $challenge_date)

				@if(!empty($challenge_date))

					<li class="challenge_date">
						<div class="task-head">
							<div class="sprite-16 icon-task"></div>
							<div class="task-date">{{ $date }}</div>
							<div class="task-count">{{ $challenge_date->count() }}</div>
							<hr />
						</div>

						@if(!empty($challenge_date))
							@foreach($challenge_date as $challenge)
								<div class="task-content">
									<h5 class="task-content-head">{{ $challenge->challenge_type->name or ''}}</h5>
									<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">От: {{ $challenge->author->first_name . ' ' . $challenge->author->second_name }}</span>
									<p class="task-target">{{ $challenge->description or ''}}</p>
									<ul class="task-list">
										<li><span class="task-data">№: <a href="/admin/leads/{{ $challenge->challenges->id }}/edit">{{ $challenge->challenges->case_number }}</a></span></li>
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

	</ul>
</div>



<!-- Я поставил -->
<div class="tabs-panel" id="task-panel1">
	<p>lol</p>
</div>


</div>
</div>
</div>
</aside>