	
	@if(
		(!empty($result_search_goods))||
		(!empty($result_search_services))||
		(!empty($result_search_raws))
	)
	<div class="small-12 medium-6 cell" id="search-add_product-result-wrap">
<!-- 		<h3 class="search-result-head">Вот, что удалось найти:</h3> -->

		<ul class="search-result-list">
			@if(!empty($result_search_goods))
				@foreach($result_search_goods as $item_goods)
				<li>
					<a class="add-product-button" id="goods-{{ $item_goods->id }}">{{ $item_goods->goods_article->name }}</a>
				</li>
				@endforeach
			@endif

			@if(!empty($result_search_services))
				@foreach($result_search_services as $item_services)
				<li>
					<a class="add-product-button" id="services-{{ $item_services->id }}">{{ $item_services->services_article->name }}</a>
				</li>
				@endforeach
			@endif

			@if(!empty($result_search_raws))
				@foreach($result_search_raws as $item_raws)
				<li>
					<a class="add-product-button" id="raws-{{ $item_raws->id }}">{{ $item_raws->raws_article->name }}</a>
				</li>
				@endforeach
			@endif
		</ul>
	</div>
	@endif
