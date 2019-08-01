{{-- Каталоги --}}
<div class="tabs-panel" id="catalogs">
	<div class="grid-x grid-padding-x">

		<div class="small-12 medium-6 cell">
			@include('products.articles.goods.prices.catalogs')

			<table class="table-compositions">
				<thead>
				<tr>
					<th>Каталог:</th>
					<th>Пункт:</th>
					<th>Филиал:</th>
					<th>Цена:</th>
					<th></th>
				</tr>
				</thead>
				<tbody id="table-prices">

				@if ($item->prices->isNotEmpty())
					@foreach ($item->prices as $price)
						@include('products.articles.goods.prices.price', ['cur_price_goods' => $price])
					@endforeach
				@endif

				</tbody>
			</table>

		</div>
	</div>
</div>

{{--Состав--}}
@if($article->kit)
	<div class="tabs-panel" id="goods">
		@include('products.articles.goods.goods.goods')
	</div>
	@else

<div class="tabs-panel" id="raws">
@include('products.articles.goods.raws.raws')
</div>
	@endif

