
<div class="grid-x grid-padding-x modal-content inputs">
	<div class="small-10 small-offset-1 cell">

		@isset($parent_id)
		<label>Расположение
			@include('includes.selects.goods_categories', ['parent_id' => $parent_id])
		</label>
		@endisset

		<label>Название категории
			@include('includes.inputs.name', ['name' => 'name', 'required' => 'required', 'check' => 'check-field'])
			<div class="item-error">Такая категория уже существует!</div>
		</label>

		@include('includes.selects.goods_modes')

		{{ Form::hidden('id', null, ['id' => 'item-id']) }}
		{{ Form::hidden('category_id', null, ['id' => 'category-id']) }}

		@include('includes.control.checkboxes', $item)

	</div>
</div>

<div class="grid-x align-center">
	<div class="small-6 medium-4 cell">
		{{ Form::submit('Добавить категорию', ['data-close', 'class'=>'button modal-button submit-create']) }}
	</div>
</div>


<script type="text/javascript">
	$.getScript("/crm/js/jquery.maskedinput.js");
	$.getScript("/crm/js/inputs_mask.js");
</script>



