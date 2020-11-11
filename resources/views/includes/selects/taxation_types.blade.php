{!! Form::select('taxation_type_id', $taxationTypes->pluck('name', 'id'), null, [
'id' => 'select-taxation_types',
'placeholder' => isset($placeholder) ? $placeholder: null
]) !!}
