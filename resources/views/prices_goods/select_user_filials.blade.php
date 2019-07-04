{!! Form::select('filial_id', is_null($user_filials) ? [null => 'Нет филиала']: $user_filials, $filial_id, ['id' => 'select-user_filials']) !!}
