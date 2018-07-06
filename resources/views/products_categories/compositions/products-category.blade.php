
@if (($products_cat['products_count'] > 0) || isset($products_cat['children']))
<li>
	<span class="parent" data-open="products_cat-{{ $products_cat['id'] }}">{{ $products_cat['name'] }}</span>
	<div class="checker-nested" id="products_cat-{{ $products_cat['id'] }}">
		<ul class="checker">
			@if ($products_cat['products_count'] > 0)

			@foreach ($products_cat['products'] as $product)
			@include('products_categories.compositions.product', $product)
			@endforeach

			@endif
			@if (isset($products_cat['children']))

			@foreach ($products_cat['children'] as $products_cat)
			@include('products_categories.compositions.products-category', $products_cat)
			@endforeach

			@endif
			
		</ul>
	</div>
</li>
@endif
