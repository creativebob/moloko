{!! Form::select('page_id', $pages->pluck('name', 'id'), null, ['placeholder' => 'Не выбрана', 'id' => 'select-pages']) !!}

