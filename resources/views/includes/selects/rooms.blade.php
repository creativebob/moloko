{!! Form::select('room_id', $rooms->pluck('article.name', 'id'), null, ['id' => 'select-rooms']) !!}

