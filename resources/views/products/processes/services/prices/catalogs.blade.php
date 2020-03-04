@if ($catalogs_services->isNotEmpty())
<div class="grid-x grid-padding-x" id="form-prices_service">

	{!! Form::hidden('service_id', $item->id, null, []) !!}

	<div class="medium-3 cell">
		<label>Каталог
			{!! Form::select('catalogs_service_id', $catalogs_services->pluck('name', 'id'), null, ['id' => 'select-catalogs']) !!}
		</label>
	</div>
	<div class="medium-3 cell">
		<label>Пункты каталога
			@include ('products.processes.services.prices.catalogs_items', ['catalog_id' => $catalogs_services->first()->id])
		</label>
	</div>
	<div class="medium-3 cell">
		<label>Филиал
            @include ('products.processes.services.prices.filials', ['catalog_id' => $catalogs_services->first()->id])
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
	<div class="medium-3 cell">
		<button class="button" id="button-store-prices_service">Добавить</button>
	</div>
</div>

@endif
