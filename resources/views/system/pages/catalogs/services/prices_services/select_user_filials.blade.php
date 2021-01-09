@if($catalog->filials->isNotEmpty())
    @if(count($filials) > 1)
        {!! Form::select('filial_id', is_null($filials) ? [null => 'Нет филиала']: $filials, $filial_id, ['id' => 'select-filials'])  !!}
    @else
        {!! Form::hidden('filial_id', array_key_first($filials)) !!}
    @endif
@endif
