<div class=" up-input-button">
<a id="mode-default" class="modes">Вернуться</a>
</div>
<label>Группа услуг
	@if (isset($services_products_list))
	{{ Form::select('services_product_id', $services_products_list, null, ['id' => 'services-products-list']) }}
	@endif
</label>

<label>Название услуги
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required' => true])
	<div class="item-error">Такая услуга уже существует!</div>
</label>
{{ Form::hidden('mode', 'mode-select') }}