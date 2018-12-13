<ul>
	@foreach ($departments as $department)
	<li class="checkbox">
		{{ Form::checkbox('departments[]', $department->id, null, ['id'=>'department-'.$department->id, 'class'=>'department-checkbox']) }}
		<label for="department-{{ $department->id }}"><span>{{ $department->name }}</span></label>
	</li>
	@endforeach
</ul>