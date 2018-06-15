<ul class="checker" id="properties-list">
	@foreach ($properties as $property)
	@if($property->metrics_count > 0)
	@include('products.property', $property)
	@endif
	@endforeach

	{{-- @each('products.property', $properties, 'property') --}}

</ul>