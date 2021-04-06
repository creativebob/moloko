{{-- Список правовых форм --}}
<label>Форма

	{{ Form::select('legal_form_id', $legal_forms_list, $value ?? null, ['placeholder' => 'Не указано']) }}

</label>