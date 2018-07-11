@php
$checked = '';
@endphp

@if (in_array($metric->id, $products_category_metrics))
@php
$checked = 'checked';
@endphp
@endif

<li class="checkbox">
	{{ Form::checkbox('add_metric_id', $metric->id, null, ['class' => 'add-metric', 'id' => 'add-metric-'. $metric->id, $checked]) }}
	<label for="add-metric-{{ $metric->id }}"><span>{{ $metric->name }}</span></label>
</li>

