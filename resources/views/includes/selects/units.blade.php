<label>Единица измерения
	{{ Form::select('unit_id', $units->pluck('name', 'id'), isset($default) ? $default : null, ['id' => 'units-list', 'required']) }}

	<select name="unit_id" id="units-list" required>
		@include('goods.units_list', $units)
	</select>

</label>