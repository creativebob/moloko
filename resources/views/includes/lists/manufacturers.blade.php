<ul>
	@foreach ($manufacturers as $manufacturer)
	<li class="checkbox">
		{{ Form::checkbox('manufacturers[]', $manufacturer->id, null, ['id'=>'manufacturer-'.$manufacturer->id, 'class'=>'manufacturer-checkbox']) }}
		<label for="manufacturer-{{ $manufacturer->id }}"><span>{{ $manufacturer->name }}</span></label>
	</li>
	@endforeach
</ul>