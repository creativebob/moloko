<div class="grid-x">
	<div class="cell small-12">
		<label><h4>Категории:</h4>
			{!! Form::select('catalogs_goods_item', $catalogs_goods_items, request()->catalogs_goods_item ?? null, ['placeholder' => 'Все категории', 'class'=>'catalogs_goods_item_filter']) !!}
		</label>
	</div>
</div>