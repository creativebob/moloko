{!! Form::select($name . '_id', $contragents->pluck('name', 'id'), ($value ?? null), ['id' => 'select-contragents']) !!}

