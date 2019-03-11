@if (count($cities['cities_list']) > 1)
<button class="change-city" type="button" data-toggle="example-dropdown">Город:
	<span id="mycity" class="mycity">{{ $cities['active_city']->name }}

    </span>
</button>

<div class="dropdown-pane toggle-city" id="example-dropdown" data-v-offset="3" data-dropdown data-close-on-click="true">
	<ul class="list-city">

		@foreach ($cities['cities_list'] as $city)
		@if ($city->id == $cities['active_city']->id)

		<li class="noactive">
			<span>{{ $city->name }}</span>
		</li>

		@else

		<li>
			@if (isset($page))
				<a href="change_city/{{ $city->id }}/{{ $page->alias }}">{{ $city->name }}</a>
			@else
				<a href="change_city/{{ $city->id }}">{{ $city->name }}</a>
			@endif

		</li>

		@endif
		@endforeach

	</ul>
</div>

@else

<div class="change-city">Город: <span id="mycity" class="mycity">{{ $cities['cities_list'][0]->name }}</span></div>

@endif