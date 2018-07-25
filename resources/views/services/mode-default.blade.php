<div class=" up-input-button">
	@if ($services_products_count > 0)
	<a id="mode-select" class="modes">Добавить в группу</a> |
	@endif
	<a id="mode-add" class="modes">Создать группу</a>
</div>
<label>Название услуги
	@include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
	<div class="item-error">Такая услуга уже существует!</div>
</label>
{{ Form::hidden('mode', 'mode-default') }}

