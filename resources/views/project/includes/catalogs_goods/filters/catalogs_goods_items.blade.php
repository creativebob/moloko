<div class="grid-x">
	<div class="cell small-12">

		<label><h4>Пункты</h4>
			{!! Form::select('catalogs_goods_item', $catalogs_goods_items->pluck('name', 'id'), $catalog->id) !!}
		</label>
</div>