{!! Form::select('unit_id', $units->pluck('name', 'id'), isset($process->unit_id) ? $process->unit_id : $process->group->unit_id, ['id' => 'select-units', isset($disabled) ? 'disabled' : '']) !!}
