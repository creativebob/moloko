<aside class="task-manager el {{ $open }}" id="task-manager">
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
	<ul class="for_scroll my-task" id="portal-challenges-for-me">

    	{{-- Менеджер задач --}}
    	@include('layouts.challenges_for_me')

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