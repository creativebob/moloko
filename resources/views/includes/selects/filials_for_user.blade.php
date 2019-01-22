{{-- Список филиалов --}}
<label>Филиал:
	{{ Form::select('filial_id', $filial_list, $value, ['id'=>'select-filials']) }}
</label>