@if (($goods_cat['goods_products_count'] > 0) || isset($goods_cat['children']))
<li>
	<span class="parent" data-open="goods_cat-{{ $goods_cat['id'] }}">{{ $goods_cat['name'] }}</span>
	<div class="checker-nested" id="goods_cat-{{ $goods_cat['id'] }}">
		<ul class="checker">
			@if ($goods_cat['goods_products_count'] > 0)

			@foreach ($goods_cat['goods_products'] as $goods_product)
			@include('goods_categories.compositions.goods-product', $goods_product)
			@endforeach

			@endif
			@if (isset($goods_cat['children']))

			@foreach ($goods_cat['children'] as $goods_cat)
			@include('goods_categories.compositions.goods-category', $goods_cat)
			@endforeach

			@endif
			
		</ul>
	</div>
</li>
@endif
