<label>Группа услуг
	@if (isset($services_products_list))
	{{ Form::select('services_product_id', $services_products_list, null, ['id' => 'services-products-list']) }}
	@else
	<select name="product_id" id="services-products-list" required disabled></select>
	@endif
</label>
<label>Название услуги
  @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
  <div class="item-error">Такой товар уже существует!</div>
</label>
<a id="mode-add" class="modes">Добавить группу услуг</a>
{{ Form::hidden('mode', 'mode_select') }}