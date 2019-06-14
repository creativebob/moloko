{{-- Список филиалов --}}
<label>Филиал под которым создан пользователь:
	{{ Form::select('filial_id', $filial_list, $value, ['id'=>'select-filials']) }}
</label>