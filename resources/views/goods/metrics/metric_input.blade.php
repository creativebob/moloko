@switch($metric->property->type)

@case('numeric')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	{{ Form::number('metrics['.$metric->id.'][]', $metrics_values[$metric->id] ? number_format($metrics_values[$metric->id], $metric->decimal_place) : null, ['required', 'id' => 'metric-'.$metric->id.'-field', 'min' => number_format($metric->min, $metric->decimal_place), 'max' => number_format($metric->max, $metric->decimal_place), 'step' => 'any']) }}
	<span class="form-error">Поле обязательно для заполнения!</span>
</label>
<script type="text/javascript">

	let metric_object_{{ $metric->id }} = new MetricNumeric({{ $metric->decimal_place }});

	// Таблица
	$(document).on('keyup', "#metric-{{ $metric->id }}-field", function() {
		metric_object_{{ $metric->id }}.check(this);
	});

</script>
@break

@case('percent')
<label>
	<span data-tooltip tabindex="1" title="{{ $metric->description }}">{{ $metric->name }}</span>
	{{ Form::number('metrics['.$metric->id.'][]', $metrics_values[$metric->id] ? number_format($metrics_values[$metric->id], $metric->decimal_place) : null, ['required', 'id' => 'metric-'.$metric->id.'-field', 'min' => number_format($metric->min, $metric->decimal_place), 'max' => number_format($metric->max, $metric->decimal_place), 'step' => 'any']) }}
	<span class="form-error">Поле обязательно для заполнения!</span>
</label>
<script type="text/javascript">

	let metric_object_{{ $metric->id }} = new MetricNumeric({{ $metric->decimal_place }});

	// Таблица
	$(document).on('keyup', "#metric-{{ $metric->id }}-field", function() {
		metric_object_{{ $metric->id }}.check(this);
	});

</script>
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
			<span class="title">Список</span>
			<span class="form-error">Выберите минимум один пункт!</span>
		</div>
		<div class="checkboxer-button">
			<span class="sprite icon-checkboxer"></span>
		</div>
	</div>
</div>

<div class="dropdown-pane checkboxer-pane hover" data-position="bottom" data-alignment="left" id="metric-{{ $metric->id }}-dropdown" data-dropdown data-auto-focus="true" data-close-on-click="true" data-h-offset="-17" data-v-offset="2">

	<ul class="checkbox checkbox-group" id="checkbox-group-{{ $metric->id }}">
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
<script type="text/javascript">

	let metric_object_{{ $metric->id }} = new MetricList({{ $metric->id }});

	// Таблица
	$(document).on('click', "#checkbox-group-{{ $metric->id }} input:checkbox", function() {
		metric_object_{{ $metric->id }}.check(this);
	});

</script>
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








