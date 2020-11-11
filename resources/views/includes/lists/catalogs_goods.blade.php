<ul>
	@foreach ($catalogsGoods as $catalogGoods)
	<li class="checkbox">
		{!! Form::checkbox('catalogs_goods[]', $catalogGoods->id, null, ['id' => 'checkbox-catalog_goods-'.$catalogGoods->id]) !!}
		<label for="checkbox-catalog_goods-{{ $catalogGoods->id }}">
            <span>{{ $catalogGoods->name }}</span>
        </label>
	</li>
	@endforeach
</ul>
