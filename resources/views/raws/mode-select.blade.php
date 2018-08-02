<div class=" up-input-button">
<a id="mode-default" class="modes">Вернуться</a>
</div>
<label>Группа сырья
	@if (isset($raws_products_list))
	{{ Form::select('raws_product_id', $raws_products_list, null, ['id' => 'goods-products-list']) }}
	@endif
</label>

<label>Название сырья
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
	<div class="item-error">Такая услуга уже существует!</div>
</label>
{{ Form::hidden('mode', 'mode-select') }}