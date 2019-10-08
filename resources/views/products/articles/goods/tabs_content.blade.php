{{-- Каталоги --}}
<div class="tabs-panel" id="catalogs">
	<div class="grid-x grid-padding-x">

		<div class="small-12 medium-7 cell">
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

		<div class="small-12 medium-5 cell cmv-indicators">
			<div class="grid-x grid-padding-x">
				<fieldset class="small-12 cell goods-indicators">
					<legend> Расчет по товару:</legend>
					<div class="grid-x grid-padding-x">
						<div class="small-12 cell indicators-item">
							<span>Общая себестоимость: </span><span id="total_goods_cost" class="indicators_total">0</span><span> руб.</span>
						</div>
						<div class="small-12 cell indicators-item">
							<span>Общий вес: </span><span id="total_goods_weight" class="indicators_total">0</span><span> гр.</span>
						</div>
						{{-- <div class="small-12 cell indicators-item">
							<span>Желаемая наценка, %: </span>
							<input-digit-component name="margin" v-on:countchanged="changePrice" class="goods-margin-calc"></input-digit-component>
						</div> --}}
					</div>
				</fieldset> 
			</div>
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

<div class="tabs-panel" id="containers">
	@include('products.articles.goods.containers.containers')
</div>

<div class="tabs-panel" id="attachments">
	@include('products.articles.goods.attachments.attachments')
</div>

