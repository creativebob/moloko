{!! Form::select('tools_type_id', $toolsTypes->pluck('name', 'id'), ($value ?? null), [
    'placeholder' => isset($placeholder) ? 'Тип не выбран' : null
]) !!}
