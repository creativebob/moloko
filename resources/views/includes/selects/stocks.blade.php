{!! Form::select('stock_id', $stocks->pluck('name', 'id'), isset($stock_id) ? $stock_id : null, [
    'id' => 'select-stocks',
    isset($disabled) ? 'disabled' : '',
    'placeholder' => isset($placeholder) ? $placeholder : null,
    ]) !!}

