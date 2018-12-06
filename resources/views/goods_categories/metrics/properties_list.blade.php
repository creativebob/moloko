<ul class="checker" id="{{ $set_status }}-properties-list">
	@foreach ($properties as $property)
	@if(count($property->metrics))
	@include('goods_categories.metrics.property', ['property' => $property, 'set_status' => $set_status])
	@endif
	@endforeach

	{{-- @each('products.property', $properties, 'property') --}}

	<li>
		<br>
		<label>Создать свойство
			{{ Form::select('property_id', $properties->pluck('name', 'id'), null, ['id' => $set_status.'-properties-select', 'placeholder' => 'Выберите свойство']) }}
		</label>
	</li>
</ul>