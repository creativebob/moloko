<label>Величина
	{{ Form::select($name ?? 'units_category_id', $units_categories->pluck('name', 'id'), $default ?? null, [
		'id' => $id ?? 'select-units_categories',
		'required',
		(isset($disabled)) ? 'disabled' : '',
	]
	) }}
</label>