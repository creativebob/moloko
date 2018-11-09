@php
if ($set_status == 'one') {
	$name = 'metrics';
} else {
	$name = 'set_metrics';
}
@endphp

<li class="checkbox">
	{{ Form::checkbox($name . '[]', $metric->id, null, ['class' => 'add-metric', 'id' => $set_status.'-add-metric-'. $metric->id, 'data-set-status' => $set_status]) }}
	<label for="{{ $set_status }}-add-metric-{{ $metric->id }}"><span>{{ $metric->name }}</span></label>
</li>

