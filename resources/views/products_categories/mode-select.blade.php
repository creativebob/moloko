<label>Группа товаров
	@if (isset($products_list))
	{{ Form::select('product_id', $products_list, null, ['id' => 'products-list']) }}
	@else
	<select name="product_id" id="products-list" required disabled></select>
	@endif
</label>
<a id="mode-add" class="modes">Добавить группу продуктов</a>
{{ Form::hidden('mode', 'mode_select') }}