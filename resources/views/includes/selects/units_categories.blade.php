<label>Величина
	{{ Form::select($name ?? 'units_category_id', $units_categories->pluck('name', 'id'), $data ?? $default ?? 2, [
		'id' => $id ?? 'select-units_categories',
		'required'
	]
	) }}
</label>
