@php
	if(!isset($name)){$name = 'units_category_id';};
	if(!isset($id)){$id = 'select-units_categories';};
	if(!isset($data)){$data = isset($default) ? $default : null;};
@endphp

<label>Величина
	{{ Form::select($name, $units_categories->pluck('name', 'id'), $data, [
		'id' => $id,
		'required',
		(isset($disabled)) ? 'disabled' : '',
	]
	) }}
</label>