<ul>
	@foreach ($filters as $filter)
	<li class="checkbox">
		{{ Form::checkbox('filters[]', $filter->id, null, ['id' => 'filter-' . $filter->id, 'class' => 'filter-checkbox']) }}
		<label for="filter-{{ $filter->id }}"><span>{{ $filter->name }}</span></label>
	</li>
	@endforeach
</ul>
