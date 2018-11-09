
@if(!empty($list_challenges))

<!-- Задачи мне -->
<div class="tabs-panel is-active" id="tasks-for-me">

	<div class="grid-x tabs-wrap">
		<div class="small-12 cell">
			<ul class="tabs-period-task" data-tabs id="tabs-period-for-me">
				<li class="tabs-title">
					<span class="tab-challanges-count" id="last-challenges-count">
						@if(isset($list_challenges['for_me']['last']))
							{{ $list_challenges['for_me']['last']->flatten(1)->count() }}
						@else 0 @endif
					</span>
					<a data-tabs-target="last">Прошлые</a>
				</li>
				<li class="tabs-title is-active">
					<span class="tab-challanges-count" id="today-challenges-count">
						@if(isset($list_challenges['for_me']['today']))
							{{ $list_challenges['for_me']['today']->flatten(1)->count() }}
						@else 0 @endif
					</span>
					<a data-tabs-target="today" aria-selected="true">Сегодня</a>
				</li>
				<li class="tabs-title">
					<span class="tab-challanges-count" id="tomorrow-challenges-count">
						@if(isset($list_challenges['for_me']['future']))
							{{ $list_challenges['for_me']['future']->flatten(1)->count() }}
						@else 0 @endif
					</span>
					<a data-tabs-target="tomorrow">Будущие</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="tabs-content" data-tabs-content="tabs-period-for-me">

		<div class="tabs-panel" id="last">
			<ul class="for_scroll my-task">
				{{-- Мои просроченные задачи --}}
				@if(isset($list_challenges['for_me']['last']))
					@include('layouts.list_challenges', ['list_challenges' => $list_challenges['for_me']['last']])
				@endif
			</ul>							
		</div>

		<div class="tabs-panel is-active" id="today">
			<ul class="for_scroll my-task">
				{{-- Мои задачи на сегодня --}}
				@if(isset($list_challenges['for_me']['today']))
					@include('layouts.list_challenges', ['list_challenges' => $list_challenges['for_me']['today']])
				@endif
			</ul>				
		</div>

		<div class="tabs-panel" id="tomorrow">
			<ul class="for_scroll my-task">
				{{-- Мои задачи на завтра --}}
				@if(isset($list_challenges['for_me']['future']))
					@include('layouts.list_challenges', ['list_challenges' => $list_challenges['for_me']['future']])
				@endif
			</ul>									
		</div>

	</div>
</div>



<!-- Я поставил -->
<div class="tabs-panel" id="tasks-from-me">

	<div class="grid-x tabs-wrap">
		<div class="small-12 cell">
			<ul class="tabs-period-task" data-tabs id="tabs-period-from-me">
				<li class="tabs-title">
					<span class="tab-challanges-count" id="last-challenges-count-from">
						@if(isset($list_challenges['from_me']['last']))
							{{ $list_challenges['from_me']['last']->flatten(1)->count() }}
						@else 0 @endif
					</span>
					<a data-tabs-target="last_from">Прошлые</a>
				</li>
				<li class="tabs-title is-active">
					<span class="tab-challanges-count" id="today-challenges-count-from">
						@if(isset($list_challenges['from_me']['today']))
							{{ $list_challenges['from_me']['today']->flatten(1)->count() }}
						@else 0 @endif
					</span>
					<a data-tabs-target="today_from" aria-selected="true">Сегодня</a>
				</li>
				<li class="tabs-title">
					<span class="tab-challanges-count" id="tomorrow-challenges-count-from">
						@if(isset($list_challenges['from_me']['future']))
							{{ $list_challenges['from_me']['future']->flatten(1)->count() }}
						@else 0 @endif
					</span>
					<a data-tabs-target="tomorrow_from">Будущие</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="tabs-content" data-tabs-content="tabs-period-from-me">

		<div class="tabs-panel" id="last_from">
			<ul class="for_scroll my-task">
				{{-- Задачи которые ставил я просрочили --}}
				@if(isset($list_challenges['from_me']['last']))
					@include('layouts.list_challenges', ['list_challenges' => $list_challenges['from_me']['last']])
				@endif
			</ul>										
		</div>


		<div class="tabs-panel is-active" id="today_from">
			<ul class="for_scroll my-task">
				{{-- Задачи которые ставил на сегодня --}}
				@if(isset($list_challenges['from_me']['today']))
					@include('layouts.list_challenges', ['list_challenges' => $list_challenges['from_me']['today']])
				@endif
			</ul>			
		</div>


		<div class="tabs-panel" id="tomorrow_from">
			<ul class="for_scroll my-task">
				{{-- Задачи которые ставил на будущее --}}
				@if(isset($list_challenges['from_me']['future']))
					@include('layouts.list_challenges', ['list_challenges' => $list_challenges['from_me']['future']])
				@endif
			</ul>							
		</div>
	</div>
</div>
@endif