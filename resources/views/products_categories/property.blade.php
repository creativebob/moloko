<li>
	<span class="parent" data-open="property-{{ $property->id }}">{{ $property->name }}</span>
	<div class="checker-nested" id="property-{{ $property->id }}">
		<ul  class="checker">
			@foreach ($property->metrics as $metric)
                  @include('products_categories.metrics', $metric)
                @endforeach
			{{-- @each('products.metrics', $property->metrics, 'metric') --}}
		</ul>
	</div>
</li>