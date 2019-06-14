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
			{!! Form::select('filial_id', $filials->pluck('name', 'id'), null, ['id' => 'select-filials']) !!}
		</label>
	</div>
	<div class="medium-3 cell">
		<label>Цена
			{!! Form::number('price', null, null, []) !!}
		</label>
	</div>
	<div class="medium-3 cell">
		<a class="button" id="button-store-prices_service">Добавить</a>
	</div>
</div>

@endif
