<label>Категория единиц измерения
	{{ Form::select('units_category_id', $units_categories->pluck('name', 'id'), isset($default) ? $default : null, ['id' => 'select-units_categories', 'required']) }}
</label>