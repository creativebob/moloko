{!! Form::select('page_id', $pages->pluck('name', 'id'), null, [
    'placeholder' => isset($placeholder) ? 'Не выбрана' : null,
    'id' => 'select-pages'
]
) !!}

