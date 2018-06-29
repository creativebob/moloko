<ul class="checker" id="properties-list">
	@foreach ($properties as $property)
	@include('products.property', $property)
	@endforeach

	{{-- @each('products.property', $properties, 'property') --}}

</ul>