{!! Form::select('mailing_id', $mailings->pluck('name', 'id'), null, [
    'id' => 'select-mailings',
    (isset($disabled) ? 'disabled' : ''),
    'placeholder' => isset($placeholder) ? $placeholder: null
]) !!}

