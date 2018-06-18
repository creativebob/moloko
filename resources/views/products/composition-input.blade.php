
<label>
	<span data-tooltip tabindex="1" title="{{ $composition->description }}">{{ $composition->name }}</span> ({{ $composition->unit->abbreviation }})
	{{ Form::number('compositions-'.$composition->id) }}
</label>

