
@php
	if(!isset($disabled)){$disabled = false;}
@endphp

<label>Временной период
	{{ Form::select('period_id', $periods->pluck('name', 'id'), isset($period_id) ? $period_id : $default, ['id' => 'select-periods', 'required', $disabled == true ? 'disabled' : '']) }}
</label>