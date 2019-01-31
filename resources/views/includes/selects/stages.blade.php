{{-- Этапы процесса --}}

<label>Этап
	{{ Form::select('stage_id', $stages_list, $value) }}
</label>