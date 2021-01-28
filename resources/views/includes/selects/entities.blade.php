{!! Form::select('entity_id', $entities->pluck('name', 'id'), ($entityId ?? null), [

]
) !!}
{{--(isset($disabled)) ? 'disabled' : '',--}}
