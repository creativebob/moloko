<div class="small-12 cell up-input-button text-center">
	@if ($goods_products_count > 0)
	<a id="mode-select" class="modes">Добавить в группу</a> <span>|</span>
	@endif
	<a id="mode-add" class="modes">Создать группу</a>
</div>

{{ Form::hidden('mode', 'mode-default', ['id' => 'mode']) }}

