{{-- Этапы процесса --}}

<label>Этап
	{{ Form::select('stage_id', $stages_list, $value, ['required']) }}
	<span class="form-error">
		Отсутствует этап. 

		@can ('index', App\Stage::class)
			{{ link_to_route('stages.index', 'Настроить', $value = Null) }}

		@else
			<span>Обратитесь к администратору.</span>

		@endcan
	</span>



</label>