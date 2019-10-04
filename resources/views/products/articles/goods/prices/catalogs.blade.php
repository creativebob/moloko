@if ($catalogs_goods->isNotEmpty())
<div class="grid-x grid-padding-x" id="form-prices_goods">


	{{-- <div class="cell small-12">

				<div class="grid-x grid-margin-x" id="price-units-block">
					<div class="small-12 medium-3 cell">
						@include('includes.selects.units_categories', [
						'default' => 6, 
						'type' => 'article',
						'name' => 'price-units_category_id',
						'id' => 'select-price-units_categories',
						])
					</div>
					<div class="small-12 medium-3 cell">
						@include('includes.selects.units', [
						'default' => 32,
						'units_category_id' => 6,
						'name' => 'price-unit_id',
						'id' => 'select-price-units',
						])
					</div>
				</div>

				<script>

				    // При смене категории единиц измерения меняем список единиц измерения
				    $(document).on('change', '#select-price-units_categories', function() {
				        $.post('/admin/get_units_list', {
				            units_category_id: $(this).val()
				        }, function(html) {
				            $('#select-price-units').html(html);
				        });
				    });

				</script>
		<hr>
	</div> --}}

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
		</div>
		<div class="grid-x grid-padding-x">
			<div class="medium-3 cell">
				<button class="button" id="button-store-prices_goods">Добавить</button>
			</div>
		</div>
	</div>
</div>

@endif
