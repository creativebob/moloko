@can('index', App\Raw::class)
<div class="tabs-panel" id="tab-raws">
	@include('products.articles_categories.goods_categories.raws.raws', ['category' => $category])
</div>
@endcan

