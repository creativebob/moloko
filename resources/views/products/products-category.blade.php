<li>
	<span class="parent" data-open="products_category-{{ $products_category->id }}">{{ $products_category->name }}</span>
	@if ($products_category->products_count > 0)
	<div class="checker-nested" id="products_category-{{ $products_category->id }}">
		<ul  class="checker">
			@foreach ($products_category->products as $composition)
                  @include('products.compositions', $composition)
                @endforeach
			{{-- @each('products.metrics', $products_category->metrics, 'metric') --}}
		</ul>
	</div>
	@endif
</li>