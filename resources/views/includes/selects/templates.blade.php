{!! Form::select('template_id', $templates->pluck('name', 'id'), null, [
    'id' => 'select-templates',
    (isset($disabled) ? 'disabled' : ''),
    'placeholder' => isset($placeholder) ? $placeholder : null,
]) !!}

