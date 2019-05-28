<li class="checkbox">
	{{ Form::checkbox('metrics[]', $metric->id, null, ['class' => 'change-metric', 'id' => 'metric-'. $metric->id]) }}
	<label for="metric-{{ $metric->id }}"><span>{{ $metric->name }}</span></label>
</li>

