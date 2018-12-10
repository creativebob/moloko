@if($raws_modes->count() == 1)
{!! Form::hidden('raws_mode_id', $raws_modes->first()->id, []) !!}
@else
<label>Тип
	{!! Form::select('raws_mode_id', $raws_modes->pluck('name', 'id')) !!}
</label>
@endif

