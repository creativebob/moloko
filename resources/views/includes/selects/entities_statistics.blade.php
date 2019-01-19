{!! Form::select('entity_id', $entities->pluck('name', 'id'), ($entity_id ?? null), [
	(isset($disabled)) ? 'disabled' : '',
]
) !!}