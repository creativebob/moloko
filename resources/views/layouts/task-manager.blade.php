<aside class="task-manager el {{ $open }}" id="task-manager">
	<div class="grid-x tabs-wrap">
		<div class="small-12 cell">
			<ul class="tabs-list" data-tabs id="tabs">
				<li class="tabs-title is-active"><a data-tabs-target="tasks-for-me" aria-selected="true">Задачи мне</a></li>
				<li class="tabs-title"><a data-tabs-target="tasks-from-me">Задачи от меня</a></li>
			</ul>
		</div>
	</div>
	<div class="grid-x tabs-wrap period-task">
		<div class="small-12 cell">
			<div class="tabs-content" data-tabs-content="tabs" id="portal-challenges-for-me">

				{{-- Менеджер задач --}}
				@include('layouts.challenges_for_me')

			</div>
		</div>
	</div>
</aside>

@include('layouts.task-manager-script')
