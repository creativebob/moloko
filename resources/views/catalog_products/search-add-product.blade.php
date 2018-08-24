	
	@if((!empty($result_search_goods))||(!empty($result_search_services))||(!empty($result_search_raws)))
	<div class="small-12 medium-6 cell" id="search-add_product-result-wrap">
<!-- 		<h3 class="search-result-head">Вот, что удалось найти:</h3> -->
		<ul class="search-result-list">
			@foreach($result_search_goods as $item)
			<li>
				<a class="add-product-button" id="goods-{{ $item->id }}">{{ $item->name }}</a>
			</li>
			@endforeach
			@foreach($result_search_services as $item)
			<li>
				<a class="add-product-button" id="services-{{ $item->id }}">{{ $item->name }}</a>
			</li>
			@endforeach
			@foreach($result_search_raws as $item)
			<li>
				<a class="add-product-button" id="raws-{{ $item->id }}">{{ $item->name }}</a>
			</li>
			@endforeach
		</ul>
	</div>
	@endif
