{{-- Роли --}}
<label>Роли:
	{{ Form::select('role_id', $roles_list, null, ['id'=>'select-roles']) }}
</label>
