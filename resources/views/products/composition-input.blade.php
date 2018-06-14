
<label>{{ $composition->name . ' (' . $composition->unit->abbreviation . ')' }}
	{{ Form::number('compositions-'.$composition->id) }}
</label>

