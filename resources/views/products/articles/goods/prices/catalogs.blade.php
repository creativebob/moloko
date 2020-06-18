@if ($catalogs_goods->isNotEmpty())
<div class="grid-x grid-padding-x" id="form-prices_goods">

	<div class="small-12 cell">
		{!! Form::hidden('goods_id', $item->id, null, []) !!}
		<div class="grid-x grid-padding-x">
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

            @php
                $currencies = auth()->user()->company->currencies;
            @endphp

            @if($currencies->isNotEmpty())
                @if($currencies->count() > 1)
                    <div class="medium-3 cell">
                        <label>Валюта
                            {!! Form::select('currency_id', $currencies->pluck('name', 'id'), $currencies->first()->id, ['required']) !!}
                        </label>
                        <span class="form-error">Введите цену!</span>
                    </div>
                @else
                    {!! Form::hidden('currency_id', $currencies->first()->id) !!}
                @endif
            @else
                {!! Form::hidden('currency_id', 1) !!}
            @endif

		</div>
		<div class="grid-x grid-padding-x">
			<div class="medium-3 cell">
				<button class="button" id="button-store-prices_goods">Добавить</button>
			</div>
		</div>
	</div>
</div>

@endif
