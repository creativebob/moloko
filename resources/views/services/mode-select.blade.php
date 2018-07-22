
<div class="small-6 cell relative">
	<a id="mode-add" class="modes up-input-button">+ Добавить группу</a>
	<label>Группа услуг
		@if (isset($services_products_list))
		{{ Form::select('services_product_id', $services_products_list, null, ['id' => 'services-products-list']) }}
		@else
		<select name="product_id" id="services-products-list" required disabled></select>
		@endif
	</label>
</div>

<div class="small-12 cell">
	<label>Название услуги
		@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
		<div class="item-error">Такая услуга уже существует!</div>
	</label>
</div>
{{ Form::hidden('mode', 'mode_select') }}