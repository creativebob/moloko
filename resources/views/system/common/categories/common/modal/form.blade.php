<div class="grid-x grid-padding-x modal-content inputs">
		<div class="small-10 small-offset-1 cell">

			@isset($parent_id)
				<label>Расположение
					@include('includes.selects.categories_select', ['id' => $item->id, 'parent_id' => $parent_id])
				</label>
			@endisset

			<label>Название
				@include('includes.inputs.name', ['required' => true, 'check' => true])
				<div class="item-error">Такое название уже существует!</div>
			</label>

			{{-- @includeIf($page_info->entity->view_path . '.form') --}}

			{{ Form::hidden('id', null, ['id' => 'item-id']) }}

			{{--        @include('includes.control.checkboxes')--}}
		</div>
	</div>

	<div class="grid-x align-center">
		<div class="small-6 medium-4 cell">
			<button type="button" id="add-category-button" class="button modal-button {{ $class }}">{{ $submit_text }}</button>
			{{-- Form::submit('Добавить', ['class'=>'button modal-button submit-create']) --}}
		</div>
	</div>

{{--	@include('system.common.categories.form', ['submit_text' => 'Добавить', 'class' => 'submit-create'])--}}
