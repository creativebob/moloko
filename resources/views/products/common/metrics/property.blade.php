<li>
	<span class="parent" data-open="property-{{ $property->id }}">{{ $property->name }}</span>
	<div class="checker-nested" id="property-{{ $property->id }}">
		<ul class="checker">
			@foreach ($property->metrics as $metric)

			@include('products.common.metrics.metrics', ['metric' => $metric])
			@endforeach
		</ul>
	</div>
</li>
