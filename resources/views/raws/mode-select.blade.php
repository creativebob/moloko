<div class=" up-input-button">
<a id="mode-default" class="modes">Вернуться</a>
</div>
<label>Группа услуг
	@if (isset($goods_products_list))
	{{ Form::select('goods_product_id', $goods_products_list, null, ['id' => 'goods-products-list']) }}
	@endif
</label>

<label>Название услуги
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
	<div class="item-error">Такая услуга уже существует!</div>
</label>
{{ Form::hidden('mode', 'mode-select') }}