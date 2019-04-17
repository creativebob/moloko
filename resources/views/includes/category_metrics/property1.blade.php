<li>
	<span class="parent" data-open="{{ $set_status }}-property-{{ $property->id }}">{{ $property->name }}</span>
	<div class="checker-nested" id="{{ $set_status }}-property-{{ $property->id }}">
		<ul class="checker">
			@foreach ($property->metrics as $metric)

			@include('includes.metrics_category.metrics', ['metric' => $metric, 'set_status' => $set_status])
			@endforeach
		</ul>
	</div>
</li>