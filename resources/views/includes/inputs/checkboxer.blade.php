{{-- Чекбоксер --}}

@php
	$checkboxer_mass = $value;
	$relation = $name;
@endphp

@if(!empty($checkboxer_mass[$name]))
<div class="checkboxer-wrap {{$name}}">
	<div class="checkboxer-toggle" data-toggle="{{$name}}-dropdown-bottom-left" data-name="{{$name}}">
		<div class="checkboxer-title">
			<span class="title">{{$checkboxer_mass[$name]['title']}}</span>
			<span class="count_filter_{{$name}}" id="count_filter_{{$name}}">({{$checkboxer_mass[$name]['count_mass']}})</span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>

	@php
		if($checkboxer_mass[$name]['count_mass'] > 0){$show_status = 'show-elem';} else {$show_status = 'hide-elem';};
	@endphp

	<div class="checkboxer-clean {{ $show_status }}" onclick="event.stopPropagation()" data-name="{{$name}}">
		<span class="sprite icon-clean"></span>
	</div>

</div>

<div class="dropdown-pane checkboxer-pane hover {{$name}}" data-position="bottom" data-alignment="left" id="{{$name}}-dropdown-bottom-left" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="1">

	<ul class="checkboxer-menu {{$name}}" data-name="{{$name}}">

		@foreach ($checkboxer_mass[$name]['collection'] as $key => $value)
			<li>

				{{-- Блок для выбора по городу (через связи) --}}
				@if($name == 'city')

					@if($checkboxer_mass[$name]['mode'] == 'id')

						{{ Form::checkbox($name . '_id[]', $value->location->city->id, $checkboxer_mass[$name]['mass_id'], ['id'=>$name.'-'.$value->location->city->id]) }}
						<label for="{{$name}}-{{ $value->location->city->id }}"><span>{{ $value->location->city->name }}</span></label>
					@else

						{{ Form::checkbox($name . '_id[]', $value->id, $checkboxer_mass[$name]['mass_id'], ['id'=>$name.'-'.$value->id]) }}
						<label for="{{$name}}-{{ $value->id }}">
							<span>{{ $value->$entity_name }}</span>
						</label>
					@endif

				{{-- Блок для выбора по прямым полям (id) --}}
				@else

					@if($checkboxer_mass[$name]['mode'] == 'id')

						{{ Form::checkbox($name . '_id[]', $value->$relation->id, $checkboxer_mass[$name]['mass_id'], ['id'=>$name.'-'.$value->$name->id]) }}
						<label for="{{$name}}-{{ $value->$name->id }}"><span>{{ $value->$name->name }}</span></label>
					@else

						{{ Form::checkbox($name . '_id[]', $value->id, $checkboxer_mass[$name]['mass_id'], ['id'=>$name.'-'.$value->id]) }}
						<label for="{{$name}}-{{ $value->id }}">
							<span>{{ $value->$entity_name }}</span>
						</label>
					@endif

				@endif

			</li>

		@endforeach
	</ul>

</div>

<script type="text/javascript">

	let {{$name}} = new CheckBoxer("{{$name}}", {{$checkboxer_mass[$name]['count_mass']}});

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