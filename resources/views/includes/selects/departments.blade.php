{{-- Список отделов --}}
<label>Отдел:
	{{ Form::select('department_id', $departments_list, $value, ['id'=>'select-departments']) }}
</label>