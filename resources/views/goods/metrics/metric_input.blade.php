@switch($metric->property->type)

@case('numeric')



<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	{{ Form::number('metrics['.$metric->id.'][]', $metrics_values[$metric->id] ? number_format($metrics_values[$metric->id], $metric->decimal_place) : null, ['required', 'id' => 'metric-'.$metric->id.'-field', 'min' => number_format($metric->min, $metric->decimal_place), 'max' => number_format($metric->max, $metric->decimal_place), 'step' => 'any']) }}
</label>
<script type="text/javascript">

	let metric_object_{{ $metric->id }} = new MetricNumeric({{ $metric->decimal_place }});

	// Таблица
	$(document).on('keyup', "#metric-{{ $metric->id }}-field", function() {
		metric_object_{{ $metric->id }}.check(this);
	});

	// alert({{ $metric->name }}.max);




</script>
@break

@case('percent')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span> ({{ $metric->unit->abbreviation }})
	{{ Form::number('metrics['.$metric->id.'][]', $metrics_values[$metric->id] ? number_format($metrics_values[$metric->id], $metric->decimal_place) : null, ['required']) }}
</label>
@break

@case('list')

@switch($metric->list_type)

@case('list')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
</label>

<div class="checkboxer-wrap">
	<div class="checkboxer-toggle" data-toggle="metric-{{ $metric->id }}-dropdown" data-name="">
		<div class="checkboxer-title">
			<span class="title">Список </span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>
</div>
<div class="dropdown-pane checkboxer-pane hover" data-position="bottom" data-alignment="left" id="metric-{{ $metric->id }}-dropdown" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="2">

	<ul class="checkbox checkbox-group">
		@foreach ($metric->values as $value)
		@php
		if ($metrics_values[$metric->id]) {
			$checked = in_array($value->id, $metrics_values[$metric->id]);
		} else {
			$checked = false;
		}
		@endphp

		<li>
			{{ Form::checkbox('metrics['.$metric->id.'][]', $value->id, $checked, ['id' => 'add-metric-value-'. $value->id]) }}
			<label for="add-metric-value-{{ $value->id }}"><span>{{ $value->value }}</span></label>
		</li>
		@endforeach
	</ul>
</div>
@break

@case('select')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	{{ Form::select('metrics['.$metric->id.'][]', $metric->values->pluck('value', 'id'), $metrics_values[$metric->id] ? $metrics_values[$metric->id] : null, ['required']) }}
</label>
@break

@endswitch

@break

@endswitch








