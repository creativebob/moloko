{{-- Список уровней лояльности --}}
<label>Уровень лояльности

	{{ Form::select('loyalty_id', $loyalties_list, $value) }}

</label>