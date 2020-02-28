{!! Form::select($name ?? 'unit_id', $units->pluck('name', 'id'), $value ?? 12, ['id' => 'select-units', isset($disabled) ? 'disabled' : '']) !!}
