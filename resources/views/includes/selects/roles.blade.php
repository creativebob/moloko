{{ Form::select('role_id', $roles->pluck('name', 'id'), null, ['id' => 'select-roles']) }}
