
@switch($metric->property->type)

@case('numeric')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	{{ Form::number('metrics['.$metric->id.'][]', $metrics_values[$metric->id] ? $metrics_values[$metric->id] : null) }}
</label>
@break

@case('percent')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span> ({{ $metric->unit->abbreviation }})
	{{ Form::number('metrics['.$metric->id.'][]', $metrics_values[$metric->id] ? $metrics_values[$metric->id] : null) }}
</label>
@break

@case('list')
<label>
	@switch($metric->list_type)
	@case('list')
	<a data-toggle="metric-{{ $metric->id }}-dropdown">Список: {{ $metric->name }}</a>
</label>
<div class="dropdown-pane" id="metric-{{ $metric->id }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">
	<ul>



@foreach ($metric->values as $value)
@php
if ($metrics_values[$metric->id]) {
	$checked = in_array($value->id, $metrics_values[$metric->id]);
} else {
	$checked = false;
}


@endphp

<li class="checkbox">
	{{ Form::checkbox('metrics['.$metric->id.'][]', $value->id, $checked, ['id' => 'add-metric-value-'. $value->id]) }}
	<label for="add-metric-value-{{ $value->id }}"><span>{{ $value->value }}</span></label>
</li>
@endforeach

</ul>
</div>

@break

@case('select')

<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
<ul>
	@php
	$list = $metric->values->pluck('value', 'id');
	@endphp

	{{ Form::select('metrics['.$metric->id.'][]', $list, $metrics_values[$metric->id] ? $metrics_values[$metric->id] : null) }}

</ul>
</label>
@break
@endswitch
@break


@endswitch

