{!! Form::select('unit_id', $units->pluck('name', 'id'), 8, ['id' => 'select-units', isset($disabled) ? 'disabled' : '']) !!}
