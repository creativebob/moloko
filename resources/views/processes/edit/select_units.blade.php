{!! Form::select('unit_id', $units->pluck('name', 'id'), 12, ['id' => 'select-units', isset($disabled) ? 'disabled' : '']) !!}
