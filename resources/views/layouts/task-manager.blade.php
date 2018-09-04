<aside class="task-manager el" id="task-manager">
	<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title"><a href="#task-panel1">Я поставил</a></li>
        <li class="tabs-title is-active"><a data-tabs-target="task-panel2" href="#task-panel2" aria-selected="true">Задачи мне</a></li>
      </ul>
    </div>
  </div>
	<div class="grid-x tabs-wrap">
	  <div class="small-12 cell">
	    <div class="tabs-content" data-tabs-content="tabs">
	      <!-- Я поставил -->
	      <div class="tabs-panel" id="task-panel1">
	     		<p>lol</p>
	      </div>
	      <!-- Задачи мне -->
	      <div class="tabs-panel is-active" id="task-panel2">
	      	<ul>
	      			@if(!empty($challenges))
	      			@foreach($challenges as $challenge)

	     			<li>
	     				<div class="task-head">
   							<div class="sprite-16 icon-task"></div>
   							<div class="task-date">{{ date('d.m.Y') }}</div>
   							<div class="task-count">{{ $challenges->count() }}</div>
   							<hr />
	     				</div>
	     				<div class="task-content">
		     				<h5 class="task-content-head">{{ $challenge->challenge_type->name }}</h5>
		     				<span class="task-time">{{ $challenge->deadline_date->format('H:i') }}</span><span class="task-set">От: {{ $challenge->author->first_name . ' ' . $challenge->author->second_name }}</span>
		     				<p class="task-target">{{ $challenge->description or ''}}</p>
		     				<ul class="task-list">
		     					<li><span class="task-data">Клиент: </span><span class="task-name-lead">{{ $challenge->challenges->name }}</span></li>
		     					<li><span class="task-data">Телефон: </span>{{ $challenge->challenges->phone }}</span></li>
		     					<li><span class="task-data">Чек: </span>{{ $challenge->challenges->badget }}</li>
		     					<li><span class="task-data">Товар: </span>Откатные ворота</li>
		     					<li><span class="task-data">Адрес: </span>{{ $challenge->challenges->address }}</li>
		     				</ul>
		     				<a href="#" class="task-button button">ГОТОВО</a>
	     				</div>
	     			</li>

	     			@endforeach
	     			@endif

	     		</ul>
	      </div>
	    </div>
	  </div>
	</div>
</aside>