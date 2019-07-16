@php
	if(!isset($label_title)){$label_title = 'Ед. измерения';};
	if(!isset($name)){$name = 'unit_id';};
	if(!isset($id)){$id = 'select-units';};
	if(!isset($data)){$data = isset($default) ? $default : $units->first()->category->unit_id;};
@endphp

<label>{{ $label_title }}
	{{ Form::select($name, $units->pluck('name', 'id'), $data, [
		'id' => $id,
		'required',
		(isset($disabled)) ? 'disabled' : '',
	], isset($units_attributes) ? $units_attributes : []
	) }}
</label>
