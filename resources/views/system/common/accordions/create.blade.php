<div class="reveal" id="modal-create" data-reveal data-close-on-click="false">
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>{{ $title }}</h5>
		</div>
	</div>

	{{ Form::open(['id' => 'form-create', 'data-abide', 'novalidate']) }}

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
			{{ Form::submit('Добавить', ['class'=>'button modal-button submit-create']) }}
		</div>
	</div>

{{--	@include('system.common.accordions.form', ['submit_text' => 'Добавить', 'class' => 'submit-create'])--}}

	{{ Form::close() }}

	<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@push('scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check')
@endpush


