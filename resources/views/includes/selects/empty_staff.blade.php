{{ Form::select('staffer_id', $staff->pluck('position.name', 'id'), null, ['placeholder' => 'Выберите должность']) }}
