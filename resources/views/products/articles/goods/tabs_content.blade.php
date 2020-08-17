{{-- Каталоги --}}
<div class="tabs-panel" id="tab-prices">
    <goods-store-component></goods-store-component>

	<div class="grid-x grid-padding-x tabs-margin-top">
		<div class="cell small-12">
            @include('products.articles.goods.prices.prices')
		</div>
	</div>
</div>

{{--Состав--}}
@if($article->kit)
	<div class="tabs-panel" id="tab-goods">
		@include('products.articles.goods.goods.goods')
	</div>
@else
    <div class="tabs-panel" id="tab-raws">
        @include('products.articles.goods.raws.raws')
    </div>
@endif

@can('index', App\Container::class)
    <div class="tabs-panel" id="tab-containers">
        @include('products.articles.goods.containers.containers')
    </div>
@endcan

@can('index', App\Attachment::class)
    <div class="tabs-panel" id="tab-attachments">
        @include('products.articles.goods.attachments.attachments')
    </div>
@endcan

@can('index', App\Goods::class)
    <div class="tabs-panel" id="tab-related">
        @include('products.articles.goods.related.related', ['id' => $item->id])
    </div>
@endcan

