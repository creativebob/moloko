<label>Единица измерения
	{{ Form::select('unit_id', $units->pluck('name', 'id'), isset($default) ? $default : null, ['id' => 'units-list', 'required'], isset($units_attributes) ? $units_attributes : []) }}
</label>