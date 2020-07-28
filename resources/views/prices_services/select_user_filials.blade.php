{!! Form::select('filial_id', is_null($filials) ? [null => 'Нет филиала']: $filials, $filial_id, ['id' => 'select-filials']) !!}
