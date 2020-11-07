{{-- Список филиалов --}}
<label>Филиал под которым создан пользователь:
	{{ Form::select('filial_id', $filial_list, isset($value) ? $value : null, ['id' => 'select-filials', (isset($disabled) ? 'disabled' : '')]) }}
</label>
