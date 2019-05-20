<label>Единица измерения
	{{ Form::select('unit_id', $units->pluck('name', 'id'), isset($default) ? $default : $units->first()->units_category->unit_id, [
		'id' => 'select-units',
		'required',
		(isset($disabled)) ? 'disabled' : '',
	], isset($units_attributes) ? $units_attributes : []
	) }}
</label>
