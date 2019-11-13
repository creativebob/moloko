{!! Form::select('stock_id', $stocks->pluck('name', 'id'), $stock_id, ['id' => 'select-stocks', isset($disabled) ? 'disabled' : '']) !!}

