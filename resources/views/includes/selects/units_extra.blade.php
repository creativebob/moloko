<label>Единица измерения
	{{ Form::select('extra_unit_id', $units->pluck('name', 'id'), isset($default) ? $default : null, [
		'id' => 'select-extra_units',
		'required',
		(isset($disabled)) ? 'disabled' : '',
	], isset($units_attributes) ? $units_attributes : []
	) }}
</label>
