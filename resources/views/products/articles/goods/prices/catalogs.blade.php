@if ($catalogs_goods->isNotEmpty())
<div class="grid-x grid-padding-x" id="form-prices_goods">

	{!! Form::hidden('goods_id', $item->id, null, []) !!}

	<div class="medium-3 cell">
		<label>Каталог
			{!! Form::select('catalogs_goods_id', $catalogs_goods->pluck('name', 'id'), null, ['id' => 'select-catalogs']) !!}
		</label>
	</div>
	<div class="medium-3 cell">
		<label>Пункты каталога
			@include ('products.articles.goods.prices.catalogs_items', ['catalog_id' => $catalogs_goods->first()->id])
		</label>
	</div>
	<div class="medium-3 cell">
		<label>Филиал
            @include ('products.articles.goods.prices.filials', ['catalog_id' => $catalogs_goods->first()->id])
			{{-- {!! Form::select('filial_id', $filials->pluck('name', 'id'), null, ['id' => 'select-filials']) !!} --}}
		</label>
	</div>
	<div class="medium-3 cell">
		<label>Цена
			{!! Form::number('price', null, null, ['required']) !!}
		</label>
		<span class="form-error">Введите цену!</span>
	</div>
	<div class="medium-3 cell">
		<button class="button" id="button-store-prices_goods">Добавить</button>
	</div>
</div>

@endif
