<li>
	<span class="parent" data-open="property-{{ $property->id }}">{{ $property->name }}</span>
	<div class="checker-nested" id="property-{{ $property->id }}">
		<ul class="checker">
			@foreach ($property->metrics as $metric)

			@include('includes.category_metrics.metrics', ['metric' => $metric])
			@endforeach
		</ul>
	</div>
</li>