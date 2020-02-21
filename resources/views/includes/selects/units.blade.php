<label>{{ $label_title ?? 'Ед. измерения' }}

	{{ Form::select($name ?? 'unit_id', $units->pluck('name', 'id'), $data ?? $default ?? $units->first()->category->unit_id, [
		'id' => $id ?? 'select-units',
		(isset($required)) ? 'required' : '',
		{{-- (isset($disabled)) ? 'disabled' : '', --}}
	], $units_attributes ?? []
	) }}
</label>
