<label>Группа товаров
	@if (isset($goods_products_list))
	{{ Form::select('goods_product_id', $goods_products_list, null, ['id' => 'goods_products-list']) }}
	@else
	<select name="goods_product_id" id="goods_products-list" required disabled></select>
	@endif
</label>
<a id="mode-add" class="modes">Добавить группу продуктов</a>
{{ Form::hidden('mode', 'mode_select') }}