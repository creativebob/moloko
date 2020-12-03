{{-- Признак агента --}}
<label>Признак агента

	{{ Form::select('agent_type_id', $agent_types_list, $value) }}

</label>