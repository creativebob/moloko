{{-- Модалка добавления роли --}}
<div class="reveal" id="role-add" data-reveal>
	<div class="grid-x">
		<div class="small-12 cell modal-title">
			<h5>ДОБАВЛЕНИЕ Роли</h5>
		</div>
	</div>

	{{ Form::open(['id' => 'form-role-add']) }}
	<div class="grid-x grid-padding-x modal-content inputs">
		<div class="small-10 medium-6 cell">
			<label>Роли:
				@include('includes.selects.roles')
			</label>
		</div>

		{{-- <div class="small-10 medium-6 cell">
			<label>
				@include('includes.selects.filials_for_user', ['value'=>$user->filial_id])
			</label>
		</div> --}}

		<div class="small-12 cell">
			<label>
				@include('includes.selects.departments_for_user', ['value'=>$user->filial_id])
			</label>
		</div>

		<input type="hidden" name="user_id" id="user-id" value="{{ $user->id }}">

	</div>
	<div class="grid-x align-center">
		<div class="small-6 medium-4 cell">
			{{ Form::submit('Добавить роль', ['data-close', 'class'=>'button modal-button', 'id'=>'submit-role-add']) }}
		</div>
	</div>

	{{ Form::close() }}
	<div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

{{-- Конец модалки добавления роли --}}
