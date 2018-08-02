<div class=" up-input-button">
	@if ($goods_products_count > 0)
	<a id="mode-select" class="modes">Добавить в группу</a> <span>|</span>
	@endif
	<a id="mode-add" class="modes">Создать группу</a>
</div>
<label>Название товара
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
	<div class="item-error">Такой товар уже существует!</div>
</label>
<div class="checkbox">
	{{ Form::checkbox('status', 'set', null, ['id' => 'status']) }}
	<label for="status"><span>Набор</span></label>
</div>
{{ Form::hidden('mode', 'mode-default') }}

