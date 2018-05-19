{{-- Чекбоксер --}}

@php

	$filter = $value;
	$relations = $filter[$name]['relations'];

@endphp

@if(!empty($filter[$name]))
<div class="checkboxer-wrap {{$name}}">
	<div class="checkboxer-toggle" data-toggle="{{$name}}-dropdown-bottom-left" data-name="{{$name}}">
		<div class="checkboxer-title">
			<span class="title">{{$filter[$name]['title']}}</span>
			<span class="count_filter_{{$name}}" id="count_filter_{{$name}}">({{$filter[$name]['count_mass']}})</span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>

	@php
		if($filter[$name]['count_mass'] > 0){$show_status = 'show-elem';} else {$show_status = 'hide-elem';};
	@endphp

	<div class="checkboxer-clean {{ $show_status }}" onclick="event.stopPropagation()" data-name="{{$name}}">
		<span class="sprite icon-clean"></span>
	</div>

</div>

<div class="dropdown-pane checkboxer-pane hover {{$name}}" data-position="bottom" data-alignment="left" id="{{$name}}-dropdown-bottom-left" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="1">

	<ul class="checkboxer-menu {{$name}}" data-name="{{$name}}">

		@foreach ($filter[$name]['collection'] as $key => $value)
			<li>

				{{-- Блок для выбора через связь --}}
				@if($relations != null)

					@php

					if($value->$relations == null){

						$value_id = 'null';
						$value_name = 'Не указано';
					} else {

						$value_id = $value->$relations->$name->id;
						$value_name = $value->$relations->$name->name;
					};

					@endphp

					{{ Form::checkbox($name . '_id[]', $value_id, $filter[$name]['mass_id'], ['id'=>$name.'-'.$value_id]) }}
					<label for="{{$name}}-{{ $value_id }}"><span>{{ $value_name }}</span></label>


				{{-- Блок для выбора по прямым полям (id) --}}
				@else

					@php
					$value_id = $value->$name->id;
					$value_name = $value->$name->name;
					@endphp

					{{ Form::checkbox($name . '_id[]', $value_id, $filter[$name]['mass_id'], ['id'=>$name.'-'.$value_id]) }}
					<label for="{{$name}}-{{ $value_id }}"><span>{{ $value_name }}</span></label>

				@endif

			</li>

		@endforeach
	</ul>

</div>

<script type="text/javascript">

	let {{$name}} = new CheckBoxer("{{$name}}", {{$filter[$name]['count_mass']}});

  	$(".checkboxer-menu.{{$name}} :checkbox").click(function() {
		{{$name}}.CheckBoxerAddDel(this);
	});

  	$(".{{$name}} .checkboxer-toggle").click(function() {
		{{$name}}.CheckBoxerSetWidth(this);
	});

 	$(".{{$name}} .checkboxer-clean").click(function() {
		{{$name}}.CheckBoxerClean();
	});

</script>
@endif