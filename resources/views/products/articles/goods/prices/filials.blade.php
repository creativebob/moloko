@if ($filials->isNotEmpty())
{!! Form::select('filial_id', $filials->pluck('name', 'id'), null, ['id' => 'select-filials']) !!}
@else
{!! Form::select('filial_id', $filials, null, ['id' => 'select-filials', 'placeholder' => 'Нет филиала']) !!}
@endif

