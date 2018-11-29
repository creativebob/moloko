@if(count($raws_modes) == 1)
<input type="hidden" name="raws_mode_id" value="{{ $raws_modes->first()->id }}">
@else
<label>Тип
	{!! Form::select('raws_mode_id', $raws_modes->pluck('name', 'id')) !!}
</label>
@endif

