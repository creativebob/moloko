{!! Form::select('parent_id', $sectors->pluck('name', 'id')) !!}
{{--
<select name="sector_id">
	{!! $sectors_list !!}
</select> --}}