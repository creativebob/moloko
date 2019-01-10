{!! Form::select('direction_id', $directions->pluck('category.name', 'id'), null, ['id' => 'select-directions', 'placeholder' => 'Выберите направление']) !!}

