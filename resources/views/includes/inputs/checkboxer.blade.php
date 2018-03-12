{{-- Чекбоксер 

--}}
<div class="checkboxer-wrap {{$name}}">
	<div class="checkboxer-toggle" data-toggle="{{$name}}-dropdown-bottom-left">
		<div class="checkboxer-title"> 
			<span class="title">Выбрать город</span>
			<span class="count_filter_{{$name}}" id="count_filter_{{$name}}">({{$filter[$name]['count_mass']}})</span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>

	@php
		if($filter[$name]['count_mass'] > 0){$show_status = 'show-elem';} else {$show_status = 'hide-elem';};
		$entity_name = $name . '_name';
	@endphp

	<div class="checkboxer-clean {{ $show_status }}">
		<span class="sprite icon-clean"></span>
	</div>

</div>

<div class="dropdown-pane checkboxer-pane hover" data-position="bottom" data-alignment="left" id="{{$name}}-dropdown-bottom-left" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="1">

	<ul class="checkboxer-menu {{$name}}">
		@foreach ($filter[$name]['collection'] as $key => $value)
			<li>
				{{ Form::checkbox($name . '_id[]', $value->$name->id, $filter[$name]['mass_id'], ['id'=>$value->$name->id]) }}
				<label for="{{ $value->$name->id }}"><span>{{ $value->$name->$entity_name }}</span></label>
			</li>
		@endforeach
	</ul>

</div>

<script type="text/javascript">

	let {{$name}} = new CheckBoxer("{{$name}}", {{$filter[$name]['count_mass']}});

  	$(".checkboxer-menu.{{$name}} :checkbox").click(function() {
		{{$name}}.CheckBoxerAddDel(this);
	});

 	$(".{{$name}} .checkboxer-clean").click(function() {
		{{$name}}.CheckBoxerClean();
	});

</script>