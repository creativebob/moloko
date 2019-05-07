<label>Группа товаров
	@if (isset($services_products_list))
	{{ Form::select('services_product_id', $services_products_list, null, ['id' => 'services_products-list']) }}
	@else
	<select name="services_product_id" id="services_products-list" required disabled></select>
	@endif
</label>
<a id="mode-add" class="modes">Добавить группу продуктов</a>
{{ Form::hidden('mode', 'mode_select') }}