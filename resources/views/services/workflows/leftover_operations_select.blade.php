{!! Form::select('compositions['.$composition->id.'][leftover_operation_id]', $leftover_operations->pluck('name', 'id'), $selected, ['class' => 'compact', !empty($disabled) ? 'disabled' : '']) !!}