
@if($booklist_types->count() > 1)
	{{ Form::select('booklist_type_id', $booklist_types->pluck('name', 'id'), isset($default) ? $default : null, ['id' => 'booklist_type_id', 'class' => 'select-in-booklist', 'required']) }}
@else

	{!! Form::hidden('booklist_type_id', 1, ['id' => 'booklist_type_id']) !!}

@endif