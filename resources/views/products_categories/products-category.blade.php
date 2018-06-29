<li>

	@if ($products_cat['products_count'] > 0)
	<div class="checkbox">
		{{ Form::checkbox('add_products_category_id', $products_cat['id'], null, ['class' => 'add-composition', 'id' => 'add-producs_categories-'.$products_cat['id']]) }}
		<label for="add-producs_categories-{{ $products_cat['id'] }}"><span>{{ $products_cat['name'] }}</span></label>
	</div>
	@else
	<span class="parent" data-open="products_cat-{{ $products_cat['id'] }}">{{ $products_cat['name'] }}</span>
	@if (isset($products_cat['children']))
	<div class="checker-nested" id="products_cat-{{ $products_cat['id'] }}">
		<ul class="checker">
			@foreach ($products_cat['children'] as $products_cat)
			@include('products_categories.products-category', $products_cat)
			@endforeach
		</ul>
	</div>
	@endif
	@endif
</li>