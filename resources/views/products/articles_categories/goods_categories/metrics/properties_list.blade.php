<div class="grid-x grid-padding-x">
	<div class="small-12 cell">
		<ul class="checker" id="properties-list">
			@foreach ($properties as $property)
			@if($property->metrics->isNotEmpty())
			@include('products.articles_categories.goods_categories.metrics.property', ['property' => $property])
			@endif
			@endforeach

			{{-- @each('products.property', $properties, 'property') --}}
		</ul>
	</div>

	<div class="small-12 cell wrap-add-new-metric">
		<label>Создать свойство
			{{ Form::select('property_id', $properties->pluck('name', 'id'), null, ['id' => 'properties-select', 'placeholder' => 'Выберите свойство']) }}
		</label>
	</div>
</div>
