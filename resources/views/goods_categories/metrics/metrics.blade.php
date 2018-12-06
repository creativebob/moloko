@php
	$name = ($set_status == 'one') ? 'one_metrics' : 'set_metrics';
@endphp

<li class="checkbox">
	{{ Form::checkbox($name . '[]', $metric->id, null, ['class' => 'change-metric', 'id' => $set_status.'-metric-'. $metric->id, 'data-set-status' => $set_status]) }}
	<label for="{{ $set_status }}-metric-{{ $metric->id }}"><span>{{ $metric->name }}</span></label>
</li>

