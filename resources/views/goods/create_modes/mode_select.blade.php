<div class="small-12 cell up-input-button text-center">
	<a id="mode-default" class="modes">Вернуться</a>
</div>

@if (count($goods_products))
<label>Группа товаров
	<select name="goods_product_id" id="goods-products-list" required>
		@foreach ($goods_products as $goods_product)
		<option value="{{ $goods_product->id }}" data-abbreviation="{{ $goods_product->unit->abbreviation }}">{{ $goods_product->name }}</option>
		@endforeach
	</select>
	{{-- Form::select('goods_product_id', $goods_products_list, null, ['id' => 'goods-products-list']) --}}
</label>
@else
В данной категории нет групп, выберите другую категорию или <a id="mode-default" class="modes">вернитесь назад</a>
@endif

{{ Form::hidden('mode', 'mode-select', ['id' => 'mode']) }}