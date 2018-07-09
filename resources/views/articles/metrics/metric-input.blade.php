@php
$metrics_value = null;
@endphp

@if(isset($metrics_values[$metric->id]->pivot->value))
@php
$metrics_value = $metrics_values[$metric->id]->pivot->value;
@endphp
@endif

@switch($metric->property->type)

@case('numeric')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	{{ Form::number('metrics['.$metric->id.'][value]', $metrics_value) }}
</label>
@break

@case('percent')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	{{ Form::number('metrics['.$metric->id.'][value]', $metrics_value) }}
</label>
@break

@case('list')
<label>
	@switch($metric->list_type)
	@case('list')
	<a data-toggle="metric-{{ $metric->id }}-dropdown">Список: {{ $metric->name }}</a>
	<div class="dropdown-pane" id="metric-{{ $metric->id }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">
		<ul>

			@foreach ($metric->values as $value)
			<li class="checkbox">
				{{ Form::checkbox('metrics-'.$metric->id.'[]', $value->value, null, ['id' => 'add-metric-value-'. $value->id]) }}
				<label for="add-metric-value-{{ $value->id }}"><span>{{ $value->value }}</span></label>
			</li>
			@endforeach

		</ul>
	</div>
	@break

	@case('select')
	
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	<ul>
		<select name="metrics[{{ $metric->id }}][value]">
			@foreach ($metric->values as $value)
			@php
			$selected = null;
			@endphp

			@if(isset($metrics_values[$metric->id]->pivot->value))
			@if($metrics_values[$metric->id]->pivot->value == $value->value)
			@php
			$selected = 'selected';
			@endphp
			@endif
			@endif
			<option value="{{ $value->value }}" {{ $selected }}>{{ $value->value }}</option>
			@endforeach
		</select>
	</ul>
	@break
	@endswitch
</label>
@break


@endswitch

