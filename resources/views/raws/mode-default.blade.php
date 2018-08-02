<div class=" up-input-button">
	@if ($raws_products_count > 0)
	<a id="mode-select" class="modes">Добавить в группу</a> <span>|</span>
	@endif
	<a id="mode-add" class="modes">Создать группу</a>
</div>
<label>Название сырья
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
	<div class="item-error">Такое сырье уже существует!</div>
</label>
{{ Form::hidden('mode', 'mode-default') }}

